from django.urls import path
from . import views

urlpatterns = [
    path('', views.home, name='home'),
    path('about/', views.about, name='about'),
    path('media/<int:index>/', views.media_detail, name='media_detail'), # Новий динамічний маршрут
]