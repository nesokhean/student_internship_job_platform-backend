<?php

namespace App\Policies;

use App\Models\StudentProfile;
use App\Models\User;

class StudentProfilePolicy
{
    public function view(User $user, StudentProfile $studentProfile): bool
    {
        return $user->id === $studentProfile->user_id;
    }

    public function update(User $user, StudentProfile $studentProfile): bool
    {
        return $user->id === $studentProfile->user_id;
    }

    public function delete(User $user, StudentProfile $studentProfile): bool
    {
        return $user->id === $studentProfile->user_id;
    }
}
