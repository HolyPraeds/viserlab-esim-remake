# 🎯 Простое решение: Alp-Pay для покупки eSIM

## ✅ **Что сделано:**

**Заменили код в `OrderController@taurixyDirect`** - теперь вместо Taurixy используется Alp-Pay!

### **Было:**
```php
$service = new TaurixyService();
$response = $service->createPayment([...]);
```

### **Стало:**
```php
// Create deposit record for this order
$deposit = new \App\Models\Deposit();
$deposit->order_id = $order->id;
// ... создаем deposit ...

// Create Alp-Pay payment
$response = Http::withToken($apiKey)->post('/api/v1/payments', $payload);
```

## 🔄 **Как работает:**

1. **Пользователь заполняет форму** (как раньше) → нажимает **"Pay with Card"**
2. **Форма идет на `taurixyDirect()`** (как раньше) - но теперь этот метод использует **Alp-Pay**
3. **Создается Deposit** с `order_id` и `method_code = 0`
4. **Вызывается Alp-Pay API** → получаем `redirectUrl`
5. **Редирект на Alp-Pay** → пользователь платит
6. **Webhook от Alp-Pay** → `AlpPayController@webhook` → обновляет deposit → создается eSIM

## 📋 **Файлы изменены:**

### **1. `OrderController.php`**
- Метод `taurixyDirect()` теперь создает Alp-Pay платеж вместо Taurixy
- Создает deposit запись для связи с order
- Использует Alp-Pay API для создания платежа

### **2. Форма остается как была**
- Никаких изменений в `payment.blade.php`
- Все поля адреса остаются (хотя Alp-Pay их не использует)
- Роут остается `user.order.payment.taurixy.direct`

## 🚀 **Деплой на продакшн:**

### **Шаг 1: Загрузить изменения**
```bash
git pull origin main
```

### **Шаг 2: Очистить кэш**
```bash
cd /path/to/core
php artisan route:clear
php artisan view:clear
```

### **Шаг 3: Проверить конфигурацию Alp-Pay**
В `.env` должны быть:
```env
ALPPAY_BASE_URL=https://your-alppay-url.com
ALPPAY_API_KEY=your-api-key-here
ALPPAY_SIGNING_KEY=your-signing-key-here
ALPPAY_WEBHOOK_URL=https://travelsim.live/webhooks/alppay
```

### **Шаг 4: Тестировать**
1. Выбрать план → **Purchase Now**
2. Заполнить форму → **Pay with Card**
3. Должен редиректнуть на **Alp-Pay**
4. После оплаты → webhook → создается eSIM

## 🔍 **Логи для отладки:**

```bash
tail -f storage/logs/laravel.log
```

Должны появиться:
```
[...] Order payment deposit created {"order_id":...,"deposit_trx":"...","amount":...}
[...] AlpPay webhook received {"id":"...","state":"COMPLETED",...}
```

## ⚠️ **Важно:**

- **Форма остается как была** - никаких изменений в UI
- **Роут остается тот же** - `user.order.payment.taurixy.direct`
- **Только код метода изменился** - теперь использует Alp-Pay
- **Webhook должен быть доступен** на продакшене

## 🎉 **Готово!**

Теперь при нажатии **"Pay with Card"** создается платеж через **Alp-Pay**!

**Простое и чистое решение** - минимальные изменения, максимальный эффект! ✨

---

**Преимущества:**
- ✅ Форма остается как была
- ✅ Никаких новых роутов
- ✅ Простое изменение в одном методе
- ✅ Полная совместимость с существующей логикой



