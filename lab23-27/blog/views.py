from django.shortcuts import render
from .models import User, Media  # Імпортуємо обидва класи

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