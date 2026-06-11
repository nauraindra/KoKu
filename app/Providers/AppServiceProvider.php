<?php

namespace App\Providers;

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\TrackVisit;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        app('router')->aliasMiddleware('role', RoleMiddleware::class);
        app('router')->aliasMiddleware('track.visit', TrackVisit::class);

        Blade::directive('rupiah', function ($amount) {
            return "<?php echo 'Rp ' . number_format($amount, 0, ',', '.'); ?>";
        });
    }
}
