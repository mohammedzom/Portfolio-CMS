<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\UpdateSiteSettingsRequest;
use App\Http\Resources\SiteSettingstResource;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\Storage;

class SiteSettingsController extends Controller
{
    public function index()
    {
        $settings = SiteSettings::firstOrFail();

        return $this->successResponse(
            new SiteSettingstResource($settings),
            'Site settings fetched successfully.'
        );
    }

    public function update(UpdateSiteSettingsRequest $request)
    {
        $settings = SiteSettings::firstOrFail();

        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($settings->avatar) {
                Storage::disk('public')->delete($settings->avatar);
            }
            $file_name = 'avatar.'.$request->file('avatar')->getClientOriginalExtension();
            $validated['avatar'] = $request->file('avatar')->storeAs('avatars', $file_name, 'public');
        }

        if ($request->hasFile('cv_file')) {
            if ($settings->cv_file) {
                Storage::disk('public')->delete($settings->cv_file);
            }
            $file_name = $request->file('cv_file')->getClientOriginalName();
            $file_name = str_replace(' ', '_', $file_name);
            $validated['cv_file'] = $request->file('cv_file')->storeAs('cv', $file_name, 'public');
            $settings['cv_file_name'] = $file_name;
            $settings->save();

        }

        $settings->update($validated);

        return $this->successResponse(
            new SiteSettingstResource($settings),
            'Site settings updated successfully.'
        );
    }
}
