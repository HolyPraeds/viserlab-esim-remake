<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::get('cron', 'CronController@cron')->name('cron');

// Guest checkout routes (public)
Route::controller('User\PlanController')->prefix('user/plan')->name('user.plan.')->group(function () {
    Route::match(['GET', 'POST'], 'purchase', 'purchase')->name('purchase');
});
Route::controller('User\OrderController')->prefix('user/order')->name('user.order.')->group(function () {
    Route::get('payment/return/{id?}/{referenceId?}/{state?}/{type?}', 'paymentReturn')->name('payment.return')->where(['state' => '.*']);
    Route::get('payment/success', 'paymentSuccess')->name('payment.success');
    Route::get('payment/{id}', 'payment')->name('payment');
    Route::post('payment/initiate', 'paymentInitiate')->name('payment.initiate');
    Route::post('payment/taurixy-direct', 'taurixyDirect')->name('payment.taurixy.direct');
    Route::get('pending', 'pending')->name('pending');
    Route::get('completed', 'completed')->name('completed');
    Route::match(['GET', 'POST'], 'track', 'track')->name('track');
});

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{id}', 'replyTicket')->name('reply');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
});

Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');
    Route::get('/about', 'about')->name('about');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::post('send-message', 'sendMessage')->name('send.message');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('blogs', 'blogs')->name('blogs');
    Route::get('blog/{slug}', 'blogDetails')->name('blog.details');
    Route::get('/country-plans/{slug}', 'countryPlans')->name('country.plans');
    Route::get('/region-plans/{slug}', 'regionPlans')->name('region.plans');
    Route::get('/region-countries/{slug}', 'regionCountries')->name('region.countries');
    Route::get('/all-countries', 'getAllCountries')->name('all.countries');
    Route::get('/search/country', 'searchCountry')->name('search.country');
    Route::get('destination', 'destination')->name('destination');

    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode', 'maintenance')->withoutMiddleware('maintenance')->name('maintenance');

    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});

// Alp-Pay user deposit routes (must stay behind auth — was previously duplicated in routes/user.php)
Route::middleware(['auth', 'check.status', 'registration.complete'])->controller(\App\Http\Controllers\Gateway\AlpPayController::class)->prefix('user/deposit/alppay')->name('user.deposit.alppay.')->group(function () {
    // Allow direct redirect from deposit form without intermediate Payment Preview page
    Route::match(['GET','POST'], 'create', 'create')->name('create');
    // H2H flow: form first, then create with card
    Route::get('h2h', 'createH2H')->name('h2h.create');
    Route::post('h2h/card', 'createH2HWithCard')->name('h2h.createWithCard');
    Route::get('h2h/process', 'processH2H')->name('h2h.process');
    Route::post('h2h/submit', 'submitH2H')->name('h2h.submit');
    Route::get('h2h/check', 'checkH2H')->name('h2h.check');
});

// Alp-Pay webhook
Route::post('webhooks/alppay', [\App\Http\Controllers\Gateway\AlpPayController::class, 'webhook'])->name('webhooks.alppay');
// Local check (GET) fallback
Route::get('webhooks/alppay', [\App\Http\Controllers\Gateway\AlpPayController::class, 'check'])->name('webhooks.alppay.check');
// DEV ONLY: force confirm on localhost
Route::get('webhooks/alppay/force', [\App\Http\Controllers\Gateway\AlpPayController::class, 'force'])->name('webhooks.alppay.force');