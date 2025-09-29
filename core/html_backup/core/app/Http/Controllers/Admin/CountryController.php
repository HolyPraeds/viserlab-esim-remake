<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\RequiredConfig;
use App\Models\Country;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CountryController extends Controller {

    public function index() {
        $pageTitle = 'Countries';

        $number = null;
        if (in_array(request()->per_page, [20, 50, 100, 150, 200])) {
            $number = request()->per_page;
        }

        $countries = Country::filter(['status', 'is_featured'])->searchable(['name', 'code'])->withCount('plans')->orderBy('name')->paginate(getPaginate($number));
        return view('admin.country.index', compact('pageTitle', 'countries'));
    }

    public function update($id, Request $request) {
        $request->validate([
            'country_image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        $country = Country::findOrFail($id);
        if ($request->hasFile('country_image')) {
            try {
                $image                  = $request->file('country_image');
                $extension              = $image->getClientOriginalExtension();
                $filename               = $country->code . '.' . $extension;
                $country->image         = fileUploader($image, getFilePath('countryFlag'), getFileSize('countryFlag'), $country->image, filename: $filename);
            } catch (\Exception $exp) {
                $notify[] = ['errors', 'Image could not be uploaded'];
                return back()->withNotify($notify);
            }
            $country->save();
        }

        $notify[] = ['success', 'Country updated successfully'];
        return back()->withNotify($notify);
    }

    public function toggleFeature($id) {
        return Country::changeStatus($id, 'is_featured');
    }

    public function changeStatus(Request $request) {
        $countries = explode(',', $request->countries);
        $request->merge(['countries' => $countries]);

        $request->validate([
            'countries' => 'required|array|min:1',
            'status'    => ['required', Rule::in([Status::ENABLE, Status::DISABLE])],
        ]);

        $countries = Country::whereIn('id', $request->countries)->update(
            ['status' => $request->status]
        );

        $notify[] = ['success', 'Status updated successfully'];
        return to_route('admin.destination.country.index')->withNotify($notify);
    }

    public function fetchCountries() {
        $countries = dataPlans()->fetchCountries();

        if (isset($countries['error'])) {
            $notify[] = ['error', $countries['error']];
            return back()->withNotify($notify);
        }

        $data = [];
        foreach ($countries as $item) {
            $countryExist = Country::where('code', $item['code'])->exists();
            if (!$countryExist) {
                $country = [];
                $country['name'] = $item['name'];
                $country['slug'] = Str::slug($item['name']);
                $country['code'] = $item['code'];
                $country['image'] = 'https://p.qrsim.net/img/flags/' . strtolower($item['code']) . '.png';
                $country['created_at'] = now();
                $country['updated_at'] = now();
                $data[] = $country;
            }
        }

        if (empty($data)) {
            $notify[] = ['info', 'No new countries found'];
        } else {
            Country::insert($data);
            RequiredConfig::configured('country');
            $notify[] = ['success', 'Countries fetched successfully'];
        }


        return back()->withNotify($notify);
    }
}
