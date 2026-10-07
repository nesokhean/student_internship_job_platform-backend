<?php

namespace App\Policies;

use App\Models\JobPosting;
use App\Models\User;

class JobPostingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'company' && $user->status === 'active' && $user->hasVerifiedEmail();
    }

    public function view(User $user, JobPosting $jobPosting): bool
    {
        return $user->role === 'company'
            && $user->status === 'active'
            && $user->hasVerifiedEmail()
            && $jobPosting->company_id === ($user->companyProfile?->id);
    }

    public function create(User $user): bool
    {
        return $user->role === 'company' && $user->status === 'active' && $user->hasVerifiedEmail();
    }

    public function update(User $user, JobPosting $jobPosting): bool
    {
        return $user->role === 'company'
            && $user->status === 'active'
            && $user->hasVerifiedEmail()
            && $jobPosting->company_id === ($user->companyProfile?->id);
    }

    public function delete(User $user, JobPosting $jobPosting): bool
    {
        return $this->update($user, $jobPosting);
    }
}
