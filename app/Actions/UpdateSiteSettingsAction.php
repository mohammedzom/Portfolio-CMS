<?php

namespace App\Actions;

use App\Models\SiteSettings;
use App\Traits\ManagesCache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSiteSettingsAction
{
    use AsAction, ManagesCache;

    public function handle(SiteSettings $settings, array $validated, array $files): SiteSettings
    {
        return DB::transaction(function () use ($settings, $validated, $files) {
            if (isset($validated['delete_avatar']) && $validated['delete_avatar'] == true) {
                if ($settings->avatar) {
                    Storage::disk('public')->delete($settings->avatar);
                }
                $validated['avatar'] = null;
            }

            if (isset($validated['delete_cv']) && $validated['delete_cv'] == true) {
                if ($settings->cv_file) {
                    Storage::disk('public')->delete($settings->cv_file);
                }
                $validated['cv_file'] = null;
            }

            if (isset($files['avatar'])) {
                if ($settings->avatar) {
                    Storage::disk('public')->delete($settings->avatar);
                }
                $validated['avatar'] = $files['avatar']->storeAs(
                    'avatars',
                    'avatar.'.$files['avatar']->getClientOriginalExtension(),
                    'public'
                );
            }

            if (isset($files['cv_file'])) {
                if ($settings->cv_file) {
                    Storage::disk('public')->delete($settings->cv_file);
                }
                $fileName = str_replace(' ', '_', $files['cv_file']->getClientOriginalName());
                $validated['cv_file'] = $files['cv_file']->storeAs('cv', $fileName, 'public');
            }

            $settings->save();
            $settings->update($validated);

            $this->forgetPortfolioCache();

            return $settings;
        });
    }
}
