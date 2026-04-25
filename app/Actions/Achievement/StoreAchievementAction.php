<?php

namespace App\Actions\Achievement;

use App\Models\Achievement;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreAchievementAction
{
    use AsAction;

    public function handle(array $data, $file = null): Achievement
    {
        return DB::transaction(function () use ($data, $file) {
            if ($file) {
                $fileName = $file->getClientOriginalName();
                $file = $file->storeAs('achievements', $fileName, 'public');
            }

            $achievement = Achievement::create([
                'title' => $data['title'],
                'issuer' => $data['issuer'],
                'date' => $data['date'],
                'url' => $data['url'],
                'description' => $data['description'],
                'certificate_url' => $file ?? null,
            ]);

            Cache::forget('achievements');
            Cache::forget('portfolio_all');

            return $achievement;
        });
    }
}
