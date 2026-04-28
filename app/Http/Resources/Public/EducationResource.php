<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EducationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'degree' => $this->degree,
            'institution' => $this->institution,
            'field_of_study' => $this->field_of_study,
            'start_year' => $this->start_year,
            'end_year' => $this->end_year,
            'gpa' => $this->gpa,
            'description' => $this->description,
        ];
    }
}
