<?php

namespace App\Actions\Achievement;

use App\Models\Achievement;
use App\Traits\ManagesCache;
use App\Traits\ManagesUploadedFiles;
use Illuminate\Http\UploadedFile;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreAchievementAction
{
    use AsAction, ManagesCache, ManagesUploadedFiles;

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, ?UploadedFile $file = null): Achievement
    {
        $certificatePath = $file ? $this->storeUploadedFile($file, 'achievements') : null;

        $achievement = $this->commitFileChanges(fn (): Achievement => Achievement::create([
            'title' => $data['title'],
            'issuer' => $data['issuer'],
            'date' => $data['date'],
            'url' => $data['url'] ?? null,
            'description' => $data['description'] ?? null,
            'certificate_url' => $certificatePath,
        ]), newPaths: array_filter([$certificatePath]));

        $this->forgetAchievementsCache();

        return $achievement;
    }
}
