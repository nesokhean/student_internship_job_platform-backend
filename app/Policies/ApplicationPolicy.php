<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\JobPosting;
use App\Models\User;

class ApplicationPolicy
{
    public function viewAny(User $user, JobPosting $jobPosting): bool
    {
        return $user->role === 'company'
            && $user->status === 'active'
            && $user->hasVerifiedEmail()
            && $jobPosting->company_id === ($user->companyProfile?->id);
    }

    public function view(User $user, Application $application): bool
    {
        if ($user->role === 'student' && $user->status === 'active' && $user->hasVerifiedEmail()) {
            return $application->student_id === ($user->studentProfile?->id);
        }

        if ($user->role === 'company' && $user->status === 'active' && $user->hasVerifiedEmail()) {
            return $application->jobPosting?->company_id === ($user->companyProfile?->id);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->role === 'student' && $user->status === 'active' && $user->hasVerifiedEmail();
    }

    public function update(User $user, Application $application): bool
    {
        return $user->role === 'company'
            && $user->status === 'active'
            && $user->hasVerifiedEmail()
            && $application->jobPosting?->company_id === ($user->companyProfile?->id);
    }

    public function delete(User $user, Application $application): bool
    {
        return $user->role === 'student'
            && $user->status === 'active'
            && $user->hasVerifiedEmail()
            && $application->student_id === ($user->studentProfile?->id);
    }
}
