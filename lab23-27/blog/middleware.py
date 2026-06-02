import time

class RequestLogMiddleware:
    def __init__(self, get_response):
        self.get_response = get_response
        # Одноразове налаштування та ініціалізація під час запуску сервера.

    def __call__(self, request):
        # 1. Логіка ДО виклику View (фіксуємо час старту)
        start_time = time.time()

        # Виклик наступного middleware або безпосередньо View
        response = self.get_response(request)

        # 2. Логіка ПІСЛЯ виклику View (обчислюємо час виконання)
        execution_time = time.time() - start_time
        
        # Виводимо інформацію в консоль (форматуємо час до 3 знаків після коми)
        print(f"[{request.method}] {request.path} - {execution_time:.3f}s")

        # 3. Додаємо кастомний заголовок у відповідь
        response['X-App-Name'] = 'MyDjangoApp'

        return response