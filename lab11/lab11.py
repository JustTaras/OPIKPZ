# Ввід послідовності цілих чисел
numbers_input = input("Введіть цілі числа через пробіл: ")
numbers_list = list(map(int, numbers_input.split()))

# Формування множини з унікальних значень
numbers_set = set(numbers_list)
print("Сформована множина:", numbers_set)

# Ввід заданого числа
limit = int(input("Введіть число для порівняння: "))

# Підрахунок елементів, більших за задане число
count = 0
for num in numbers_set:
    if num > limit:
        count += 1

print("Кількість елементів, більших за", limit, ":", count)

# Функція для обчислення суми елементів множини
def sum_of_set(s):
    total = 0
    for num in s:
        total += num
    return total

# Виклик функції
result = sum_of_set(numbers_set)
print("Сума всіх елементів множини:", result)

# Функція перевірки належності числа до множини
def check_element(s, value):
    for num in s:
        if num == value:
            print("Число", value, "належить до множини.")
            return
    print("Число", value, "не належить до множини.")

# Перевірка належності
check_value = int(input("Введіть число для перевірки належності: "))
check_element(numbers_set, check_value)