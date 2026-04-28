<?php

namespace App\Actions\Experiences;

use App\Models\Experience;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreExperienceAction
{
    use AsAction;

    public function handle(array $data): Experience
    {
        $this->validateCurrentExperience($data);

        if (($data['is_current'] ?? false) === true) {
            $data['end_date'] = null;
        }

        $experience = Experience::create($data);

        Cache::forget('experiences');
        Cache::forget('portfolio_all');

        return $experience;
    }

    private function validateCurrentExperience(array $data): void
    {
        if (($data['is_current'] ?? false) === true && ! empty($data['end_date'])) {
            throw ValidationException::withMessages([
                'end_date' => 'End date must be empty when is_current is true.',
            ]);
        }
    }
}
