# AlpPay – интеграция в проекте `viserlab-esim-remake`

Документ описывает, как устроена текущая интеграция с AlpPay:

- депозиты в личный кабинет (wallet top‑up)
- оплата eSIM‑заказов картой через AlpPay
- вебхуки и финализация платежей
- ключевые настройки и типичные ошибки

---

## 1. Конфигурация (`config/alppay.php` и `.env`)

Конфиг:

```php
return [
    'base_url'    => env('ALPPAY_BASE_URL', 'https://engine-sandbox.alp-pay.com'),
    'api_key'     => env('ALPPAY_API_KEY', ''),
    'signing_key' => env('ALPPAY_SIGNING_KEY', ''),
    'shop_id'     => env('ALPPAY_SHOP_ID', ''),
    'webhook_url' => env('ALPPAY_WEBHOOK_URL', ''),
];
```

Ожидаемые переменные окружения:

```env
ALPPAY_BASE_URL=https://engine-sandbox.alp-pay.com   # или https://engine.alp-pay.com для продакшена
ALPPAY_API_KEY=...                                   # Bearer токен мерчанта
ALPPAY_SIGNING_KEY=...                               # ключ для подписи вебхуков (HMAC-SHA256)
ALPPAY_WEBHOOK_URL=https://<домен>/webhooks/alppay   # публичный URL нашего webhook'а
ALPPAY_SHOP_ID=...                                   # опционально, если требуется AlpPay
```

> Важно: `ALPPAY_WEBHOOK_URL` должен совпадать с URL, настроенным в кабинете AlpPay (иначе вебхук не дойдёт).

---

## 2. Роуты AlpPay

### 2.1. Депозиты (пополнение баланса)

```php
// routes/web.php
Route::controller(\App\Http\Controllers\Gateway\AlpPayController::class)
    ->prefix('user/deposit/alppay')
    ->name('user.deposit.alppay.')
    ->group(function () {
        Route::post('create', 'create')->name('create');
    });
```

### 2.2. Webhook + dev‑фолбэки

```php
// routes/web.php
Route::post('webhooks/alppay', [AlpPayController::class, 'webhook'])->name('webhooks.alppay');
Route::get('webhooks/alppay', [AlpPayController::class, 'check'])->name('webhooks.alppay.check');     // локальный поллинг статуса
Route::get('webhooks/alppay/force', [AlpPayController::class, 'force'])->name('webhooks.alppay.force'); // dev-only, форс‑подтверждение
```

### 2.3. Оплата заказа (checkout, eSIM purchase)

```php
// routes/user.php
Route::controller('User\OrderController')->name('user.order.')->prefix('order')->group(function () {
    Route::get('payment/success', 'paymentSuccess')->name('payment.success');
    Route::get('payment/{id}', 'payment')->name('payment');
    Route::post('payment/initiate', 'paymentInitiate')->name('payment.initiate');
    Route::post('payment/taurixy-direct', 'taurixyDirect')->name('payment.taurixy.direct');
});
```

> Исторически роут называется `taurixyDirect`, но внутри он уже работает через AlpPay.

---

## 3. Поток: депозит через AlpPay (HPP)

**Файлы:**
- `core/app/Http/Controllers/Gateway/AlpPayController.php`
- шаблоны депозита: `core/resources/views/templates/basic/user/deposit/*.blade.php`

### 3.1. Инициация

1. Пользователь выбирает сумму депозита.
2. На шаге подтверждения депозита форма отправляет `deposit_trx` на роут:

   ```php
   route('user.deposit.alppay.create')
   ```

3. В `AlpPayController::create()`:

   ```php
   $deposit = Deposit::where('trx', $request->deposit_trx)
       ->where('status', 0) // PAYMENT_INITIATE
       ->firstOrFail();

   $baseUrl = config('alppay.base_url');
   $apiKey  = config('alppay.api_key');
   $webhook = config('alppay.webhook_url');

   $payload = [
       'paymentType' => 'DEPOSIT',
       'description' => 'Deposit via Alp-Pay',
       'amount'      => (float) $deposit->final_amount,
       'currency'    => 'EUR',                // для этого шопа жёстко EUR
       'referenceId' => $deposit->trx,        // наш внутренний идентификатор депозита
       'webhookUrl'  => $webhook,
       'customer'    => [
           'referenceId' => (string) $deposit->user_id,
           'email'       => optional($deposit->user)->email,
           'firstName'   => optional($deposit->user)->firstname,
           'lastName'    => optional($deposit->user)->lastname,
           'locale'      => app()->getLocale(),
       ],
   ];

   $response = Http::withToken($apiKey)
       ->acceptJson()
       ->post(rtrim($baseUrl, '/').'/api/v1/payments', $payload);
   ```

4. При успешном ответе:

   ```php
   $data        = $response->json();
   $redirectUrl = $data['result']['redirectUrl'] ?? $data['redirectUrl'] ?? null;
   $paymentId   = $data['result']['id']          ?? $data['id']          ?? null;

   if ($paymentId) {
       $deposit->gateway_trx = $paymentId;
       $deposit->save();
   }

   return redirect()->away($redirectUrl);
   ```

Мы работаем в **HPP‑режиме**: никаких карточных данных не собираем, AlpPay сам показывает платёжную страницу по `redirectUrl`.

### 3.2. Webhook: завершение депозита

Алгоритм в `AlpPayController::webhook()`:

1. Проверка подписи:

   ```php
   $signature = $request->header('Signature');
   $signingKey = config('alppay.signing_key');
   $raw  = $request->getContent();
   $calc = hash_hmac('sha256', $raw, $signingKey);

   if (!hash_equals($calc, (string) $signature)) {
       Log::warning('AlpPay webhook signature mismatch', [...]);
       return response()->json(['ok' => false], 401);
   }
   ```

2. Разбор и поиск депозита:

   ```php
   $payload  = $request->json()->all();
   $paymentId = $payload['id']          ?? null;
   $state     = $payload['state']       ?? null;
   $reference = $payload['referenceId'] ?? null;

   $deposit = null;
   if ($reference) {
       $deposit = Deposit::where('trx', $reference)->first();
   }
   if (!$deposit && $paymentId) {
       $deposit = Deposit::where('gateway_trx', $paymentId)->first();
   }
   ```

3. Обработка статуса:

   ```php
   if ($state === 'COMPLETED') {
       if (in_array($deposit->status, [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])) {
           CorePaymentController::userDataUpdate($deposit);
       }
   } elseif (in_array($state, ['DECLINED', 'CANCELLED'])) {
       ...
   }
   ```

`CorePaymentController::userDataUpdate($deposit)` — общий метод, который:
- проставляет статус депозита `SUCCESS`
- зачисляет баланс пользователю
- если у депозита есть `order_id`, финализирует заказ и активирует eSIM.

---

## 4. Поток: оплата eSIM‑заказа (checkout) через AlpPay

**Файл:** `core/app/Http/Controllers/User/OrderController.php`, метод `taurixyDirect()`.

Поток:

1. Пользователь выбирает план, заполняет checkout‑форму и нажимает **Pay with Card**.
2. Форма отправляется на `user.order.payment.taurixy.direct` (роут остался со старым именем).
3. В `taurixyDirect()`:

   1) Создаём `Deposit`, связанный с заказом:

   ```php
   $order = Order::pending()->findOrFail($request->order_id);

   $deposit = new Deposit();
   $deposit->user_id         = auth()->id() ?? 0;
   $deposit->order_id        = $order->id;
   $deposit->method_code     = 0;      // Alp-Pay
   $deposit->method_currency = 'EUR';
   $deposit->amount          = $order->total_amount;
   $deposit->charge          = 0;
   $deposit->rate            = 1.0;
   $deposit->final_amount    = $order->total_amount;
   $deposit->trx             = getTrx();
   $deposit->success_url     = route('user.order.completed');
   $deposit->failed_url      = route('user.order.payment', $order->order_number);
   $deposit->save();
   ```

   2) Создаём AlpPay‑платёж так же, как для депозитов:

   ```php
   $payload = [
       'paymentType' => 'DEPOSIT',
       'description' => 'eSIM Order #' . $order->order_number,
       'amount'      => (float) $deposit->final_amount,
       'currency'    => 'EUR',
       'referenceId' => $deposit->trx,
       'webhookUrl'  => $webhook,
       'customer'    => [
           'referenceId' => (string) ($deposit->user_id ?: 'guest'),
           'email'       => $request->email,
           'firstName'   => $request->first_name,
           'lastName'    => $request->last_name,
           'locale'      => app()->getLocale(),
       ],
   ];
   ```

4. Дальше всё аналогично депозиту: редирект на AlpPay → оплата → webhook → `userDataUpdate()`:
   - депозит → `SUCCESS`
   - заказ → `COMPLETED`
   - создаётся и активируется eSIM (логика в `DataPlans`/`confirmPurchase`).

> Для локальной разработки есть fallback в `paymentSuccess()`, который может добить заказ, если вебхук не доходит до localhost.

---

## 5. HPP vs H2H (прямые карточные данные)

На данный момент интеграция работает **только в HPP‑режиме**:

- мы **не** отправляем блок `card` в `PaymentRequest`
- карточные данные вводятся **на стороне AlpPay**
- проект не обрабатывает PAN/CVV/expiry → **не требует PCI DSS**.

В `openapi (2).json` описаны H2H‑поля:

- `PaymentRequest.card` → объект `Card` с полями `cardNumber`, `cardholderName`, `cardSecurityCode`, `expiryMonth`, `expiryYear`
- `DepositPatchRequest` → для передачи `customerIp`, `userAgent`, размеров окна и т.п.

Если когда‑то понадобиться H2H:

- придётся:
  - собирать полные данные карты у нас (PCI DSS обязательна),
  - заполнить `card{...}` в `PaymentRequest`,
  - затем сделать `PATCH /payments/{id}` с `DepositPatchRequest` (device‑fingerprinting/3DS).
- сейчас это **осознанно не используется**, чтобы не тащить PCI в проект.

---

## 6. Типичные ошибки и отладка

### 6.1. Где смотреть логи

```bash
tail -f core/storage/logs/laravel.log
```

Ключевые сообщения:

- `Order payment deposit created` — успешное создание депозита под заказ.
- `AlpPay create payment failed` — ошибка на запросе `POST /api/v1/payments`.
- `AlpPay authentication failed (401) - check API key` — неверный или просроченный API‑ключ.
- `AlpPay webhook signature mismatch` — не совпала HMAC‑подпись вебхука.
- `AlpPay webhook deposit not found` — пришёл вебхук с `referenceId`/`id`, по которому нет депозита.

### 6.2. Частые кейсы

- **HTTP 401 от AlpPay**  
  - Причина: неверный `ALPPAY_API_KEY` или токен не привязан к базовому URL.  
  - Решение: проверить ключи в `.env`, перезапустить PHP‑FPM/очистить конфиг‑кэш.

- **HTTP 400 / валидационные ошибки**  
  - Причины: неверный `paymentType`, `currency`, некорректные данные.  
  - В этом проекте уже настроено: `paymentType = 'DEPOSIT'`, `currency = 'EUR'`, минимальный валидный payload.

- **500 на стороне мерчанта при H2H** (для внешних интеграций)  
  - Частая причина: отправка замаскированных карт (`************`), пустой expiry/CVV, попытка H2H без PCI.  
  - В нашем проекте этого нет, т.к. мы не используем H2H.

- **Webhook не доходит**  
  - Проверить:
    - что `ALPPAY_WEBHOOK_URL` публично доступен (не localhost/192.168.x.x)
    - что этот точный URL указан в кабинете AlpPay  
  - На dev можно временно использовать:
    - `GET /webhooks/alppay` для опроса статуса по `gateway_trx`
    - `GET /webhooks/alppay/force` для принудительного завершения депозита.

---

## 7. Чек‑лист для нового мерчанта AlpPay

1. **В AlpPay:**
   - Создать мерчанта и получить:
     - `API key` (Bearer)
     - `Signing key`
   - Настроить `Webhook URL` → `https://<домен>/webhooks/alppay`.
   - Включить нужные валюты (по умолчанию код жёстко использует `EUR`).

2. **В проекте:**
   - Прописать `.env`:
     - `ALPPAY_BASE_URL`
     - `ALPPAY_API_KEY`
     - `ALPPAY_SIGNING_KEY`
     - `ALPPAY_WEBHOOK_URL`
   - При необходимости поменять валюту:
     - поле `'currency' => 'EUR'` в:
       - `AlpPayController::create()`
       - `OrderController::taurixyDirect()`

3. **Проверить:**
   - Депозит маленькой суммой (sandbox):
     - создаётся депозит;
     - есть редирект на AlpPay;
     - после оплаты депозит в БД → `SUCCESS`;
     - баланс пользователя увеличился.
   - Покупка плана (checkout):
     - создаётся `Order` и связанный `Deposit`;
     - редирект на AlpPay;
     - после оплаты заказ → `COMPLETED`, eSIM создан.

4. **Наблюдать логи:**  
   - в случае ошибок смотреть сообщения из раздела **6.1**.

---

Этот документ отражает текущее «боевое» состояние интеграции AlpPay в проекте и может использоваться как база для переноса/масштабирования на другие шопы или инсталляции.


