<?php

namespace App\Providers;

use App\Domain\Models\User;
use App\Domain\Models\Tenant;
use App\Domain\Models\Account;
use App\Domain\Models\Blacklist;
use Illuminate\Support\ServiceProvider;
use App\Domain\Repositories\EloquentUserRepository;
use App\Domain\Repositories\EloquentTenantRepository;
use App\Domain\Repositories\EloquentAccountRepository;
use App\Domain\Repositories\EloquentBlacklistRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('UserRepository', function($app) {
            return new EloquentUserRepository(new User());
        });

        $this->app->singleton('TenantRepository', function($app) {
            return new EloquentTenantRepository(new Tenant());
        });

        $this->app->singleton('AccountRepository', function($app) {
            return new EloquentAccountRepository(new Account());
        });

        $this->app->singleton('BlacklistRepository', function($app) {
            return new EloquentBlacklistRepository(new Blacklist());
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
