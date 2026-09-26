# Инструкция: Как использовать дизайн политик в другом проекте

## Файлы для копирования

1. **`POLICY_DESIGN_CSS_ONLY.css`** - Все стили CSS (скопируйте в ваш проект)
2. **`POLICY_DESIGN_FOR_OTHER_PROJECT.html`** - Полный пример HTML страницы

## Быстрый старт

### Вариант 1: Использование готового HTML

1. Откройте файл `POLICY_DESIGN_FOR_OTHER_PROJECT.html`
2. Скопируйте содержимое `<style>` в ваш CSS файл или оставьте в `<head>`
3. Скопируйте структуру HTML из `<section class="policy-section">` в ваш шаблон

### Вариант 2: Интеграция CSS в существующий проект

1. Скопируйте содержимое `POLICY_DESIGN_CSS_ONLY.css` в ваш CSS файл
2. Используйте HTML структуру из примера ниже

## HTML структура

```html
<section class="policy-section">
    <div class="container">
        <div class="policy-container">
            <div class="policy-content">
                
                <!-- Заголовок политики -->
                <h3>Privacy Policy</h3>

                <!-- Блок информации о компании (опционально) -->
                <div class="company-header">
                    <p class="company-name">YOUR COMPANY NAME</p>
                    <p>Company Registration Number: 12345678</p>
                    <p>Registered Address: Your Address</p>
                    <p>Last Updated: January 1, 2025</p>
                </div>

                <!-- Вводный текст -->
                <p>Ваш вводный текст...</p>

                <!-- Секция -->
                <h4>1. Заголовок секции</h4>
                <p>Текст секции...</p>

                <!-- Подсекция -->
                <h5>1.1 Подзаголовок</h5>
                <p>Текст подсекции...</p>

                <!-- Списки -->
                <ul>
                    <li>Пункт списка 1</li>
                    <li>Пункт списка 2</li>
                </ul>

                <!-- Важный блок информации -->
                <div class="highlight-box">
                    <p><strong>Важно:</strong> Важное сообщение...</p>
                </div>

                <!-- Предупреждение -->
                <div class="warning-box">
                    <p><strong>Предупреждение:</strong> Текст предупреждения...</p>
                </div>

                <!-- Информационный блок -->
                <div class="info-box">
                    <p><strong>Примечание:</strong> Информационное сообщение...</p>
                </div>

                <!-- Секция контактов -->
                <div class="contact-section">
                    <h4>Contact Us</h4>
                    <p>Ваша контактная информация...</p>
                </div>

            </div>
        </div>
    </div>
</section>
```

## Классы для использования

### Основные контейнеры:
- `.policy-section` - Внешняя секция с фоном
- `.policy-container` - Белая карточка с содержимым
- `.policy-content` - Внутренний контент

### Элементы:
- `h3` - Заголовок страницы (автоматически стилизуется)
- `h4` - Заголовки секций (1., 2., 3. и т.д.)
- `h5` - Подзаголовки (1.1, 1.2 и т.д.)
- `.company-header` - Блок информации о компании (градиентный фон)

### Специальные блоки:
- `.highlight-box` - Голубой блок для важной информации
- `.warning-box` - Оранжевый блок для предупреждений
- `.info-box` - Зеленый блок для информационных сообщений
- `.contact-section` - Секция контактов с градиентным фоном

## Адаптивность

Дизайн полностью адаптивен:
- **Desktop (>768px):** Полная версия
- **Tablet (≤768px):** Уменьшенные отступы и шрифты
- **Mobile (≤576px):** Компактная версия

## Настройка цветов

Если нужно изменить цветовую схему, отредактируйте в CSS:

```css
/* Основной акцентный цвет (синий) */
border-left: 4px solid #007bff;  /* Замените на ваш цвет */

/* Градиент для company-header */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

/* Цвета блоков */
.highlight-box { background: #f0f9ff; border-left: 4px solid #007bff; }
.warning-box { background: #fff7ed; border-left: 4px solid #f59e0b; }
.info-box { background: #f0fdf4; border-left: 4px solid #10b981; }
```

## Интеграция с популярными фреймворками

### Laravel Blade:
```blade
@extends('layouts.app')
@section('content')
    <section class="policy-section">
        <div class="container">
            <div class="policy-container">
                <div class="policy-content">
                    <h3>{{ $policy->title }}</h3>
                    {!! $policy->content !!}
                </div>
            </div>
        </div>
    </section>
@endsection
```

### React:
```jsx
<div className="policy-section">
    <div className="container">
        <div className="policy-container">
            <div className="policy-content">
                <h3>Privacy Policy</h3>
                {/* Ваш контент */}
            </div>
        </div>
    </div>
</div>
```

### Vue.js:
```vue
<template>
    <section class="policy-section">
        <div class="container">
            <div class="policy-container">
                <div class="policy-content">
                    <h3>{{ title }}</h3>
                    <div v-html="content"></div>
                </div>
            </div>
        </div>
    </section>
</template>
```

## Примеры использования

### Простая политика:
```html
<div class="policy-content">
    <h3>Terms of Service</h3>
    <h4>1. Acceptance</h4>
    <p>By using our service...</p>
    <h4>2. Usage</h4>
    <p>You agree to...</p>
</div>
```

### Политика с важными блоками:
```html
<div class="policy-content">
    <h3>Refund Policy</h3>
    <div class="warning-box">
        <p><strong>No Refunds:</strong> All sales are final.</p>
    </div>
    <h4>1. Exceptions</h4>
    <p>Refunds may be issued in the following cases...</p>
</div>
```

## Поддержка

Все стили протестированы и работают в:
- ✅ Chrome/Edge (последние версии)
- ✅ Firefox (последние версии)
- ✅ Safari (последние версии)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## Лицензия

Свободно используйте этот дизайн в любых проектах. Никаких ограничений.
