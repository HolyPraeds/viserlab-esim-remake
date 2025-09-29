<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Country;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Region;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

// Import helper functions
if (!function_exists('mapRegionNameBySlug')) {
    require_once app_path('Http/Helpers/helpers.php');
}

class SiteController extends Controller {
    public function index() {
        if (isset($_GET['reference'])) {
            session()->put('reference', $_GET['reference']);
        }

        $pageTitle     = 'Home';
        $sections      = Page::where('tempname', activeTemplate())->where('slug', '/')->first();
        $seoContents   = $sections->seo_content;
        $seoImage      = isset($seoContents->image) ? getImage(getFilePath('seo') . '/' . $seoContents->image, getFileSize('seo')) : null;
        return view('Template::home', compact('pageTitle', 'sections', 'seoContents', 'seoImage'));
    }

    public function destination() {
        $pageTitle = 'Destination';
        $sections  = Page::where('tempname', activeTemplate())->where('slug', 'destination')->first();

        $countries = Country::active()
            ->with(['plans' => function ($query) {
                $query
                    ->active()
                    ->whereHas('region', fn($q) => $q->where('status', Status::ENABLE))
                    ->with('region');
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
                    'region_slug'     => $plan?->region?->slug,
                    'region_name'     => $plan?->region?->name,
                ];
            })
            ->sortBy('name')
            ->values();

        // Group countries by canonical continents
        $continentsOrder = ['Africa','Asia','Europe','North America','South America','Oceania'];
        $countriesByContinent = [];
        foreach ($continentsOrder as $c) { $countriesByContinent[$c] = []; }
        foreach ($countries as $c) {
            $bucket = mapRegionNameBySlug($c['region_slug'] ?? null, $c['region_name'] ?? null) ?? 'Other';
            if (!isset($countriesByContinent[$bucket])) continue;
            $countriesByContinent[$bucket][] = $c;
        }

        // Use canonical buckets for Regional, and heuristic for Global (kept for other pages, not used here)
        $regions = getCanonicalRegionsForFrontend();
        $globalRegions = getGlobalRegionsForFrontend();

        return view('Template::destination', compact('pageTitle', 'countries', 'sections', 'regions', 'globalRegions', 'countriesByContinent', 'continentsOrder'));
    }

    public function countryPlans($slug) {
        $country = Country::with(['plans' => function ($query) {
            $query
                ->active()
                ->whereHas('region', fn($q) => $q->where('status', Status::ENABLE));
        }])
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $pageTitle = $country->country_name . ' eSIM Plans';
        return view('Template::country_plans', compact('pageTitle', 'country'));
    }

    public function regionPlans($slug) {
        $region = Region::active()->with(['plans' => function ($query) {
            $query
                ->active()
                ->whereHas('countries', fn($q) => $q->active());
        }])->active()->where('slug', $slug)->firstOrFail();

        $pageTitle = $region->name . ' eSIM Plans';

        $plans = $this->convertPlanPrice($region->plans);

        return view('Template::region_plans', compact('pageTitle', 'region', 'plans'));
    }

    public function regionCountries($slug) {
        // Определяем континент по slug
        $continentMap = [
            'asia' => 'Asia',
            'europe' => 'Europe',
            'north-america' => 'North America',
            'south-america' => 'South America',
            'africa' => 'Africa',
            'oceania' => 'Oceania'
        ];

        $continent = $continentMap[$slug] ?? null;
        if (!$continent) {
            abort(404);
        }

        // Получаем все регионы этого континента
        $regions = Region::active()->get()->filter(function($region) use ($continent) {
            $bucket = mapRegionNameBySlug($region->slug, $region->name);
            return $bucket === $continent;
        });

        // Получаем страны из всех регионов континента
        $countries = Country::active()
            ->whereHas('plans', function($query) use ($regions) {
                $query->whereIn('region_id', $regions->pluck('id'));
            })
            ->with(['plans' => function ($query) use ($regions) {
                $query->whereIn('region_id', $regions->pluck('id'));
            }])
            ->get()
            ->filter(fn($country) => $country->plans->isNotEmpty())
            ->map(function ($country) {
                $plan = $country->plans->sortBy('retail_price')->first();
                return [
                    'id'              => $country->id,
                    'code'            => $country->code,
                    'name'            => $country->name,
                    'country_image'   => $country->image,
                    'slug'            => $country->slug,
                    'retail_price'    => $plan?->retail_price,
                    'price_currency'  => $plan?->price_currency,
                ];
            })
            ->sortBy('name')
            ->values();

        $pageTitle = $continent . ' Countries';
        $sections = Page::where('tempname', activeTemplate())->where('slug', 'destination')->first();

        return view('Template::region_countries', compact('pageTitle', 'continent', 'countries', 'sections'));
    }

    private function convertPlanPrice($plans) {
        $baseCurrency = gs('cur_text');

        return $plans->map(function ($plan) use ($baseCurrency) {
            $convertedPrice = $plan->retail_price;

            if ($plan->currency && $plan->currency->conversion_rate > 0) {
                if ($plan->price_currency !== $baseCurrency) {
                    $convertedPrice = $plan->retail_price / $plan->currency->conversion_rate;
                }
            }

            $plan->converted_price = $convertedPrice;
            return $plan;
        });
    }

    public function searchCountry(Request $request) {
        $keyword = trim((string) $request->keyword);
        if (strlen($keyword) < 2) {
            return response()->json(['html' => '']);
        }

        $countries = Country::active()
            ->where(function($q) use ($keyword){
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('code', 'like', "%{$keyword}%");
            })
            ->whereHas('plans', function ($query) {
                $query->active()->whereHas('region', fn($r) => $r->where('status', Status::ENABLE));
            })
            ->select(['id','name','slug','code','image'])
            ->orderBy('name')
            ->limit(20)
            ->get();

        $html = view('Template::partials.country_result', ['countries' => $countries])->render();

        return response()->json(['html' => $html]);
    }

    public function pages($slug) {
        $page        = Page::where('tempname', activeTemplate())->where('slug', $slug)->firstOrFail();
        $pageTitle   = $page->name;
        $sections    = $page->secs;
        $seoContents = $page->seo_content;
        $seoImage    = isset($seoContents->image) ? getImage(getFilePath('seo') . '/' . $seoContents->image, getFileSize('seo')) : null;
        return view('Template::pages', compact('pageTitle', 'sections', 'seoContents', 'seoImage'));
    }

    public function contact() {
        $pageTitle   = "Contact Us";
        $user        = auth()->user();
        $sections    = Page::where('tempname', activeTemplate())->where('slug', 'contact')->first();
        $seoContents = $sections->seo_content;
        $seoImage    = isset($seoContents->image) ? getImage(getFilePath('seo') . '/' . $seoContents->image, getFileSize('seo')) : null;
        return view('Template::contact', compact('pageTitle', 'user', 'sections', 'seoContents', 'seoImage'));
    }

    public function contactSubmit(Request $request) {
        $request->validate([
            'name'    => 'required',
            'email'   => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $random = getNumber();

        $ticket           = new SupportTicket();
        $ticket->user_id  = auth()->id() ?? 0;
        $ticket->name     = $request->name;
        $ticket->email    = $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;

        $ticket->ticket     = $random;
        $ticket->subject    = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status     = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title     = 'A new contact message has been submitted';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message                    = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message           = $request->message;
        $message->save();

        $notify[] = ['success', 'Your message has been sent'];
        return back()->withNotify($notify);
    }

    public function policyPages($slug) {
        $policy = Frontend::where('slug', $slug)->where('data_keys', 'policy_pages.element')->first();
        if ($policy) {
            $pageTitle   = $policy->data_values->title;
            $seoContents = $policy->seo_content;
            $seoImage    = isset($seoContents->image) ? frontendImage('policy_pages', $seoContents->image, getFileSize('seo'), true) : null;
            return view('Template::policy', compact('policy', 'pageTitle', 'seoContents', 'seoImage'));
        }

        // Fallback to static policies bundled in the template
        $map = [
            'terms-and-conditions' => 'terms',
            'privacy-policy'       => 'privacy',
            'cookies-policy'       => 'cookies',
            'refund-policy'        => 'refund',
            'disclosure-disclaimer'=> 'disclaimer',
        ];
        if (isset($map[$slug])) {
            $view = 'Template::policies.' . $map[$slug];
            abort_unless(view()->exists($view), 404);
            $pageTitle = ucwords(str_replace('-', ' ', $slug));
            return view($view, compact('pageTitle'));
        }

        abort(404);
    }

    public function changeLanguage($lang = null) {
        $language = Language::where('code', $lang)->first();
        if (!$language) {
            $lang = 'en';
        }

        session()->put('lang', $lang);
        return back();
    }

    public function blogs() {
        $pageTitle   = 'Blogs';
        $blogs       = Frontend::where('data_keys', 'blog.element')->latest()->paginate(getPaginate(9));
        $sections    = Page::where('tempname', activeTemplate())->where('slug', 'blog')->first();
        $seoContents = $sections->seo_content;
        $seoImage    = isset($seoContents->image) ? frontendImage('blog', $seoContents->image, getFileSize('seo'), true) : null;
        return view('Template::blogs', compact('pageTitle', 'blogs', 'sections', 'seoContents', 'seoImage'));
    }

    public function blogDetails($slug) {
        $blog        = Frontend::where('slug', $slug)->where('data_keys', 'blog.element')->firstOrFail();
        $latest      = Frontend::where('data_keys', 'blog.element')->where('id', '!=', $blog->id)->orderBy('id', 'DESC')->limit(5)->get();
        $pageTitle   = 'Blog Details';
        $seoContents = $blog->seo_content;
        $seoImage    = isset($seoContents->image) ? frontendImage('blog', $seoContents->image, getFileSize('seo'), true) : null;
        return view('Template::blog_details', compact('blog', 'pageTitle', 'seoContents', 'seoImage', 'latest'));
    }

    public function cookieAccept() {
        Cookie::queue('gdpr_cookie', gs('site_name'), 43200);
    }

    public function cookiePolicy() {
        $cookieContent = Frontend::where('data_keys', 'cookie.data')->first();
        abort_if($cookieContent->data_values->status != Status::ENABLE, 404);
        $pageTitle = 'Cookie Policy';
        $cookie    = Frontend::where('data_keys', 'cookie.data')->first();
        return view('Template::cookie', compact('pageTitle', 'cookie'));
    }

    public function placeholderImage($size = null) {
        $imgWidth  = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text      = $imgWidth . '×' . $imgHeight;
        $fontFile  = realpath('assets/font/solaimanLipi_bold.ttf');
        $fontSize  = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgFill);
        $textBox    = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance() {
        $pageTitle = 'Maintenance Mode';
        if (gs('maintenance_mode') == Status::DISABLE) {
            return to_route('home');
        }
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        return view('Template::maintenance', compact('pageTitle', 'maintenance'));
    }
}
