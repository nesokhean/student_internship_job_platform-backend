<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'university' => $this->university,
            'major' => $this->major,
            'year' => $this->year,
            'bio' => $this->bio,
            'skills' => $this->skills,
            'address' => $this->address,
            'profile_image' => $this->profile_image,
            'cv_path' => $this->cv_path,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
