from django.shortcuts import render
from django.http import Http404
from .models import Media

def home(request):
    # Створюємо об'єкт користувача (3 бали)
    user = User(
        first_name='Іван', 
        last_name='Петренко', 
        description='Програміст'
    )
    
    # Створюємо об'єкт медіа (4-5 балів)
    media_item = Media(
        title='Interstellar',
        description='Епічний науково-фантастичний фільм.',
        rating=10,
        studio_name='Paramount Pictures'
    )

    # Пакуємо все в один словник
    context = {
        'user': user,
        'media': media_item,
    }
    
    return render(request, 'blog/home.html', context)

def about(request):
    return render(request, 'blog/about.html')

def media_detail(request, index):
    try:
        # Отримуємо об'єкт за його порядковим індексом (0, 1, 2...)
        media = Media.objects.all()[index]
    except IndexError:
        # Якщо об'єкта під таким індексом немає в базі, повертаємо помилку 404
        raise Http404("Медіа під цим індексом не знайдено")
    
    context = {
        'media': media,
    }
    return render(request, 'blog/media_detail.html', context)

from rest_framework import viewsets
from .models import Task
from .serializers import TaskSerializer

class TaskViewSet(viewsets.ModelViewSet):
    queryset = Task.objects.all()
    serializer_class = TaskSerializer