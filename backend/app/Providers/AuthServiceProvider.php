<?php

namespace App\Providers;

use App\Interfaces\BaseInterface;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use stdClass;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        foreach (config('permissions') as $group => $details) {
            foreach ($details['actions'] as $action=>$isActive) {
                $permissionSlug = $group.'_'.$action;

                // define our gates
                Gate::define($permissionSlug, function (User $user) use ($permissionSlug, $isActive) {

                    if (! $isActive) {
                        return false;
                    }

                    return $user->hasPermissionFor($permissionSlug);
                });
            }
        }
    }
}
