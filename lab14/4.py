import time
from abc import ABC, abstractmethod


# ================== BASE SERVICE ==================

class OrderServiceInterface(ABC):
    @abstractmethod
    def create_order(self, order_id):
        pass


class OrderService(OrderServiceInterface):
    def create_order(self, order_id):
        print("Замовлення", order_id, "створено")


# ================== DECORATOR BASE ==================

class ServiceDecorator(OrderServiceInterface):
    def __init__(self, service):
        self.service = service

    def create_order(self, order_id):
        self.service.create_order(order_id)


# ================== CONCRETE DECORATORS ==================

class LoggingDecorator(ServiceDecorator):
    def create_order(self, order_id):
        print("LOG: початок створення замовлення")
        self.service.create_order(order_id)
        print("LOG: замовлення успішно створено")


class TimeDecorator(ServiceDecorator):
    def create_order(self, order_id):
        start = time.time()
        self.service.create_order(order_id)
        end = time.time()
        print("Час виконання:", round(end - start, 4), "сек")


class AccessDecorator(ServiceDecorator):
    def __init__(self, service, has_access):
        super().__init__(service)
        self.has_access = has_access

    def create_order(self, order_id):
        if self.has_access:
            self.service.create_order(order_id)
        else:
            print("ДОСТУП ЗАБОРОНЕНО")


# ================== ПРОГРАМА ==================

# базовий сервіс
service = OrderService()

# обгортання декораторами (КОМБІНУВАННЯ)
service = LoggingDecorator(service)
service = TimeDecorator(service)
service = AccessDecorator(service, has_access=True)

# виклик методу
service.create_order("ORD-101")