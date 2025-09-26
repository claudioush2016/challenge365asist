<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Eloquent\EloquentBookingRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            BookingRepositoryInterface::class,
            EloquentBookingRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
