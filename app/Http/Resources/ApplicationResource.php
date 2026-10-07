<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'job_posting_id' => $this->job_posting_id,
            'student_id' => $this->student_id,
            'cv_path' => $this->cv_path,
            'cover_letter' => $this->cover_letter,
            'status' => $this->status,
            'applied_at' => $this->applied_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'job_posting' => $this->whenLoaded('jobPosting', function () {
                return [
                    'id' => $this->jobPosting->id,
                    'title' => $this->jobPosting->title,
                    'company' => $this->jobPosting->companyProfile ? [
                        'id' => $this->jobPosting->companyProfile->id,
                        'company_name' => $this->jobPosting->companyProfile->company_name,
                    ] : null,
                ];
            }),
            'student' => $this->whenLoaded('studentProfile', function () {
                return [
                    'id' => $this->studentProfile->id,
                    'user' => $this->studentProfile->user ? [
                        'id' => $this->studentProfile->user->id,
                        'name' => $this->studentProfile->user->name,
                        'email' => $this->studentProfile->user->email,
                    ] : null,
                ];
            }),
        ];
    }
}
