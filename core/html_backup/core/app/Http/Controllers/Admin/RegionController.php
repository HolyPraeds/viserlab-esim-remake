<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\RequiredConfig;
use App\Models\Region;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class RegionController extends Controller {
    public function index() {
        $pageTitle = 'Regions';
        $regions   = Region::searchable(['name', 'slug'])->withCount('plans')->paginate(getPaginate());
        return view('admin.region.index', compact('pageTitle', 'regions'));
    }

    public function update($id, Request $request) {
        $request->validate([
            'region_image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        $region = Region::findOrFail($id);
        if ($request->hasFile('region_image')) {
            try {
                $image                = $request->file('region_image');
                $extension            = $image->getClientOriginalExtension();
                $filename             = $region->slug . '.' . $extension;
                $region->region_image = fileUploader($image, getFilePath('regionImage'), getFileSize('regionImage'), $region->region_image, filename: $filename);
            } catch (\Exception $exp) {
                $notify[] = ['errors', 'Image could not be uploaded'];
                return back()->withNotify($notify);
            }

            $region->save();
        }

        $notify[] = ['success', 'Region updated successfully'];
        return back()->withNotify($notify);
    }

    public function changeStatus($id) {
        return Region::changeStatus($id);
    }

    public function fetchRegions() {
        $regions   = dataPlans()->fetchRegions();

        if (isset($regions['error'])) {
            $notify[] = ['error', $regions['error']];
            return back()->withNotify($notify);
        }

        $data = [];
        foreach ($regions as $item) {
            $regionExist = Region::where('slug', $item['code'])->exists();
            if (!$regionExist) {
                $region         = [];
                $region['name'] = $item['name'];
                $region['slug'] = $item['code'];
                $region['countries_list'] = json_encode(array_column($item['subLocationList'] ?? [], 'code'));
                $region['countries_list_name'] = json_encode(array_column($item['subLocationList'] ?? [], 'name'));
                $region['status'] = 1;
                $region['created_at'] = now();
                $region['updated_at'] = now();
                $data[] = $region;
            }
        }

        if (empty($data)) {
            $notify[] = ['info', 'No new regions found'];
        } else {
            Region::insert($data);
            RequiredConfig::configured('region');
            $notify[] = ['success', 'Regions fetched successfully'];
        }


        return back()->withNotify($notify);
    }
}
