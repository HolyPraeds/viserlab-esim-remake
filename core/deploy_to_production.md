# Инструкция по развертыванию политик на продакшн сервере

## 1. Файлы для обновления

### Шаблоны (заменить на сервере):
- `core/resources/views/templates/basic/policy.blade.php`
- `core/resources/views/templates/basic/policy_styles.blade.php` (новый файл)
- `core/resources/views/templates/basic/layouts/app.blade.php`

### Контроллер:
- `core/app/Http/Controllers/SiteController.php`

## 2. Скрипт для обновления базы данных

Создайте файл `update_policies_production.php` на сервере:

```php
<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Updating policies for production...\n\n";

// Удаляем старые записи
DB::statement("DELETE FROM frontends WHERE data_keys = 'policy_pages.element'");
echo "✅ Deleted old policy records\n";

// Создаем новые записи с правильными заголовками и контентом
$policies = [
    [
        'data_keys' => 'policy_pages.element',
        'data_values' => json_encode([
            'title' => 'Privacy Policy',
            'content' => '',
            'details' => '<!-- Privacy Policy Content -->'
        ]),
        'slug' => 'privacy-policy'
    ],
    [
        'data_keys' => 'policy_pages.element', 
        'data_values' => json_encode([
            'title' => 'Terms of Service',
            'content' => '',
            'details' => '<!-- Terms of Service Content -->'
        ]),
        'slug' => 'terms-of-service'
    ],
    [
        'data_keys' => 'policy_pages.element',
        'data_values' => json_encode([
            'title' => 'Cookies Policy', 
            'content' => '',
            'details' => '<!-- Cookies Policy Content -->'
        ]),
        'slug' => 'cookies-policy'
    ]
];

foreach($policies as $policy) {
    DB::table('frontends')->insert($policy);
    echo "✅ Created policy: {$policy['slug']}\n";
}

echo "\n✅ All policies updated successfully!\n";
```

## 3. Команды для выполнения на сервере

```bash
# 1. Обновить файлы через FTP/SSH
# 2. Выполнить скрипт обновления БД
php update_policies_production.php

# 3. Очистить кеш
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# 4. Проверить результат
curl http://yourdomain.com/policy/privacy-policy
```

## 4. Проверка

После развертывания проверьте:
- [ ] Страницы политик загружаются без ошибок
- [ ] Дизайн применяется корректно
- [ ] Ссылки в форме регистрации работают
- [ ] Контент отображается правильно

## 5. Резервное копирование

Перед обновлением сделайте бэкап:
```bash
# Бэкап базы данных
mysqldump -u username -p database_name > backup_before_policies.sql

# Бэкап файлов
tar -czf backup_files.tar.gz core/resources/views/templates/basic/
```

