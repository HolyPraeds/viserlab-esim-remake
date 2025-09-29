 <?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Frontend;
use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "    РАЗВЕРТЫВАНИЕ НА ПРОДАКШН СЕРВЕРЕ\n";
echo "========================================\n\n";

// 1. Устанавливаем EUR как базовую валюту
echo "1. Настройка валюты...\n";
DB::table('general_settings')->where('id', 1)->update([
    'cur_text' => 'EUR',
    'cur_sym' => '€',
    'currency_format' => 1
]);
echo "✅ EUR установлен как базовая валюта\n\n";

// 2. Устанавливаем курсы валют
echo "2. Настройка курсов валют...\n";
DB::table('currencies')->updateOrInsert(
    ['api_currency' => 'USD'],
    ['conversion_rate' => '1.085', 'updated_at' => now()]
);
DB::table('currencies')->updateOrInsert(
    ['api_currency' => 'EUR'],
    ['conversion_rate' => '1.0', 'updated_at' => now()]
);
echo "✅ Курсы установлены (1 EUR = 1.085 USD)\n\n";

// 3. Конвертируем все USD планы в EUR
echo "3. Конвертация планов в EUR...\n";
$usdPlans = DB::table('plans')->where('price_currency', 'USD')->get();
$conversionRate = 1.085;
$convertedCount = 0;

foreach($usdPlans as $plan) {
    $eurPrice = $plan->retail_price / $conversionRate;
    
    DB::table('plans')
        ->where('id', $plan->id)
        ->update([
            'retail_price' => round($eurPrice, 2),
            'price_currency' => 'EUR',
            'updated_at' => now()
        ]);
    
    $convertedCount++;
    if ($convertedCount % 200 == 0) {
        echo "   Обработано планов: {$convertedCount}\n";
    }
}

echo "✅ Конвертировано {$convertedCount} планов из USD в EUR\n\n";

// 4. Исправляем политики
echo "4. Исправление политик...\n";
DB::table('frontends')->where('data_keys', 'policy_pages.element')->delete();

$policySlugs = [
    'privacy-policy' => [
        'title' => 'Privacy Policy',
        'content' => '<h3>Privacy Policy</h3><p>We respect your privacy and are committed to protecting your personal data. This privacy policy will inform you about how we look after your personal data when you visit our website and tell you about your privacy rights and how the law protects you.</p><h4>Information We Collect</h4><p>We may collect, use, store and transfer different kinds of personal data about you including identity data, contact data, technical data, usage data, and marketing data.</p><h4>How We Use Your Information</h4><p>We will only use your personal data when the law allows us to. Most commonly, we will use your personal data to provide our services, improve our website, and communicate with you.</p><h4>Data Security</h4><p>We have put in place appropriate security measures to prevent your personal data from being accidentally lost, used or accessed in an unauthorized way, altered or disclosed.</p>'
    ],
    'terms-of-service' => [
        'title' => 'Terms of Service',
        'content' => '<h3>Terms of Service</h3><p>These terms and conditions outline the rules and regulations for the use of our website and services.</p><h4>Acceptance of Terms</h4><p>By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement.</p><h4>Use License</h4><p>Permission is granted to temporarily download one copy of the materials on our website for personal, non-commercial transitory viewing only.</p><h4>Disclaimer</h4><p>The materials on our website are provided on an "as is" basis. We make no warranties, expressed or implied, and hereby disclaim and negate all other warranties including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p>'
    ],
    'cookies-policy' => [
        'title' => 'Cookies Policy',

'content' => '<h3>Cookies Policy</h3><p>This website uses cookies to improve your experience while you navigate through the website. Out of these cookies, the cookies that are categorized as necessary are stored on your browser as they are essential for the working of basic functionalities of the website.</p><h4>What Are Cookies</h4><p>Cookies are small text files that are placed on your computer by websites that you visit. They are widely used in order to make websites work, or work more efficiently, as well as to provide information to the owners of the site.</p><h4>How We Use Cookies</h4><p>We use cookies to understand how you use our site and to improve your experience. This includes personalizing content and advertising, and analyzing our traffic.</p>'
    ]
];

foreach ($policySlugs as $slug => $data) {
    $policy = Frontend::firstOrNew(['slug' => $slug, 'data_keys' => 'policy_pages.element']);
    $policy->data_values = [
        'title' => $data['title'],
        'content' => '',
        'details' => $data['content'],
    ];
    $policy->save();
}
echo "✅ Политики исправлены\n\n";

// 5. Очищаем все кеши
echo "5. Очистка кешей...\n";

// Удаляем файлы кеша
$cacheFiles = [
    'bootstrap/cache/packages.php',
    'bootstrap/cache/services.php',
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes.php'
];

foreach($cacheFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
    }
}

// Очистка через Artisan
shell_exec('php artisan cache:clear 2>&1');
shell_exec('php artisan view:clear 2>&1');
shell_exec('php artisan config:clear 2>&1');
shell_exec('php artisan route:clear 2>&1');

echo "✅ Все кеши очищены\n\n";

// 6. Проверяем результат
echo "6. Проверка результата...\n";
$settings = DB::table('general_settings')->where('id', 1)->first();
echo "Базовая валюта: {$settings->cur_text}\n";
echo "Символ валюты: {$settings->cur_sym}\n\n";

$eurPlansCount = DB::table('plans')->where('price_currency', 'EUR')->count();
$usdPlansCount = DB::table('plans')->where('price_currency', 'USD')->count();
echo "Планов в EUR: {$eurPlansCount}\n";
echo "Планов в USD: {$usdPlansCount}\n\n";

$policiesCount = DB::table('frontends')->where('data_keys', 'policy_pages.element')->count();
echo "Политик создано: {$policiesCount}\n\n";

// 7. Показываем примеры
echo "7. Примеры конвертированных цен:\n";
$samplePlans = DB::table('plans')->where('price_currency', 'EUR')->limit(3)->get();
foreach($samplePlans as $plan) {
    echo "   {$plan->name}: {$plan->retail_price} EUR\n";
}

echo "\n========================================\n";
echo "           РАЗВЕРТЫВАНИЕ ЗАВЕРШЕНО!\n";
echo "========================================\n";
echo "✅ Все цены теперь отображаются в EUR\n";
echo "✅ Политики исправлены\n";
echo "✅ Все кеши очищены\n\n";
echo "Проверьте ваш сайт - все должно работать правильно!\n";
echo "Если цены все еще показываются в USD, очистите кеш браузера (Ctrl+F5)\n";