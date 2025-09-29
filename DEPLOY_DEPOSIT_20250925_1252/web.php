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
    Route::get('payment/success', 'paymentSuccess')->name('payment.success');
    Route::get('payment/{id}', 'payment')->name('payment');
    Route::post('payment/initiate', 'paymentInitiate')->name('payment.initiate');
    Route::post('payment/taurixy-direct', 'taurixyDirect')->name('payment.taurixy.direct');
    Route::get('pending', 'pending')->name('pending');
    Route::get('completed', 'completed')->name('completed');
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

// Alp-Pay webhook
Route::post('webhooks/alppay', [\App\Http\Controllers\Gateway\AlpPayController::class, 'webhook'])->name('webhooks.alppay');
// Local check (GET) fallback
Route::get('webhooks/alppay', [\App\Http\Controllers\Gateway\AlpPayController::class, 'check'])->name('webhooks.alppay.check');
// DEV ONLY: force confirm on localhost
Route::get('webhooks/alppay/force', [\App\Http\Controllers\Gateway\AlpPayController::class, 'force'])->name('webhooks.alppay.force');
