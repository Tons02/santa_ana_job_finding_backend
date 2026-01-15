<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'suffix' => $this->suffix,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'landline' => $this->landline,
            'mobile_number' => $this->mobile_number,
            'civil_status' => $this->civil_status,
            'height' => $this->height,
            'religion' => $this->religion,
            'resume' => 'applicant-resume/' . ($this->resume ? $this->id : null),
            'full_address' => $this->full_address,
            'province' => $this->province,
            'lgu' => $this->lgu,
            'barangay' => $this->barangay,
            'employment_status' => $this->employment_status,
            'employment_type' => $this->employment_type,
            'months_looking' => $this->months_looking,
            'is_ofw' => $this->is_ofw,
            'is_former_ofw' => $this->is_former_ofw,
            'last_deployment' => $this->last_deployment,
            'return_date' => $this->return_date,
            'username' => $this->username,
            'email' => $this->email,
            'role_type' => $this->role_type,
            'skills' => SkillResource::collection($this->whenLoaded('skills')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
