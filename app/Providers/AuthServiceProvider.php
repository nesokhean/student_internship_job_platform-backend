<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\JobPosting;
use App\Models\StudentProfile;
use App\Policies\ApplicationPolicy;
use App\Policies\JobPostingPolicy;
use App\Policies\StudentProfilePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        JobPosting::class => JobPostingPolicy::class,
        Application::class => ApplicationPolicy::class,
        StudentProfile::class => StudentProfilePolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
