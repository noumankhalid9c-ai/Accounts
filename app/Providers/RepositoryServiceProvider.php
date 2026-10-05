<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $models = [
            'Client', 'Invoice', 'Expense', 'Hosting', 'Domain', 'Payment', 
            'Receipt', 'Renewal', 'Service', 'Setting', 'User'
        ];

        foreach ($models as $model) {
            $this->app->bind(
                "App\\Interfaces\\{$model}RepositoryInterface",
                "App\\Repositories\\{$model}Repository"
            );
        }
    }

    public function boot(): void
    {
        //
    }
}
