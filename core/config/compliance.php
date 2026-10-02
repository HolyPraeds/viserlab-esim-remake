<?php

return [
    // Destinations Stripe / OFAC / card networks treat as prohibited or high-risk
    // for an eSIM shop. Keep South Korea (KR) — it is not North Korea (KP).
    'stripe_blocked_country_codes' => [
        'CU', // Cuba — Stripe / OFAC
        'IR', // Iran
        'KP', // North Korea
        'SY', // Syria
        'RU', // Russia — card networks / OFAC / website scan
        'BY', // Belarus
        'AF', // Afghanistan
        'LY', // Libya
        'SD', // Sudan
        'YE', // Yemen
        'MM', // Myanmar
        'VE', // Venezuela
        'IQ', // Iraq
        'UA', // Ukraine — Crimea / DNR / LNR sanctions; hide destination
        'CD', // DR Congo
        'ZW', // Zimbabwe
        'SS', // South Sudan
        'SO', // Somalia
        'NI', // Nicaragua — Stripe high-risk
        'CI', // Côte d'Ivoire — Stripe delivery / high-risk
        'LR', // Liberia — Stripe delivery / high-risk
    ],
];
