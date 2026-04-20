<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\UpdateSiteSettingsRequest;
use App\Models\SiteSettings;

class SiteSettingsController extends Controller
{
    public function index()
    {
        $settings = SiteSettings::first();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(UpdateSiteSettingsRequest $request)
    {
        $settings = SiteSettings::first();

        if (! $settings) {
            $settings = SiteSettings::create([]);
        }

        $validated = $request->validated();

        // Handle file uploads
        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->hasFile('cv_file')) {
            $validated['cv_file'] = $request->file('cv_file')->store('cv', 'public');
        }

        $settings->update($validated);

        return redirect()->route('admin.settings.index')->with('success', 'Site settings updated successfully');
    }
}
