<?php

$travelsim = [
    'id' => 'travelsim',
    'name' => 'TravelSim',
    'base_color' => null,
    'css' => [],
    'body_class' => '',
    'logo_dir' => 'assets/brands/travelsim',
    'url' => 'https://travelsim.live',
    'email_from' => 'info@travelsim.live',
    'navbar_brand_class' => '',
    'logo_img_class' => '',
    'footer_logo_class' => '',
    'hide_home_sections' => [],
    'images' => [],
];

$payersim = [
    'id' => 'payersim',
    'name' => 'PayerSim',
    'base_color' => '712b44',
    'css' => ['rebrand.css', 'orbit-theme.css', 'calm-theme.css'],
    'body_class' => 'theme-calm',
    'logo_dir' => 'assets/brands/payersim',
    'url' => 'https://payersim.com',
    'email_from' => 'support@payersim.com',
    'navbar_brand_class' => '',
    'logo_img_class' => '',
    'footer_logo_class' => '',
    'hide_home_sections' => ['blog', 'referral', 'referral_process'],
    'images' => [
        'banner_bg' => 'assets/images/frontend/banner/rebrand-hero-bg.jpg',
        'banner_image' => 'assets/images/frontend/banner/rebrand-hero-phone.jpg',
        'about' => 'assets/images/frontend/about/rebrand-about-story.jpg',
        'about_us_second' => 'assets/images/frontend/about_us_second/rebrand-connect.jpg',
        'about_us_third' => 'assets/images/frontend/about_us_third/rebrand-pricing.jpg',
        'about_us_fourth' => 'assets/images/frontend/about_us_fourth/rebrand-guarantee.jpg',
        'login_register' => 'assets/images/frontend/banner/rebrand-hero-phone.jpg',
    ],
];

$travelpal = [
    'id' => 'travelpal',
    'name' => 'TravelPal',
    'base_color' => '52c3c9',
    'css' => ['travelpal-theme.css'],
    'body_class' => 'theme-travelpal',
    'logo_dir' => 'assets/brands/travelpal',
    'url' => 'https://travelpal.live',
    'email_from' => 'support@travelpal.live',
    'navbar_brand_class' => 'tp-logo tp-logo--header',
    'logo_img_class' => 'tp-header-logo-img',
    'footer_logo_class' => 'tp-logo tp-logo--footer',
    'hide_home_sections' => ['blog', 'referral', 'referral_process'],
    'images' => [
        'banner_bg' => 'assets/images/frontend/banner/travelpal-hero-bg.jpg',
        'banner_image' => 'assets/images/frontend/banner/travelpal-hero-phone.jpg',
        'about' => 'assets/images/frontend/about/travelpal-about-story.jpg',
        'about_us_second' => 'assets/images/frontend/about_us_second/travelpal-connect.jpg',
        'about_us_third' => 'assets/images/frontend/about_us_third/travelpal-pricing.jpg',
        'about_us_fourth' => 'assets/images/frontend/about_us_fourth/travelpal-guarantee.jpg',
        'login_register' => 'assets/images/frontend/login_register/travelpal-auth.png',
    ],
];

return [
    'default' => 'travelsim',

    'catalog' => [
        'travelsim' => $travelsim,
        'payersim' => $payersim,
        'travelpal' => $travelpal,
    ],

    'hosts' => [
        'travelsim.live' => 'travelsim',
        'www.travelsim.live' => 'travelsim',
        'travelsim.test' => 'travelsim',
        'www.travelsim.test' => 'travelsim',

        'payersim.com' => 'payersim',
        'www.payersim.com' => 'payersim',
        'payersim.test' => 'payersim',
        'www.payersim.test' => 'payersim',

        'travelpal.live' => 'travelpal',
        'www.travelpal.live' => 'travelpal',
        'travelpal.test' => 'travelpal',
        'www.travelpal.test' => 'travelpal',
    ],
];
