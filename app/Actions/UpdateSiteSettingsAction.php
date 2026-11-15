<?php

namespace App\Actions;

use App\Models\SiteSettings;
use App\Traits\ManagesCache;
use App\Traits\ManagesUploadedFiles;
use Illuminate\Http\UploadedFile;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class UpdateSiteSettingsAction
{
    use AsAction, ManagesCache, ManagesUploadedFiles;

    /**
     * Uploaded files whose stored path is kept in a settings column, with
     * the directory they are stored in and the flag that clears them.
     *
     * @var array<string, array{directory: string, deleteFlag: string}>
     */
    private const MANAGED_FILES = [
        'avatar' => ['directory' => 'avatars', 'deleteFlag' => 'delete_avatar'],
        'cv_file' => ['directory' => 'cv', 'deleteFlag' => 'delete_cv'],
    ];

    /**
     * @param  array<string, mixed>  $validated
     * @param  array<string, mixed>  $files
     */
    public function handle(SiteSettings $settings, array $validated, array $files): SiteSettings
    {
        $newPaths = [];
        $obsoletePaths = [];

        try {
            foreach (self::MANAGED_FILES as $column => $options) {
                $upload = $files[$column] ?? $validated[$column] ?? null;
                unset($validated[$column]);

                if ($upload instanceof UploadedFile) {
                    $newPaths[] = $validated[$column] = $this->storeUploadedFile($upload, $options['directory']);
                    $obsoletePaths[] = $settings->{$column};
                } elseif (! empty($validated[$options['deleteFlag']])) {
                    $validated[$column] = null;
                    $obsoletePaths[] = $settings->{$column};
                }

                unset($validated[$options['deleteFlag']]);
            }
        } catch (Throwable $exception) {
            $this->deleteManagedFiles($newPaths);

            throw $exception;
        }

        $this->commitFileChanges(
            fn (): bool => $settings->fill($validated)->save(),
            newPaths: $newPaths,
            obsoletePaths: array_values(array_filter($obsoletePaths)),
        );

        $this->forgetPortfolioCache();

        return $settings;
    }
}
