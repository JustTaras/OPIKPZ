# ================== MODEL ==================

class Order:
    def __init__(self, order_id, amount):
        self.order_id = order_id
        self.amount = amount


class OrderRepository:
    def __init__(self):
        self.orders = []

    def add_order(self, order):
        self.orders.append(order)

    def get_all_orders(self):
        return self.orders

    def get_total_amount(self):
        total = 0
        for order in self.orders:
            total += order.amount
        return total


# ================== VIEW ==================

class OrderView:
    def show_menu(self):
        print("\n=== МЕНЮ ===")
        print("1 — додати замовлення")
        print("2 — переглянути всі замовлення")
        print("3 — показати загальну суму")
        print("0 — вихід")

    def get_choice(self):
        return input("Ваш вибір: ")

    def get_order_data(self):
        order_id = input("Введіть ID замовлення: ")
        amount = float(input("Введіть суму замовлення: "))
        return order_id, amount

    def show_orders(self, orders):
        if len(orders) == 0:
            print("Замовлень немає")
        else:
            print("\nСписок замовлень:")
            for order in orders:
                print("ID:", order.order_id, "| Сума:", order.amount)

    def show_total(self, total):
        print("Загальна сума замовлень:", total)

    def show_message(self, message):
        print(message)


# ================== CONTROLLER ==================

class OrderController:
    def __init__(self, repository, view):
        self.repository = repository
        self.view = view

    def run(self):
        while True:
            self.view.show_menu()
            choice = self.view.get_choice()

            if choice == "1":
                order_id, amount = self.view.get_order_data()
                order = Order(order_id, amount)
                self.repository.add_order(order)
                self.view.show_message("Замовлення додано")

            elif choice == "2":
                orders = self.repository.get_all_orders()
                self.view.show_orders(orders)

            elif choice == "3":
                total = self.repository.get_total_amount()
                self.view.show_total(total)

            elif choice == "0":
                self.view.show_message("Вихід з програми")
                break

            else:
                self.view.show_message("Невірний вибір")


# ================== ЗАПУСК ПРОГРАМИ ==================

repository = OrderRepository()
view = OrderView()
controller = OrderController(repository, view)

controller.run()