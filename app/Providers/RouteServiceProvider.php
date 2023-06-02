<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';
    protected $apiNamespace = 'App\\Http\\Api\\Controllers';
    protected $adminNamespace = 'App\\Http\\Admin\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $registrar = new \Cameron\Admin\Routing\ResourceRegistrar($this->app['router']);
        $this->app->bind(\Illuminate\Routing\ResourceRegistrar::class, function() use ($registrar) {
            return $registrar;
        });

       $this->routes(function () {
          //api
          $this->mapApiRoutes();
          //admin
          $this->mapAdminRoutes();
          //web
          $this->mapWebRoutes();
       });
    }

    /**
     * @return void
     */
    protected function mapWebRoutes()
    {
        $webRoutes = Route::middleware('web')
            ->namespace($this->namespace);
        array_map(function ($file) use ($webRoutes) {
            $webRoutes->group($file);
        }, self::getFilesArray(base_path('routes/web')));
    }

    /**
     * @return void
     */
    protected function mapApiRoutes()
    {
        $apiRoutes = Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace);
        array_map(function ($file) use ($apiRoutes) {
            $apiRoutes->group($file);
        }, self::getFilesArray(base_path('routes/api')));
    }


    /**
     * @return void
     */
    protected function mapAdminRoutes()
    {
        $apiRoutes = Route::prefix('admin')
            ->middleware('api')
            ->namespace($this->adminNamespace);
        array_map(function ($file) use ($apiRoutes) {
            $apiRoutes->group($file);
        }, self::getFilesArray(base_path('routes/admin')));
    }

    /**
     * @param         $searchDir
     * @param  array  $files
     * @return array
     */
    private static function getFilesArray($searchDir, array &$files = []): array
    {
        $handle = opendir($searchDir);
        while ($file = readdir($handle)) {
            if ($file !== '..' && $file !== '.') {
                $f = $searchDir . '/' . $file;
                if (is_file($f)) {
                    $extension = isset(pathinfo($file)['extension']) ? pathinfo($file)['extension'] : '';
                    if ($extension === 'php' || $extension === 'PHP') {
                        $files[] = $f;
                    }
                } else {
                    self::getFilesArray($f, $files);
                }
            }
        }
        return $files;
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
