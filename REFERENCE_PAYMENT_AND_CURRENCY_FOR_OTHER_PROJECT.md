# Справочник: оплата, AlpPay и цены — для переноса в другой проект

Корень проекта (полный путь):
```
c:/xampp/htdocs/viserlab-esim-remake
```
Все пути ниже — от этого корня. В другом проекте замени префикс на свой.

---

## 1. AlpPay (полные пути)

### Контроллер и конфиг
| Назначение | Полный путь |
|------------|-------------|
| Контроллер AlpPay (create, webhook, H2H) | `c:/xampp/htdocs/viserlab-esim-remake/core/app/Http/Controllers/Gateway/AlpPayController.php` |
| Конфиг AlpPay (base_url, api_key, shop_id, webhook) | `c:/xampp/htdocs/viserlab-esim-remake/core/config/alppay.php` |

### Роуты AlpPay (где объявлены)
| Файл | Что внутри |
|------|------------|
| `c:/xampp/htdocs/viserlab-esim-remake/core/routes/web.php` | `user/deposit/alppay/*` (create, h2h, …), `webhooks/alppay` (POST webhook, GET check, GET force) |
| `c:/xampp/htdocs/viserlab-esim-remake/core/routes/user.php` | `deposit/alppay/create` → `Gateway\AlpPayController@create` (редирект после выбора метода) |

### Вьюхи и вызов AlpPay
| Назначение | Полный путь |
|------------|-------------|
| Страница оплаты заказа (валюта, кнопки gateway) | `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/user/order/payment.blade.php` |
| Депозит: список, выбор метода, подтверждение | `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/user/deposit/index.blade.php` |
| | `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/user/deposit/confirm.blade.php` |
| | `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/user/deposit/manual.blade.php` |
| Страница payment/deposit (альт) | `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/user/payment/deposit.blade.php` |

### Документация и тесты AlpPay
| Назначение | Полный путь |
|------------|-------------|
| Интеграция AlpPay (поля, подписи, статусы) | `c:/xampp/htdocs/viserlab-esim-remake/Gateway Info/ALPPay_Integration.md` |
| Общее по гейтвеям | `c:/xampp/htdocs/viserlab-esim-remake/Gateway Info/README.md` |
| Упрощённый сценарий AlpPay | `c:/xampp/htdocs/viserlab-esim-remake/SIMPLE_ALPPAY_SOLUTION.md` |
| Тест H2H AlpPay (скрипт) | `c:/xampp/htdocs/viserlab-esim-remake/core/scripts/test_alppay_h2h_usd.php` |
| Деплой-пакет с AlpPay (пример модуля) | `c:/xampp/htdocs/viserlab-esim-remake/DEPLOY_DEPOSIT_20250925_1252/AlpPayController.php` |
| | `c:/xampp/htdocs/viserlab-esim-remake/DEPLOY_DEPOSIT_20250925_1252/web.php` |

### Упоминания AlpPay в коде
| Файл | Зачем смотреть |
|------|----------------|
| `c:/xampp/htdocs/viserlab-esim-remake/core/app/Http/Controllers/User/OrderController.php` | Выбор метода оплаты, редирект на AlpPay, `payment_currency` для заказа |
| `c:/xampp/htdocs/viserlab-esim-remake/CURRENCY_IMPLEMENTATION_README.md` | Валюта заказа и AlpPay |
| `c:/xampp/htdocs/viserlab-esim-remake/LIVE_DEPLOYMENT_PACKAGE.md` | Деплой с учётом AlpPay |

---

## 2. Оплата заказа и депозит (общий поток)

### Контроллеры
| Назначение | Полный путь |
|------------|-------------|
| Заказ: создание, оплата, success/return, инициация платежа | `c:/xampp/htdocs/viserlab-esim-remake/core/app/Http/Controllers/User/OrderController.php` |
| Депозит: форма, insert, confirm, manual | `c:/xampp/htdocs/viserlab-esim-remake/core/app/Http/Controllers/User/DepositController.php` |
| Общий gateway: редирект на шлюз, callback/return | `c:/xampp/htdocs/viserlab-esim-remake/core/app/Http/Controllers/Gateway/PaymentController.php` |
| Покупка плана (purchase, buy from wallet) | `c:/xampp/htdocs/viserlab-esim-remake/core/app/Http/Controllers/User/PlanController.php` |

### Роуты (файлы)
| Файл | Что важно для оплаты |
|------|----------------------|
| `c:/xampp/htdocs/viserlab-esim-remake/core/routes/web.php` | payment/return, payment/success, payment/{id}, payment/initiate, taurixy-direct, cron, AlpPay routes + webhook |
| `c:/xampp/htdocs/viserlab-esim-remake/core/routes/user.php` | plan/purchase, order/payment*, deposit/*, deposit/alppay/create |

### Вьюхи оплаты/депозита
| Полный путь |
|-------------|
| `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/user/order/payment.blade.php` |
| `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/user/order/index.blade.php` |
| `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/user/deposit/index.blade.php` |
| `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/user/deposit/confirm.blade.php` |
| `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/user/deposit/manual.blade.php` |
| `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/user/payment/deposit.blade.php` |
| `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/user/transactions.blade.php` |

### Модели и миграции
| Назначение | Полный путь |
|------------|-------------|
| Валюты gateway (методы оплаты, комиссии) | `c:/xampp/htdocs/viserlab-esim-remake/core/app/Models/GatewayCurrency.php` |
| Валюта заказа (payment_currency) | `c:/xampp/htdocs/viserlab-esim-remake/core/database/migrations/2025_12_22_000001_add_payment_currency_to_orders_table.php` |
| gateway_trx в deposits | `c:/xampp/htdocs/viserlab-esim-remake/core/database/migrations/` (файл add_gateway_trx_to_deposits — см. DEPLOY_DEPOSIT_20250925_1252) |

### Хелперы
| Назначение | Полный путь |
|------------|-------------|
| showAmount, формат валюты | `c:/xampp/htdocs/viserlab-esim-remake/core/app/Http/Helpers/helpers.php` |

---

## 3. Автоматическое изменение цен (курсы и отображение)

### Базовая валюта и курсы в БД
| Назначение | Полный путь |
|------------|-------------|
| Настройки сайта (cur_text, cur_sym, currency_api_key) | Таблица `general_settings`; админка: `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/admin/setting/general.blade.php` |
| API ключ для курсов (currencylayer) | `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/admin/api/index.blade.php` |

### Обновление курсов (цены «сами меняются»)
| Назначение | Полный путь |
|------------|-------------|
| Класс: запрос к API, запись conversion_rate в currencies | `c:/xampp/htdocs/viserlab-esim-remake/core/app/Lib/CurrencyLayer.php` |
| Крон: обновить все курсы | `c:/xampp/htdocs/viserlab-esim-remake/core/app/Http/Controllers/CronController.php` (метод `fetchCurrency`) |
| Синк планов: подтянуть курс для новых валют | `c:/xampp/htdocs/viserlab-esim-remake/core/app/Lib/DataPlans.php` (в конце addOrUpdatePlans → CurrencyLayer::updateRates) |
| Ручной скрипт синка курсов | `c:/xampp/htdocs/viserlab-esim-remake/core/sync_currencies.php` |

### Модели и пересчёт цены плана
| Назначение | Полный путь |
|------------|-------------|
| Модель валюты, conversion_rate | `c:/xampp/htdocs/viserlab-esim-remake/core/app/Models/Currency.php` |
| План: convertedPrice (retail_price / conversion_rate) | `c:/xampp/htdocs/viserlab-esim-remake/core/app/Models/Plan.php` |
| Контроллер: convertPlanPrice для списков | `c:/xampp/htdocs/viserlab-esim-remake/core/app/Http/Controllers/SiteController.php` |

### Переключатель валют на сайте (EUR/GBP/USD)
| Назначение | Полный путь |
|------------|-------------|
| Кнопки и JS: фиксированные курсы, data-base-amount, updateAllPrices | `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/partials/header.blade.php` |
| Страницы с ценами (data-base-amount, data-base-currency) | `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/region_plans.blade.php` |
| | `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/country_plans.blade.php` |
| | `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/sections/coverage.blade.php` |
| | `c:/xampp/htdocs/viserlab-esim-remake/core/resources/views/templates/basic/destination.blade.php` |

### Документация по валютам
| Полный путь |
|-------------|
| `c:/xampp/htdocs/viserlab-esim-remake/CURRENCY_IMPLEMENTATION_README.md` |
| `c:/xampp/htdocs/viserlab-esim-remake/CURRENCY_SWITCH_INSTRUCTIONS.txt` |

---

## 4. Краткий чеклист для переноса в другой проект

1. **AlpPay:** скопировать/адаптировать `core/app/Http/Controllers/Gateway/AlpPayController.php`, добавить `core/config/alppay.php`, зарегистрировать роуты (create + webhook).
2. **Оплата заказа:** перенести методы из `OrderController` (payment, paymentInitiate, paymentReturn, paymentSuccess), из `PaymentController` (редирект на gateway), при необходимости — `DepositController` и вьюхи депозита/оплаты.
3. **Курсы и цены:** таблицы `general_settings`, `currencies`; `CurrencyLayer.php` + вызовы (cron + при синке планов); модель `Plan` с `convertedPrice`; хелпер `showAmount`; при необходимости — переключатель в шапке и data-атрибуты на страницах с ценами.
4. **Роуты:** выписать из `core/routes/web.php` и `core/routes/user.php` все маршруты, связанные с order, payment, deposit, alppay, webhooks, и воспроизвести в новом проекте.

Все пути выше — полные (от корня `c:/xampp/htdocs/viserlab-esim-remake`). В другом проекте замени этот префикс на свой корень.
