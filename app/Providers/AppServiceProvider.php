<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Organisation;
use App\Policies\OrganisationPolicy;

// Repository Binding er jonno ei 2 to use
use App\Repositories\Interfaces\OnboardingRepositoryInterface;
use App\Repositories\OnboardingRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Controller chara On-boarding er jonno ei line tai main
        $this->app->bind(OnboardingRepositoryInterface::class, OnboardingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Organisation::class, OrganisationPolicy::class);
    }
}