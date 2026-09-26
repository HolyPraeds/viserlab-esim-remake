<?php

return [
    /*
    | Customer-facing prices = API retail_price / this divisor (default: 4 → prices 4× lower).
    | Set PLAN_PRICE_DIVISOR=1 in .env to disable.
    */
    'customer_price_divisor' => max(1, (float) env('PLAN_PRICE_DIVISOR', 4)),
];
