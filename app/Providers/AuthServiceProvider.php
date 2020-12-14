<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        /** Gates @author dplazao */
        Gate::define('sysadmin', function ($user) { return $user->privilege === 'sysadmin'; });

        Gate::define('modify-association', 'App\Http\Controllers\AssociationController@canModifyAssociation');
        Gate::define('view-association', 'App\Http\Controllers\AssociationController@canViewAssociation');
    }
}
