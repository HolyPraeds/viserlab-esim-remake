# Реализация конвертации валют и создания заказов

## Описание
Реализована система переключения валют (EUR/GBP) с фронтенд-конвертацией и созданием заказов в выбранной валюте.

## Файлы в архиве

### 1. Frontend - Переключатель валют
- `resources/views/templates/basic/partials/header.blade.php`
  - Переключатель валют EUR/GBP в header
  - JavaScript для конвертации всех цен на странице
  - Сохранение выбранной валюты в localStorage
  - Курс конвертации: 1 EUR = 0.87 GBP

### 2. Frontend - Страницы с ценами
- `resources/views/templates/basic/region_plans.blade.php`
  - Конвертация цен планов регионов
  - Скрытое поле `site_currency` для передачи валюты при покупке
  - Обновление деталей плана при переключении валюты

- `resources/views/templates/basic/country_plans.blade.php`
  - Конвертация цен планов стран
  - Скрытое поле `site_currency` для передачи валюты при покупке
  - Обновление деталей плана при переключении валюты

- `resources/views/templates/basic/sections/coverage.blade.php`
  - Конвертация цен стран на главной странице
  - Data-атрибуты для правильной конвертации

- `resources/views/templates/basic/destination.blade.php`
  - Конвертация цен стран на странице destination
  - Data-атрибуты для правильной конвертации

### 3. Backend - Создание заказов
- `app/Http/Controllers/User/PlanController.php`
  - Метод `purchase()`:
    - Принимает `site_currency` из формы
    - Конвертирует цену плана по курсу 0.87 (EUR → GBP)
    - Сохраняет `payment_currency` в заказе
    - Сохраняет конвертированную цену в `total_amount`

### 4. Backend - Обработка оплаты
- `app/Http/Controllers/User/OrderController.php`
  - Метод `payment()`:
    - Фильтрует gateway currencies по `payment_currency` из заказа
  - Метод `taurixyDirect()`:
    - Использует `payment_currency` из заказа для создания депозита
    - Отправляет правильную валюту в AlpPay API

### 5. Frontend - Страница оплаты
- `resources/views/templates/basic/user/order/payment.blade.php`
  - Отображает валюту из `payment_currency` заказа
  - Показывает правильный символ валюты (£ для GBP, € для EUR)

### 6. Helpers и API
- `app/Http/Helpers/helpers.php`
  - Функции `getCurrencyRate()` и `convertCurrency()` (не используются, конвертация на фронте)
  - Функция `showAmount()` с параметром `useCredits` для отображения Credits в личном кабинете

- `routes/api.php`
  - Маршрут `/api/currency/rates` (не используется, используется фиксированный курс)

### 7. Миграция базы данных
- `database/migrations/2025_12_22_000001_add_payment_currency_to_orders_table.php`
  - Добавляет поле `payment_currency` в таблицу `orders`

## Как работает

### 1. Переключение валюты
1. Пользователь нажимает EUR или GBP в header
2. JavaScript конвертирует все цены на странице по курсу 0.87
3. Выбор сохраняется в localStorage
4. При переходе на другую страницу валюта сохраняется

### 2. Создание заказа
1. Пользователь выбирает план и нажимает "Purchase Now"
2. JavaScript обновляет скрытое поле `site_currency` перед отправкой формы
3. Backend получает `site_currency` из формы
4. Если GBP → конвертирует цену: `EUR * 0.87 = GBP`
5. Создается заказ с `payment_currency=GBP` и конвертированной ценой

### 3. Оплата
1. На странице оплаты отображается цена в валюте из `payment_currency`
2. При оплате через AlpPay отправляется правильная валюта
3. Gateway currencies фильтруются по валюте заказа

## Курс конвертации
- **Фиксированный курс**: 1 EUR = 0.87 GBP
- Конвертация происходит на фронтенде (JavaScript) и на бэкенде (PHP)

## Установка

1. Распакуйте архив
2. Скопируйте файлы в соответствующие директории проекта
3. Убедитесь, что миграция выполнена:
   ```bash
   php artisan migrate
   ```
4. Очистите кэш:
   ```bash
   php artisan view:clear
   php artisan config:clear
   php artisan route:clear
   ```

## Проверка

1. Откройте сайт и выберите GBP в header
2. Проверьте, что все цены конвертированы
3. Выберите план и нажмите "Purchase Now"
4. На странице оплаты должна отображаться цена в GBP с символом £
5. Проверьте логи Laravel для подтверждения получения валюты

## Логирование

В `PlanController@purchase` добавлено логирование:
- Полученная валюта из формы
- Примененная конвертация (если GBP)
- Созданный заказ с валютой

Проверьте логи: `storage/logs/laravel.log`







