<?php

namespace App\Actions\Achievement;

use App\Models\Achievement;
use App\Traits\ManagesCache;
use App\Traits\ManagesUploadedFiles;
use Illuminate\Http\UploadedFile;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateAchievementAction
{
    use AsAction, ManagesCache, ManagesUploadedFiles;

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(Achievement $achievement, array $data, ?UploadedFile $file = null): Achievement
    {
        $previousCertificate = $achievement->certificate_url;
        $newCertificate = $file ? $this->storeUploadedFile($file, 'achievements') : null;

        $this->commitFileChanges(fn (): bool => $achievement->update([
            'title' => $data['title'] ?? $achievement->title,
            'issuer' => $data['issuer'] ?? $achievement->issuer,
            'date' => $data['date'] ?? $achievement->date,
            'url' => $data['url'] ?? $achievement->url,
            'description' => $data['description'] ?? $achievement->description,
            'certificate_url' => $newCertificate ?? $previousCertificate,
        ]), newPaths: array_filter([$newCertificate]), obsoletePaths: $newCertificate ? array_filter([$previousCertificate]) : []);

        $this->forgetAchievementsCache();

        return $achievement;
    }
}
