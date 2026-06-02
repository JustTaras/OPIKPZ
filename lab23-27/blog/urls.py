from django.urls import path, include
from rest_framework.routers import DefaultRouter
from . import views

# 1. Створюємо об'єкт роутера
router = DefaultRouter()

# 2. Реєструємо наш ViewSet
router.register(r'tasks', views.TaskViewSet)

urlpatterns = [
    path('', views.home, name='home'),
    path('about/', views.about, name='about'),
    path('media/<int:index>/', views.media_detail, name='media_detail'),
    
    # 3. Підключаємо згенеровані роутером шляхи API
    path('api/', include(router.urls)),
]