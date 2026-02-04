# ---------- Завдання 1 ----------

numbers = [5, 12, -3, 8, 20, 7, 0, 14, -6, 9]

min_num = numbers[0]
max_num = numbers[0]
total = 0

for num in numbers:
    if num < min_num:
        min_num = num
    if num > max_num:
        max_num = num
    total += num

average = total / len(numbers)

print("Список чисел:", numbers)
print("Мінімальне значення:", min_num)
print("Максимальне значення:", max_num)
print("Сума:", total)
print("Середнє арифметичне:", average)


# ---------- Завдання 2 ----------

data = [[1, 4, 7], [2, 5], [9, 3, 6, 8]]
flat_list = []

for sublist in data:
    for num in sublist:
        flat_list.append(num)

# Просте сортування
for i in range(len(flat_list)):
    for j in range(i + 1, len(flat_list)):
        if flat_list[i] > flat_list[j]:
            flat_list[i], flat_list[j] = flat_list[j], flat_list[i]

print("\nОдновимірний відсортований список:", flat_list)


# ---------- Завдання 3 ----------

students = [
    ("Іван", 85),
    ("Марія", 92),
    ("Олег", 78),
    ("Анна", 90)
]

best_student = students[0]
grades = []
limit = 85
count = 0

for student in students:
    grades.append(student[1])

    if student[1] > best_student[1]:
        best_student = student

    if student[1] > limit:
        count += 1

print("\nНайвища оцінка:", best_student)
print("Список оцінок:", grades)
print("Кількість студентів з оцінкою вище", limit, ":", count)