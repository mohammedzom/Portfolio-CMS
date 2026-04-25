<?php

namespace App\Actions\Achievement;

use App\Models\Achievement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateAchievementAction
{
    use AsAction;

    public function handle(array $data, $file, string $id): Achievement
    {
        return DB::transaction(function () use ($data, $file, $id) {
            $achievement = Achievement::findOrFail($id);

            if ($file) {
                Storage::disk('public')->delete($achievement->certificate_url);
                $fileName = $file->getClientOriginalName();
                $file = $file->storeAs('achievements', $fileName, 'public');
            }

            $achievement->update([
                'title' => $data['title'],
                'issuer' => $data['issuer'],
                'date' => $data['date'],
                'url' => $data['url'],
                'description' => $data['description'],
                'certificate_url' => $file ?? $achievement->certificate_url,
            ]);

            return $achievement;
        });
    }
}
