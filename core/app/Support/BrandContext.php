<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class BrandContext
{
    public static function default(): array
    {
        return self::find((string) config('brands.default', 'travelsim')) ?? [
            'id' => 'travelsim',
            'name' => 'TravelSim',
            'base_color' => '1a8fe3',
            'css' => [],
            'body_class' => '',
            'logo_dir' => 'assets/brands/travelsim',
            'url' => 'https://travelsim.live',
            'email_from' => 'info@travelsim.live',
            'navbar_brand_class' => '',
            'logo_img_class' => '',
        ];
    }

    public static function find(?string $id): ?array
    {
        if (!$id) {
            return null;
        }

        $brand = config('brands.catalog.' . $id);

        return is_array($brand) ? $brand : null;
    }

    public static function current(): array
    {
        return app()->bound('currentBrand') ? app('currentBrand') : self::default();
    }

    public static function id(): string
    {
        return (string) (self::current()['id'] ?? 'travelsim');
    }

    public static function name(?string $id = null): string
    {
        $brand = $id ? (self::find($id) ?? self::default()) : self::current();

        return (string) ($brand['name'] ?? $id ?? 'TravelSim');
    }

    public static function isKnownHost(?string $host): bool
    {
        return self::idFromHost($host) !== null;
    }

    public static function idFromHost(?string $host): ?string
    {
        $host = strtolower(trim((string) $host));
        if ($host === '') {
            return null;
        }

        $map = config('brands.hosts', []);
        if (isset($map[$host])) {
            return $map[$host];
        }

        $bare = preg_replace('/^www\./', '', $host);

        return $map[$bare] ?? null;
    }

    public static function resolve(?Request $request = null): array
    {
        $forced = trim((string) env('SITE_BRAND', ''));
        if ($forced !== '' && ($brand = self::find($forced))) {
            return $brand;
        }

        $request = $request ?: (app()->bound('request') ? request() : null);
        if ($request && ($id = self::idFromHost($request->getHost()))) {
            return self::find($id) ?? self::default();
        }

        return self::default();
    }

    public static function apply(array $brand, bool $forceOverlay = false): void
    {
        app()->instance('currentBrand', $brand);
        app()->instance('brandForceOverlay', $forceOverlay);
    }

    public static function applyRequestUrl(Request $request): void
    {
        if (!self::isKnownHost($request->getHost())) {
            return;
        }

        $root = $request->getSchemeAndHttpHost();
        config(['app.url' => $root]);
        URL::forceRootUrl($root);
    }

    public static function applyCanonicalUrl(array $brand): void
    {
        if (empty($brand['url'])) {
            return;
        }

        config(['app.url' => $brand['url']]);
        URL::forceRootUrl($brand['url']);
    }

    public static function shouldOverlay(): bool
    {
        if (app()->bound('brandForceOverlay') && app('brandForceOverlay')) {
            return true;
        }

        if (!app()->bound('request')) {
            return false;
        }

        $request = request();

        return !$request->is('admin') && !$request->is('admin/*');
    }

    public static function using(?string $brandId, callable $callback): mixed
    {
        $previousBrand = app()->bound('currentBrand') ? app('currentBrand') : null;
        $previousOverlay = app()->bound('brandForceOverlay') ? app('brandForceOverlay') : false;
        $previousUrl = config('app.url');

        $brand = $brandId ? (self::find($brandId) ?? self::default()) : self::default();
        self::apply($brand, true);
        self::applyCanonicalUrl($brand);

        try {
            return $callback();
        } finally {
            if ($previousBrand) {
                self::apply($previousBrand, (bool) $previousOverlay);
            } else {
                self::apply(self::default(), false);
            }
            config(['app.url' => $previousUrl]);
            URL::forceRootUrl($previousUrl ?: null);
        }
    }
}
