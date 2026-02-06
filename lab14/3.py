from abc import ABC, abstractmethod


# ================== OBSERVER ==================

class Observer(ABC):
    @abstractmethod
    def update(self, order):
        pass


class LoggerObserver(Observer):
    def update(self, order):
        print("LOG: створено замовлення з ID", order["id"], "на суму", order["amount"])


class NotificationObserver(Observer):
    def update(self, order):
        print("ПОВІДОМЛЕННЯ: замовлення", order["id"], "успішно створено")


class StatisticsObserver(Observer):
    def __init__(self):
        self.count = 0
        self.total = 0

    def update(self, order):
        self.count += 1
        self.total += order["amount"]
        print("СТАТИСТИКА: кількість =", self.count, "| загальна сума =", self.total)


# ================== SUBJECT ==================

class OrderService:
    def __init__(self):
        self.observers = []

    def add_observer(self, observer):
        self.observers.append(observer)

    def notify_observers(self, order):
        for observer in self.observers:
            observer.update(order)

    def create_order(self, order_id, amount):
        order = {
            "id": order_id,
            "amount": amount
        }
        print("\nЗамовлення створено")
        self.notify_observers(order)


# ================== ПРОГРАМА ==================

order_service = OrderService()

logger = LoggerObserver()
notifier = NotificationObserver()
stats = StatisticsObserver()

order_service.add_observer(logger)
order_service.add_observer(notifier)
order_service.add_observer(stats)

while True:
    print("\n1 — створити замовлення")
    print("0 — вихід")

    choice = input("Ваш вибір: ")

    if choice == "1":
        order_id = input("Введіть ID замовлення: ")
        amount = float(input("Введіть суму: "))
        order_service.create_order(order_id, amount)

    elif choice == "0":
        print("Вихід з програми")
        break

    else:
        print("Невірний вибір")