<?php

namespace App\Providers;

use App\Interfaces\CollectionInterface;
use App\Interfaces\SourceCodeInterface;
use App\Repositories\CollectionRepository;
use App\Repositories\SourceCodeRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(SourceCodeInterface::class, SourceCodeRepository::class);
        $this->app->bind(CollectionInterface::class, CollectionRepository::class);
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
