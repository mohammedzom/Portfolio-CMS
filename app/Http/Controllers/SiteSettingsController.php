<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSiteSettingsRequest;
use App\Models\SiteSettings;

class SiteSettingsController extends Controller
{
    public function update(UpdateSiteSettingsRequest $request)
    {
        SiteSettings::update($request->validated());

        return redirect()->route('admin.settings.index')->with('success', 'Site settings updated successfully');
    }
}
