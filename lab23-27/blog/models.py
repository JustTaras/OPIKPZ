from django.db import models

class Media(models.Model):
    title = models.CharField(max_length=200)
    description = models.TextField()
    rating = models.IntegerField(default=0)
    studio_name = models.CharField(max_length=100)

    def __str__(self):
        return self.title

class Comment(models.Model):
    # Зв'язок Один до Одного: одне медіа має один коментар
    media = models.OneToOneField(Media, on_delete=models.CASCADE, related_name='comment')
    text = models.TextField()
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"Коментар до {self.media.title}"