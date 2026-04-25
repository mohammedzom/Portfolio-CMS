<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EducationResource extends JsonResource
{
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
            'deleted_at_human' => $this->deleted_at?->diffForHumans(),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
