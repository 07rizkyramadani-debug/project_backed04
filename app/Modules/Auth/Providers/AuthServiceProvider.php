<?php

namespace Modules\Auth\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Group 'web' diperlukan agar session, cookie, dan CSRF aktif
        // pada route yang dimuat dari modul.
        Route::middleware('web')->group(__DIR__ . '/../routes/web.php');
    }
}
