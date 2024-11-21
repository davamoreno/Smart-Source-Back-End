<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider {
    /** 
     * Direct Path To Main Page Route For Application.
     * 
     * Typically, Users Have To Be Redirect To Main Page After Authentication
     * 
     * @var string
    */
    public const HOME = '/';

    public function boot(): void
    {
      $this->configureRateLimiting();
      
      $this->routes(function() {
        Route::middleware('api')
        ->prefix('api')
        ->group(base_path('routes/api.php'));
        Route::middleware('web')
        ->group(base_path('routes/web.php'));
      });
    }

    protected function configureRateLimiter(){
        RateLimiter::for('api', function(Request $request){
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}