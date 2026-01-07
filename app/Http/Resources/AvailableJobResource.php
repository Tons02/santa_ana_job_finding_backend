<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableJobResource extends JsonResource
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
            // 'employer_name' => [
            //     "phase 2"
            // ],
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'is_remote' => $this->is_remote,
            'employment_type' => $this->employment_type,
            'experience_level' => $this->experience_level,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'salary_currency' => $this->salary_currency,
            'salary_period' => $this->salary_period,
            'status' => $this->status,
            'hiring_status' => $this->hiring_status,
            'posted_at' => $this->posted_at,
            'expires_at' => $this->expires_at,
            'skills' => SkillResource::collection($this->whenLoaded('skills')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
