<?php

use App\Constants\Status;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\DataPlans;
use App\Lib\FileManager;
use App\Lib\GoogleAuthenticator;
use App\Models\Country;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\Language;
use App\Models\Region;
use App\Models\Transaction;
use App\Models\User;
use App\Notify\Notify;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laramin\Utility\VugiChugi;

function systemDetails() {
    $system['name']          = 'esim';
    $system['version']       = '1.0';
    $system['build_version'] = '5.1.13';
    return $system;
}

function slug($string) {
    return Str::slug($string);
}

function verificationCode($length) {
    if ($length == 0) {
        return 0;
    }

    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8) {
    $characters       = '1234567890';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function activeTemplate($asset = false) {
    $template = session('template') ?? gs('active_template');
    if ($asset) {
        return 'assets/templates/' . $template . '/';
    }

    return 'templates.' . $template . '.';
}

function activeTemplateName() {
    $template = session('template') ?? gs('active_template');
    return $template;
}

function siteLogo($type = null) {
    $name = $type ? "/logo_$type.png" : '/logo.png';
    $path = getFilePath('logoIcon') . $name;
    if (file_exists($path)) {
        return asset($path) . '?v=' . filemtime($path);
    }
    return asset('assets/images/default.png');
}
function siteFavicon() {
    $path = getFilePath('logoIcon') . '/favicon.png';
    if (file_exists($path)) {
        return asset($path) . '?v=' . filemtime($path);
    }
    return asset('assets/images/default.png');
}

function loadReCaptcha() {
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003') {
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha() {
    return Captcha::verify();
}

function loadExtension($key) {
    $extension = Extension::where('act', $key)->where('status', Status::ENABLE)->first();
    return $extension ? $extension->generateScript() : '';
}

function getTrx($length = 12) {
    $characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2) {
    $amount = round($amount ?? 0, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false, $currencyFormat = true, $useCredits = false) {
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }
    if ($currencyFormat) {
        // Если useCredits = true, используем "Credits" (для баланса в личном кабинете)
        // Иначе используем реальную валюту (EUR, GBP и т.д.)
        if ($useCredits) {
            $currencyText = 'Credits';
            // Для кредитов не показываем символ валюты, только число и слово "Credits"
            return $printAmount . ' ' . $currencyText;
        } else {
            // Используем реальную валюту из настроек
            $currencyText = gs('cur_text') ?: 'EUR';
            if (gs('currency_format') == Status::CUR_BOTH) {
                return gs('cur_sym') . $printAmount . ' ' . $currencyText;
            } elseif (gs('currency_format') == Status::CUR_TEXT) {
                return $printAmount . ' ' . $currencyText;
            } else {
                return gs('cur_sym') . $printAmount;
            }
        }
    }
    return $printAmount;
}

function removeElement($array, $value) {
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet) {
    return "https://api.qrserver.com/v1/create-qr-code/?data=" . rawurlencode($wallet) . "&size=300x300&ecc=m";
}

/**
 * Save QR code as PNG file for email attachment. Returns temp file path or null on failure.
 * Uses api.qrserver.com; for very long strings (e.g. LPA > ~1.5K) may fail due to URL length.
 */
/**
 * Remove .png from the end of a URL (e.g. https://p.qrsim.net/page.png -> https://p.qrsim.net/page).
 * Used for eSIM QR links in emails and dashboard so links are without .png.
 */
function stripPngFromUrl(?string $url): ?string
{
    if ($url === null || $url === '' || !str_starts_with($url, 'http')) {
        return $url;
    }
    return preg_replace('#\.png(\?|$)#i', '$1', $url);
}

function qrCodeToTempFile(string $data, string $filenamePrefix = 'esim-qr'): ?string
{
    $dir = storage_path('app/temp');
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $path = $dir . '/' . $filenamePrefix . '-' . uniqid() . '.png';
    $url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&ecc=M&data=" . rawurlencode($data);
    if (strlen($url) > 2000) {
        return null;
    }
    $img = @file_get_contents($url);
    if ($img === false || strlen($img) < 100) {
        return null;
    }
    if (@file_put_contents($path, $img) === false) {
        return null;
    }
    return $path;
}

function keyToTitle($text) {
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}

function titleToKey($text) {
    return strtolower(str_replace(' ', '_', $text));
}

function strLimit($title = null, $length = 10) {
    return Str::limit($title, $length);
}

function strPlural(string $title, int $count) {
    return $count . " " . Str::plural($title, $count);
}

function getIpInfo() {
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}

function osBrowser() {
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}

function getTemplates() {
    $param['purchasecode'] = env("PURCHASECODE");
    $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $param['website'] = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '' . $requestUri . ' - ' . env("APP_URL");
    $url                   = VugiChugi::gttmp() . systemDetails()['name'];
    $response              = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}

function getPageSections($arr = false) {
    $jsonUrl  = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}

function getImage($image, $size = null) {
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/default.png');
}

function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true, $pushImage = null, $emailAttachments = null) {
    $globalShortCodes = [
        'site_name'       => gs('site_name'),
        'site_currency'   => gs('cur_text'),
        'currency_symbol' => gs('cur_sym'),
        'site_logo'       => siteLogo(), // full URL for email logo
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify                  = new Notify($sendVia);
    $notify->templateName    = $templateName;
    $notify->shortCodes      = $shortCodes;
    $notify->user            = $user;
    $notify->createLog       = $createLog;
    $notify->pushImage       = $pushImage;
    $notify->emailAttachments = $emailAttachments ?? [];
    $notify->userColumn      = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}

function getPaginate($paginate = null) {
    if (!$paginate) {
        $paginate = gs('paginate_number');
    }
    return $paginate;
}

function paginateLinks($data, $view = null) {
    return $data->appends(request()->all())->links($view);
}

function menuActive($routeName, $type = null, $param = null) {
    if ($type == 3) {
        $class = 'side-menu--open';
    } elseif ($type == 2) {
        $class = 'sidebar-submenu__open';
    } else {
        $class = 'active';
    }

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) {
                return $class;
            }
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(isset(request()->route()->parameters) ? request()->route()->parameters : []);
            if (strtolower(isset($routeParam[0]) ? $routeParam[0] : '') == strtolower($param)) return $class;
            else return;
        }
        return $class;
    }
}

function fileUploader($file, $location, $size = null, $old = null, $thumb = null, $filename = null) {
    $fileManager           = new FileManager($file);
    $fileManager->path     = $location;
    $fileManager->size     = $size;
    $fileManager->old      = $old;
    $fileManager->thumb    = $thumb;
    $fileManager->filename = $filename;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager() {
    return new FileManager();
}

function getFilePath($key) {
    return fileManager()->$key()->path;
}

function getFileSize($key) {
    return fileManager()->$key()->size;
}

function getFileExt($key) {
    return fileManager()->$key()->extensions;
}

function dataPlans() {
    return new DataPlans();
}

function diffForHumans($date) {
    $lang = session()->get('lang');
    if (!$lang) {
        $lang = getDefaultLang();
    }

    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}

function showDateTime($date, $format = 'Y-m-d h:i A') {
    if (!$date) {
        return '-';
    }
    $lang = session()->get('lang');
    if (!$lang) {
        $lang = getDefaultLang();
    }

    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}

function getDefaultLang() {
    return Language::where('is_default', Status::YES)->first()->code ?? 'en';
}

function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false) {

    $templateName = activeTemplateName();
    if ($singleQuery) {
        $content = Frontend::where('tempname', $templateName)->where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::where('tempname', $templateName);
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}

function verifyG2fa($user, $code, $secret = null) {
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode  = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = Status::YES;
        $user->save();
        return true;
    } else {
        return false;
    }
}

function urlPath($routeName, $routeParam = null) {
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path     = str_replace($basePath, '', $url);
    return $path;
}

function showMobileNumber($number) {
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email) {
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}

function getRealIP() {
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(isset($_SERVER['HTTP_FORWARDED']) ? $_SERVER['HTTP_FORWARDED'] : '', FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(isset($_SERVER['HTTP_FORWARDED_FOR']) ? $_SERVER['HTTP_FORWARDED_FOR'] : '', FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '', FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : '', FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : '', FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : '', FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}

function appendQuery($key, $value) {
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b) {
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr) {
    usort($arr, "dateSort");
    return $arr;
}

function gs($key = null) {
   
    $general = GeneralSetting::first();

    if ($key) {
        return $general->$key ?? null;
    }
    
    return $general;
}

/**
 * AlpPay payment state from API (COMPLETED, PENDING, …) or null on error.
 */
function alppayFetchPaymentState(\App\Models\Deposit $deposit): ?string
{
    if (!$deposit->gateway_trx) {
        return null;
    }
    $baseUrl = config('alppay.base_url');
    $apiKey = config('alppay.api_key');
    $shopId = config('alppay.shop_id');
    $headers = [];
    if (!empty($shopId)) {
        $headers['Shop-Id'] = $shopId;
    }
    try {
        $res = Http::withToken($apiKey)
            ->acceptJson()
            ->withHeaders($headers)
            ->get(rtrim($baseUrl, '/') . '/api/v1/payments/' . $deposit->gateway_trx);
        if (!$res->ok()) {
            \Illuminate\Support\Facades\Log::warning('AlpPay GET payment failed', [
                'status' => $res->status(),
                'deposit_trx' => $deposit->trx,
                'gateway_trx' => $deposit->gateway_trx,
                'body_snippet' => substr($res->body(), 0, 500),
            ]);
            return null;
        }
        $json = $res->json();
        $state = data_get($json, 'result.state') ?? data_get($json, 'state') ?? data_get($json, 'data.state');
        return $state ? strtoupper((string) $state) : null;
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('AlpPay GET payment exception', [
            'deposit_trx' => $deposit->trx,
            'error' => $e->getMessage(),
        ]);
        return null;
    }
}

/**
 * Poll AlpPay for pending wallet deposits and credit balance if payment completed (webhook fallback).
 */
function finalizePendingAlpPayDepositsForUser(?\App\Models\User $user): void
{
    if (!$user) {
        return;
    }
    $deposits = \App\Models\Deposit::where('user_id', $user->id)
        ->where('order_id', 0)
        ->whereIn('status', [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])
        ->whereNotNull('gateway_trx')
        ->orderByDesc('id')
        ->limit(5)
        ->get();
    foreach ($deposits as $deposit) {
        $state = alppayFetchPaymentState($deposit);
        if ($state !== 'COMPLETED') {
            continue;
        }
        try {
            \App\Http\Controllers\Gateway\PaymentController::userDataUpdate($deposit->fresh());
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('finalizePendingAlpPayDeposits: failed', [
                'trx' => $deposit->trx,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

function getCachedCountries() {
    return Cache::rememberForever('active_countries_with_plans', function () {
        return Country::with('plans.region')->active()->get();
    });
}

function isImage($string) {
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
    $fileExtension     = pathinfo($string, PATHINFO_EXTENSION);
    if (in_array($fileExtension, $allowedExtensions)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Get currency rate from database
 */
function getCurrencyRate($currencyCode) {
    $currency = \App\Models\Currency::where('api_currency', $currencyCode)->first();
    return $currency ? (float)$currency->conversion_rate : 1.0;
}

/**
 * Convert currency amount
 */
function convertCurrency($amount, $fromCurrency, $toCurrency) {
    if ($fromCurrency === $toCurrency) {
        return $amount;
    }
    
    // Get rates
    $fromRate = getCurrencyRate($fromCurrency);
    $toRate = getCurrencyRate($toCurrency);
    
    // Convert: amount * (toRate / fromRate)
    if ($fromRate > 0) {
        return $amount * ($toRate / $fromRate);
    }
    
    return $amount;
}

function isHtml($string) {
    if (preg_match('/<.*?>/', $string)) {
        return true;
    } else {
        return false;
    }
}

function convertToReadableSize($size) {
    preg_match('/^(\d+)([KMG])$/', $size, $matches);
    $size = (int) $matches[1];
    $unit = $matches[2];

    if ($unit == 'G') {
        return $size . 'GB';
    }

    if ($unit == 'M') {
        return $size . 'MB';
    }

    if ($unit == 'K') {
        return $size . 'KB';
    }

    return $size . $unit;
}

function frontendImage($sectionName, $image, $size = null, $seo = false) {
    if ($seo) {
        return getImage('assets/images/frontend/' . $sectionName . '/seo/' . $image, $size);
    }
    return getImage('assets/images/frontend/' . $sectionName . '/' . $image, $size);
}

function buildResponse($remark, $status, $notify, $data = null) {
    $response = [
        'remark' => $remark,
        'status' => $status,
    ];
    $message = [];
    if ($notify instanceof \Illuminate\Support\MessageBag) {
        $message['error'] = collect($notify)->map(function ($item) {
            return $item[0];
        })->values()->toArray();
    } else {
        $message = [$status => collect($notify)->map(function ($item) {
            if (is_string($item)) {
                return $item;
            }
            if (count($item) > 1) {
                return $item[1];
            }
            return $item[0];
        })->toArray()];
    }
    $response['message'] = $message;
    if ($data) {
        $response['data'] = $data;
    }
    return response()->json($response);
}

function responseSuccess($remark, $notify, $data = null) {
    return buildResponse($remark, 'success', $notify, $data);
}

function responseError($remark, $notify, $data = null) {
    return buildResponse($remark, 'error', $notify, $data);
}

function userReferralCommission($user) {
    $referrer       = User::active()->find($user->ref_by);
    $referralAmount = gs('referral_amount');

    if (!$referrer || $referralAmount <= 0) {
        return false;
    }

    $referrer->balance += $referralAmount;
    $referrer->save();

    $transaction               = new Transaction();
    $transaction->user_id      = $referrer->id;
    $transaction->order_id     = 0;
    $transaction->amount       = $referralAmount;
    $transaction->post_balance = $referrer->balance;
    $transaction->charge       = 0;
    $transaction->trx_type     = '+';
    $transaction->trx          = getTrx();
    $transaction->remark       = 'referral_commission';
    $transaction->details      = 'Referral Commission';
    $transaction->save();

    notify($referrer, 'REFERRAL_COMMISSION', [
        'amount'       => showAmount($referralAmount, currencyFormat: false),
        'user'         => $user->username,
        'trx'          => $transaction->trx,
        'remark'       => $transaction->remark,
        'post_balance' => showAmount($referrer->balance, currencyFormat: false),
    ]);

    return true;
}

function showPlanCapacity($plan) {
    return $plan->capacity < 0 ? __('Unlimited') : $plan->capacity . ' ' . $plan->capacity_unit;
}

/**
 * Price shown to customers and charged at checkout (EUR base before GBP/USD conversion).
 */
function planCustomerPrice($plan): float
{
    if (!$plan) {
        return 0.0;
    }

    $base = (float) ($plan->price ?? 0);
    if ($base < 0.01) {
        $base = (float) ($plan->retail_price ?? 0);
    }

    $divisor = (float) config('plans.customer_price_divisor', 4);
    if ($divisor <= 1) {
        return round($base, 2);
    }

    return round($base / $divisor, 2);
}

function getRegions(array $exclude = [], array $only = []) {
    $query = Region::active();

    if ($exclude) {
        $query->whereNotIn('name', $exclude);
    }
    if ($only) {
        $query->whereIn('name', $only);
    }
    return $query->whereHas('plans', fn($q) =>
    $q->active()->withPositivePrice()->whereHas('countries', fn($country) => $country->active()))
        ->with(['plans' => fn($q) => $q->active()->withPositivePrice()->with('countries')])
        ->get()
        ->map(function ($region) {
            $validPlans = $region->plans->filter(fn($p) => $p->countries->isNotEmpty() && planCustomerPrice($p) >= 0.01);
            $plan       = $validPlans->sortBy('converted_price')->first();

            return [
                'id'              => $region->id,
                'name'            => $region->name,
                'image'           => $region->region_image,
                'slug'            => $region->slug,
                'converted_price' => $plan?->converted_price,
                'plan_count'      => $validPlans->count(),
            ];
        })
        ->sortBy('name')
        ->values();
}

/**
 * Canonical region buckets and code mapping
 */
function canonicalRegionBuckets(): array {
    return [
        'Africa'       => ['AF', 'AF-', 'AFRICA'],
        'Asia'         => ['AS', 'AP', 'ASIA', 'AS-', 'ME-'],
        'Europe'       => ['EU', 'EU-', 'EUROPE'],
        'North America'=> ['NA', 'NA-', 'NORTH-AMERICA', 'CB-'],
        'South America'=> ['SA', 'SA-', 'SOUTH-AMERICA', 'LATAM'],
        'Oceania'      => ['AU', 'OCEANIA', 'OC', 'OC-'],
    ];
}

function mapRegionNameBySlug(?string $slug, ?string $name): ?string {
    if (!$slug && !$name) return null;
    $slugUp = strtoupper((string)$slug);
    $nameUp = strtoupper((string)$name);

    // Сначала проверяем по точным совпадениям в названиях
    if (str_contains($nameUp, 'EUROPE') || str_contains($nameUp, 'EU-')) return 'Europe';
    if (str_contains($nameUp, 'ASIA') || str_contains($nameUp, 'AS-')) return 'Asia';
    if (str_contains($nameUp, 'NORTH AMERICA') || str_contains($nameUp, 'NA-') || str_contains($nameUp, 'USA & CANADA')) return 'North America';
    if (str_contains($nameUp, 'SOUTH AMERICA') || str_contains($nameUp, 'SA-')) return 'South America';
    if (str_contains($nameUp, 'AFRICA') || str_contains($nameUp, 'AF-')) return 'Africa';
    if (str_contains($nameUp, 'AUSTRALIA') || str_contains($nameUp, 'OCEANIA') || str_contains($nameUp, 'AU')) return 'Oceania';
    
    // Проверяем по slug кодам
    foreach (canonicalRegionBuckets() as $bucket => $codes) {
        foreach ($codes as $code) {
            if (str_contains($slugUp, $code)) {
                return $bucket;
            }
        }
    }

    // Fallback по ключевым словам
    if (str_contains($nameUp, 'EURO')) return 'Europe';
    if (str_contains($nameUp, 'ASIA')) return 'Asia';
    if (str_contains($nameUp, 'NORTH') && str_contains($nameUp, 'AMERICA')) return 'North America';
    if (str_contains($nameUp, 'SOUTH') && str_contains($nameUp, 'AMERICA')) return 'South America';
    if (str_contains($nameUp, 'AMERICA') || str_contains($nameUp, 'LATAM') || str_contains($nameUp, 'CARIB')) return 'North America';
    if (str_contains($nameUp, 'AFRICA')) return 'Africa';
    if (str_contains($nameUp, 'AUSTRAL') || str_contains($nameUp, 'OCEAN')) return 'Oceania';
    
    // Global regions не попадают ни в одну группу
    if (str_contains($nameUp, 'GLOBAL')) return null;

    return null;
}

/**
 * Compute grouped regions for Regional tab using canonical buckets.
 */
function getCanonicalRegionsForFrontend(): array {
    $regions = Region::active()->with(['plans' => function($q){
        $q->active()->withPositivePrice()->with('countries');
    }])->get();

    $buckets = [
        'Africa' => [], 'Asia' => [], 'Europe' => [], 'North America' => [], 'South America' => [], 'Oceania' => []
    ];

    foreach ($regions as $region) {
        $bucket = mapRegionNameBySlug($region->slug, $region->name);
        if (!isset($buckets[$bucket])) continue;
        
        $validPlans = $region->plans->filter(fn($p) => $p->countries->isNotEmpty() && planCustomerPrice($p) >= 0.01);
        if ($validPlans->isEmpty()) continue;
        
        // Собираем уникальные страны из всех планов региона
        $countries = collect();
        foreach ($validPlans as $plan) {
            $countries = $countries->merge($plan->countries);
        }
        $uniqueCountries = $countries->unique('id');
        
        $buckets[$bucket][] = [
            'id' => $region->id,
            'name' => $region->name,
            'slug' => $region->slug,
            'plan_count' => $validPlans->count(),
            'countries' => $uniqueCountries,
        ];
    }

    // Создаем континенты только для непустых групп
    $result = [];
    foreach ($buckets as $bucketName => $items) {
        if (!empty($items)) {
            $totalPlans = collect($items)->sum('plan_count');
            $totalCountries = collect($items)->flatMap->countries->unique('id')->count();
            
            $result[] = [
                'id' => 0,
                'name' => $bucketName,
                'image' => null,
                'slug' => strtolower(str_replace(' ', '-', $bucketName)),
                'plan_count' => $totalPlans,
                'area_count' => $totalCountries,
            ];
        }
    }

    return $result;
}

/**
 * Identify global plans by coverage breadth (plans linked to many countries across multiple buckets).
 */
function getGlobalRegionsForFrontend(): array {
    $plans = \App\Models\Plan::active()->withPositivePrice()->with('countries', 'region', 'currency')->get();
    $globalCandidates = $plans->filter(function($p){
        if (planCustomerPrice($p) < 0.01) return false;
        $countryCodes = $p->countries->pluck('code')->all();
        $num = count($countryCodes);
        if ($num < 20) return false; // heuristic
        $bucketsHit = collect($p->countries)->map(function($c){
            return mapRegionNameBySlug($c->region?->slug, $c->region?->name);
        })->filter()->unique()->count();
        return $bucketsHit >= 3; // spans at least 3 buckets
    });

    if ($globalCandidates->isEmpty()) return [];

    // Represent as single Global group
    $minPrice = $globalCandidates->map(fn($p) => $p->converted_price)->filter()->min();
    return [[
        'id' => 0,
        'name' => 'Global',
        'image' => null,
        'slug' => 'global',
        'converted_price' => $minPrice,
        'plan_count' => $globalCandidates->count(),
    ]];
}

function getCountries() {
    return Country::active()->where('is_featured', Status::ENABLE)
        ->whereHas('plans', function ($query) {
            $query->active()->withPositivePrice()->whereHas('region', fn($q) => $q->where('status', Status::ENABLE));
        })
        ->with(['plans' => function ($query) {
            $query
                ->active()
                ->withPositivePrice()
                ->whereHas('region', fn($q) => $q->where('status', Status::ENABLE));
        }])
        ->get()
        ->filter(fn($country) => $country->plans->isNotEmpty())
        ->map(function ($country) {
            $plan = $country->plans->sortBy('converted_price')->first();
            return [
                'id'              => $country->id,
                'code'            => $country->code,
                'name'            => $country->name,
                'country_image'   => $country->image,
                'slug'            => $country->slug,
                'converted_price' => $plan?->converted_price,
            ];
        })
        ->sortBy('name')
        ->values();
}
