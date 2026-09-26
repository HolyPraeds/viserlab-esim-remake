# Тестирование eSIM Access (автопокупка планов)

## 1. Проверка подключения к API

Из корня проекта (папка `core`):

```bash
php artisan esimaccess:test
```

Ожидается: вывод `Connection OK.` и баланс (если API вернул). Ошибка — проверить `ESIM_ACCESS_CODE` и `ESIM_SECRET_KEY` в `.env`.

---

## 2. Проверка webhook

- В кабинете eSIM Access указан URL:  
  `https://ТВОЙ_ДОМЕН/viserlab-esim-remake/ipn/esimaccess`
- При сохранении webhook eSIM Access шлёт тестовый POST (CHECK_HEALTH).
- В логе Laravel (`storage/logs/laravel.log`) должна появиться запись `EsimAccess IPN received` с телом запроса.

Ручная проверка: открыть в браузере тот же URL — должна отображаться строка «eSIM Access webhook endpoint — use POST for notifications.»

---

## 3. Полный цикл (оплата → автопокупка eSIM)

### 3.1 Тест с баланса (без карты)

1. **План в БД**  
   ```bash
   php artisan plans:sync
   ```

2. **Пополнить баланс пользователя**  
   - Войти в аккаунт → «Add Balance» (Пополнить) и оформить депозит, **или**  
   - В админке: пользователь → начислить баланс / подтвердить депозит вручную.

3. **Оплата заказа с кошелька**  
   - Выбрать план на сайте → оформить заказ (попадаешь на страницу оплаты).  
   - На странице оплаты в блоке **«Pay from Wallet»** нажать **«Pay»**.  
   - Система вызовет eSIM Access, купит eSIM, спишет сумму с баланса и отметит заказ выполненным.

4. **Результат**  
   - В «My eSIMs» появится выданный eSIM (QR, серийный номер).  
   - В логах при ошибке: `EsimAccess purchase error`.

### 3.2 Тест с карты (шлюз)

1. Выбрать план → оформить заказ → на странице оплаты выбрать «Pay with Card» и оплатить через шлюз (AlpPay, Taurixy и т.д.).
2. После успешной оплаты шлюз вызывает callback → `dataPlans()->confirmPurchase($order)` → eSIM покупается у eSIM Access.
3. В админке: заказ «Completed», у пользователя — eSIM в «My eSIMs».

### 3.3 Ручная оплата (manual)

Если оплата через «manual» (ожидание подтверждения админом), автопокупка в eSIM Access произойдёт только после того, как админ подтвердит депозит (success).

---

## Краткий чеклист

| Шаг | Действие |
|-----|----------|
| 1 | `php artisan esimaccess:test` — OK |
| 2 | Webhook URL сохранён в eSIM Access, в логе есть тестовый POST |
| 3 | `php artisan plans:sync` — планы с `slug` в БД |
| 4 | Тестовый платёж → заказ Completed, eSIM выдан |
