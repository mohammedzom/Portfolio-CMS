<?php

namespace App\Actions\Experiences;

use App\Models\Experience;
use App\Traits\ManagesCache;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateExperienceAction
{
    use AsAction, ManagesCache;

    public function handle(Experience $experience, array $data): Experience
    {
        $this->validateCurrentExperience($data);

        if (($data['is_current'] ?? false) === true) {
            $data['end_date'] = null;
        }

        $experience->update($data);

        $this->forgetExperiencesCache();

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
