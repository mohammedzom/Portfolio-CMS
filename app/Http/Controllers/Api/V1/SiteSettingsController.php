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
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->hasFile('cv_file')) {
            if ($settings->cv_file) {
                Storage::disk('public')->delete($settings->cv_file);
            }
            $validated['cv_file'] = $request->file('cv_file')->store('cv', 'public');
        }

        $settings->update($validated);

        return $this->successResponse(
            new SiteSettingstResource($settings),
            'Site settings updated successfully.'
        );
    }
}
