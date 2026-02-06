from abc import ABC, abstractmethod

# ================== STRATEGY ==================

class IPriceStrategy(ABC):
    @abstractmethod
    def calculate(self, price):
        pass


class NoDiscountStrategy(IPriceStrategy):
    def calculate(self, price):
        return price


class RegularCustomerStrategy(IPriceStrategy):
    def calculate(self, price):
        return price * 0.9   # 10% знижка


class BigOrderStrategy(IPriceStrategy):
    def calculate(self, price):
        return price * 0.8   # 20% знижка


# ================== CONTEXT ==================

class PriceCalculator:
    def __init__(self, strategy):
        self.strategy = strategy

    def set_strategy(self, strategy):
        self.strategy = strategy

    def calculate_price(self, price):
        return self.strategy.calculate(price)


# ================== ПРОГРАМА ==================

strategies = {
    "1": NoDiscountStrategy(),
    "2": RegularCustomerStrategy(),
    "3": BigOrderStrategy()
}

calculator = PriceCalculator(strategies["1"])

while True:
    print("\n=== РОЗРАХУНОК ЦІНИ ===")
    print("1 — без знижки")
    print("2 — постійний клієнт (10%)")
    print("3 — велике замовлення (20%)")
    print("0 — вихід")

    choice = input("Оберіть варіант: ")

    if choice == "0":
        print("Вихід з програми")
        break

    if choice in strategies:
        calculator.set_strategy(strategies[choice])
        price = float(input("Введіть початкову ціну: "))
        final_price = calculator.calculate_price(price)
        print("Фінальна ціна:", final_price)
    else:
        print("Невірний вибір")