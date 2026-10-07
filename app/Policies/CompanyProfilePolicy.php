<?php

namespace App\Policies;

use App\Models\CompanyProfile;
use App\Models\User;

class CompanyProfilePolicy
{
    public function view(User $user, CompanyProfile $companyProfile): bool
    {
        return $user->id === $companyProfile->user_id;
    }

    public function update(User $user, CompanyProfile $companyProfile): bool
    {
        return $user->id === $companyProfile->user_id;
    }

    public function delete(User $user, CompanyProfile $companyProfile): bool
    {
        return $user->id === $companyProfile->user_id;
    }
}
