<?php

use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->name('user.')->middleware('guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register');
        Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });

    Route::controller('SocialiteController')->group(function () {
        Route::get('social-login/{provider}', 'socialLogin')->name('social.login');
        Route::get('social-login/callback/{provider}', 'callback')->name('social.login.callback');
    });
});

// Public guest checkout routes (no auth required)
Route::controller('User\PlanController')->prefix('plan')->name('user.plan.')->group(function () {
    Route::match(['GET', 'POST'], 'purchase', 'purchase')->name('purchase');
});
Route::controller('User\OrderController')->name('user.order.')->prefix('order')->group(function () {
    Route::get('payment/return/{id?}/{referenceId?}/{state?}/{type?}', 'paymentReturn')->name('payment.return')->where(['state' => '.*']);
    Route::get('payment/success', 'paymentSuccess')->name('payment.success');
    Route::get('payment/{id}', 'payment')->name('payment');
    Route::post('payment/initiate', 'paymentInitiate')->name('payment.initiate');
    Route::post('payment/taurixy-direct', 'taurixyDirect')->name('payment.taurixy.direct');
});

// Temporarily allow guest access to user routes for checkout flow
Route::name('user.')->group(function () {

    // Shorthand /purchase URL (same as plan purchase; must not be behind registration.complete)
    Route::get('purchase', 'User\PlanController@purchase')->name('purchase.index');

    Route::get('user-data', 'User\UserController@userData')->name('data');
    Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');

    //authorization
    Route::middleware('registration.complete')->namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('2fa.verify');
    });

    Route::middleware(['check.status', 'registration.complete'])->group(function () {

        Route::namespace('User')->group(function () {

            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');
                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //KYC
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');

                //Report (use DepositController@index)
                Route::get('deposit/history', 'DepositController@index')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');

                Route::post('add-device-token', 'addDeviceToken')->name('add.device.token');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });

            // Wallet-only plan purchase (must stay behind check.status; do not duplicate user.plan.purchase — see public block above)
            Route::controller('PlanController')->prefix('plan')->name('plan.')->group(function () {
                Route::post('buy-from-wallet', 'buyFromWallet')->name('buy.from.wallet');
            });

            // Logged-in order list + wallet pay only (payment page & AlpPay routes are public above to avoid duplicate route names)
            Route::controller('OrderController')->name('order.')->prefix('order')->group(function () {
                Route::get('pending', 'pending')->name('pending');
                Route::get('completed', 'completed')->name('completed');
                Route::post('payment/{id}/pay-from-wallet', 'payFromWallet')->name('pay.from.wallet');
            });

            // eSIM
            Route::controller('EsimController')->name('esim.')->prefix('esim')->group(function () {
                Route::get('active', 'active')->name('active');
                Route::get('expired', 'expired')->name('expired');

                Route::post('check-capacity', 'checkCapacity')->name('check.capacity');
                Route::get('qr/{id}', 'getQrCode')->name('get.qr');
            });

            Route::controller('ReferralController')->prefix('referral')->name('referral.')->group(function () {
                Route::get('/', 'index')->name('index');
            });
        });

        // Payment (use User\DepositController)
        Route::prefix('deposit')->name('deposit.')->controller('User\DepositController')->group(function () {
            Route::any('/', 'deposit')->name('index');
            Route::post('insert', 'depositInsert')->name('insert');
            Route::get('confirm', 'depositConfirm')->name('confirm');
            Route::get('manual', 'depositManual')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
        });

        // Alp-Pay deposit/create is registered in routes/web.php (GET|POST) — do not duplicate here.
    });
});
