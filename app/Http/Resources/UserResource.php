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
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'suffix' => $this->suffix,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'civil_status' => $this->civil_status,
            'full_address' => $this->full_address,
            'region' => $this->region,
            'province' => $this->province,
            'city_municipality' => $this->city_municipality,
            'barangay' => $this->barangay,
            'street_address' => $this->street_address,
            'telephone' => $this->telephone,
            'mobile_number' => $this->mobile_number,
            'height' => $this->height,
            'religion' => $this->religion,
            'resume' => 'applicant-resume/' . ($this->resume ? $this->id : null),
            'employment_status' => $this->employment_status,
            'employment_type' => $this->employment_type,
            'months_looking' => $this->months_looking,
            'is_4ps' => $this->is_4ps,
            'is_pwd' => $this->is_pwd,
            'disability' => $this->disability,
            'is_ofw' => $this->is_ofw,
            'work_experience' => $this->work_experience,
            'country' => $this->country,
            'is_former_ofw' => $this->is_former_ofw,
            'last_deployment' => $this->last_deployment,
            'return_date' => $this->return_date,
            'transaction_date' => $this->transaction_date,
            'program_service' => $this->program_service,
            'event' => $this->event,
            'transaction' => $this->transaction,
            'remarks' => $this->remarks,
            'email' => $this->email,
            'username' => $this->username,
            'role_type' => $this->role_type,
            'skills' => SkillResource::collection($this->whenLoaded('skills')),
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            'preferred_positions' => PreferredPositionResource::collection($this->whenLoaded('preferred_positions')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
