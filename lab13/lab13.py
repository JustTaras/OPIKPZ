from collections import deque

# ================== СТЕК ==================

print("=== СТЕК ===")

stack = []

# додаємо 5 елементів
stack.append(1)
stack.append(2)
stack.append(3)
stack.append(4)
stack.append(5)
print("Стек після додавання:", stack)

# видаляємо 2 елементи
stack.pop()
stack.pop()
print("Стек після видалення:", stack)

# перевірка порожності
def is_empty(stack):
    return len(stack) == 0

print("Стек порожній?", is_empty(stack))

# верхній елемент
def peek(stack):
    if len(stack) == 0:
        return None
    return stack[-1]

print("Верхній елемент стеку:", peek(stack))


# реверс рядка через стек
text = input("\nВведіть рядок: ")
char_stack = []

for ch in text:
    char_stack.append(ch)

reversed_text = ""
while not is_empty(char_stack):
    reversed_text += char_stack.pop()

print("Рядок навпаки:", reversed_text)


# ================== ЧЕРГА ==================

print("\n=== ЧЕРГА ===")

queue = deque()

# додаємо 5 елементів
for i in range(1, 6):
    queue.append(i)
    print("Додано:", i, "Черга:", list(queue))

# видаляємо 3 елементи
for i in range(3):
    removed = queue.popleft()
    print("Видалено:", removed, "Черга:", list(queue))


# симуляція черги в магазині
print("\nЧерга в магазині")

shop_queue = deque()

shop_queue.append("Клієнт 1")
shop_queue.append("Клієнт 2")
shop_queue.append("Клієнт 3")
print("Поточна черга:", list(shop_queue))

served = shop_queue.popleft()
print("Обслуговано:", served)
print("Черга зараз:", list(shop_queue))


# ================== СЛОВНИК ==================

print("\n=== СЛОВНИК ===")

student = {
    "ім'я": "Тарас",
    "дані": (17, "КН-21")
}

print("Початковий словник:", student)

# додаємо нову пару
student["місто"] = "Київ"
print("Після додавання:", student)

# оновлюємо значення
student["дані"] = (18, "КН-21")
print("Після оновлення:", student)

# видаляємо елемент
del student["місто"]
print("Після видалення:", student)


# ================== ПІДРАХУНОК ОЦІНОК ==================

print("\nПідрахунок оцінок")

marks = [5, 4, 5, 3, 4, 5, 3]
result = {}

for mark in marks:
    if mark in result:
        result[mark] += 1
    else:
        result[mark] = 1

print("Результат:", result)


# ================== МЕНЮ ТОВАРІВ ==================

print("\n=== МЕНЮ ТОВАРІВ ===")

products = {}

while True:
    print("\n1 — додати товар")
    print("2 — видалити товар")
    print("3 — показати всі товари")
    print("0 — вихід")

    choice = input("Ваш вибір: ")

    if choice == "1":
        name = input("Назва товару: ")
        price = float(input("Ціна: "))
        products[name] = price
        print("Товар додано")

    elif choice == "2":
        name = input("Назва товару для видалення: ")
        if name in products:
            del products[name]
            print("Товар видалено")
        else:
            print("Товар не знайдено")

    elif choice == "3":
        print("Список товарів:")
        for name, price in products.items():
            print(name, "-", price)

    elif choice == "0":
        print("Вихід з програми")
        break