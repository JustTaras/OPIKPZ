from django.db import models
from dataclasses import dataclass

# --- Завдання на 3 бали ---
@dataclass
class User:
    first_name: str
    last_name: str
    description: str

# --- Завдання на 4-5 балів ---
@dataclass
class Media:
    title: str
    description: str
    rating: int
    studio_name: str