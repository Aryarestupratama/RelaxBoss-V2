<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
// Import model dan policy yang relevan
use App\Models\Project;
use App\Models\Todo;
use App\Policies\ProjectPolicy;
use App\Policies\TodoPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Daftarkan policy baru di sini
        Project::class => ProjectPolicy::class,
        Todo::class => TodoPolicy::class,
    ];
 
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}