<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(!$this->app->isProduction());

        /**
         * Define um Gate para verificar se o usuário pode ver o dashboard de usuários.
         * Apenas usuários com a role 'admin' ou 'moderator' podem.
         */
        Gate::define('access-user-dashboard', function (User $user) {
            return in_array($user->role, [UserRole::ADMIN, UserRole::MODERATOR]);
        });
    }
}
