# 🚀 Деплой обновлений на продакшн

## 📦 **Что обновляется:**

### **1. Alp-Pay для покупки симок**
- `OrderController@taurixyDirect()` теперь использует Alp-Pay вместо Taurixy
- Исправлена ошибка с `paymentType: 'DEPOSIT'`
- Все платежи картой идут через Alp-Pay

### **2. Исправление изображения на депозите**
- Убрана картинка PayPal
- Добавлена иконка карты 💳 без фона

## 📋 **Инструкция по деплою:**

### **Шаг 1: Загрузить изменения через Git**
```bash
cd /path/to/project
git pull origin main
```

### **Шаг 2: Перейти в директорию core**
```bash
cd core
```

### **Шаг 3: Очистить кэш**
```bash
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

### **Шаг 4: Проверить конфигурацию Alp-Pay**
В `.env` должны быть:
```env
ALPPAY_BASE_URL=https://your-alppay-url.com
ALPPAY_API_KEY=your-api-key-here
ALPPAY_SIGNING_KEY=your-signing-key-here
ALPPAY_WEBHOOK_URL=https://travelsim.live/webhooks/alppay
```

### **Шаг 5: Тестирование**

#### **5.1 Тест покупки симки:**
1. Выбрать план → **Purchase Now**
2. Заполнить форму → **Pay with Card**
3. Должен редиректнуть на **Alp-Pay** (не на Taurixy)
4. После оплаты → webhook → создается eSIM

#### **5.2 Тест депозита:**
1. Перейти на `/user/deposit`
2. Должна показываться иконка карты 💳 (не PayPal)
3. Заполнить сумму → **Confirm Deposit**
4. Должен редиректнуть на **Alp-Pay**

## 🔍 **Логи для проверки:**

```bash
tail -f storage/logs/laravel.log
```

**Успешная покупка симки:**
```
[...] Order payment deposit created {"order_id":...,"deposit_trx":"...","amount":...}
[...] AlpPay webhook received {"id":"...","state":"COMPLETED",...}
```

**Успешный депозит:**
```
[...] AlpPay create payment failed {"status":400,...} # Если есть ошибки
[...] AlpPay webhook received {"id":"...","state":"COMPLETED",...}
```

## ⚠️ **Важные моменты:**

1. **Webhook должен быть доступен** на `https://travelsim.live/webhooks/alppay`
2. **API ключ должен быть валидный** - иначе будет ошибка 401
3. **Все платежи теперь идут через Alp-Pay** - и депозиты, и покупки
4. **Taurixy больше не используется**

## 🎯 **Файлы изменены:**

### **Изменены:**
- `core/app/Http/Controllers/User/OrderController.php` - метод `taurixyDirect()`
- `core/resources/views/templates/basic/user/payment/deposit.blade.php` - убрана картинка PayPal

### **Новые файлы:**
- `SIMPLE_ALPPAY_SOLUTION.md` - документация

## 🚨 **Если что-то не работает:**

### **Ошибка 401 от Alp-Pay:**
```
AlpPay authentication failed (401) - check API key
```
**Решение:** Проверить `ALPPAY_API_KEY` в `.env`

### **Ошибка 400 от Alp-Pay:**
```
Validation failed for object='paymentRequest'. Error count: 1
```
**Решение:** Уже исправлено - используем `paymentType: 'DEPOSIT'`

### **Webhook не приходит:**
**Решение:** Проверить что `ALPPAY_WEBHOOK_URL` доступен из интернета

### **Все еще показывается Taurixy:**
**Решение:** Очистить кэш - `php artisan optimize:clear`

## 🎉 **Готово!**

После деплоя:
- ✅ **Покупка симок** → Alp-Pay
- ✅ **Депозиты** → Alp-Pay  
- ✅ **Никаких изображений PayPal**
- ✅ **Единая система платежей**

---

**Команда для быстрого деплоя:**
```bash
cd core && php artisan optimize:clear
```

**Готов к продакшну!** 🚀



