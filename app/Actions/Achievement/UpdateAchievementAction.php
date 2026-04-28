<?php

namespace App\Actions\Achievement;

use App\Models\Achievement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateAchievementAction
{
    use AsAction;

    public function handle(Achievement $achievement, array $data, $file): Achievement
    {
        return DB::transaction(function () use ($achievement, $data, $file) {
            if ($file) {
                if (! empty($achievement->certificate_url)) {
                    Storage::disk('public')->delete($achievement->certificate_url);
                }
                $fileName = $file->getClientOriginalName();
                $path = $file->storeAs('achievements', $fileName, 'public');
            }

            $achievement->update([
                'title' => $data['title'] ?? $achievement->title,
                'issuer' => $data['issuer'] ?? $achievement->issuer,
                'date' => $data['date'] ?? $achievement->date,
                'url' => $data['url'] ?? $achievement->url,
                'description' => $data['description'] ?? $achievement->description,
                'certificate_url' => $path ?? $achievement->certificate_url,
            ]);

            return $achievement;
        });
    }
}
