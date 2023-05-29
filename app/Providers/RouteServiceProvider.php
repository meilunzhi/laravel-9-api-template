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

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            //api
            $this->mapApiRoutes();
            //web
            $this->mapWebRoutes();
        });

        // $this->routes(function () {
        //     Route::prefix('api')
        //         ->middleware('api')
        //         ->namespace($this->namespace)
        //         ->group(base_path('routes/api.php'));
        //
        //     Route::middleware('web')
        //         ->namespace($this->namespace)
        //         ->group(base_path('routes/web.php'));
        // });
    }


    /**
     * @return void
     */
    protected function mapWebRoutes()
    {
        $webRoutes = Route::middleware('web')
            ->namespace($this->namespace);
        //获取指定目录下的所有文件并根据文件创建路由组
        array_map(function ($file) use ($webRoutes) {
            $webRoutes->group($file);
        }, self::getFilesArray(base_path('routes/web')));
        // ->group(base_path('routes/web.php'));
    }

    /**
     * @return void
     */
    protected function mapApiRoutes()
    {
        $apiRoutes = Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace);
        //获取指定目录下的所有文件并根据文件创建路由组
        array_map(function ($file) use ($apiRoutes) {
            $apiRoutes->group($file);
        }, self::getFilesArray(base_path('routes/api')));
    }

    // protected function mapChannelsRoutes()
    // {
    //     $routes = Route::middleware('channels')
    //         ->namespace($this->namespace);
    //     //获取指定目录下的所有文件并根据文件创建路由组
    //     array_map(function ($file) use ($routes) {
    //         $routes->group($file);
    //     }, self::getFilesArray(base_path('routes/channels')));
    //     // ->group(base_path('routes/web.php'));
    // }
    //
    // protected function mapConsoleRoutes()
    // {
    //     $consoleRoutes = Route::middleware('web')
    //         ->namespace($this->namespace);
    //     //获取指定目录下的所有文件并根据文件创建路由组
    //     array_map(function ($file) use ($consoleRoutes) {
    //         $consoleRoutes->group($file);
    //     }, self::getFilesArray(base_path('routes/console')));
    //     // ->group(base_path('routes/web.php'));
    // }

    /**
     * @param         $searchDir
     * @param  array  $files
     * @return array
     */
    private static function getFilesArray($searchDir, array &$files = []): array
    {
        //遍历目录下的所有文件和文件夹
        $handle = opendir($searchDir);
        while ($file = readdir($handle)) {
            if ($file !== '..' && $file !== '.') {
                $f = $searchDir . '/' . $file;
                if (is_file($f)) {
                    //只取php文件
                    $extension = isset(pathinfo($file)['extension']) ? pathinfo($file)['extension'] : '';
                    if ($extension === 'php' || $extension === 'PHP') {
                        $files[] = $f;
                    }
                } else {
                    //递归查询目录下的所有文件
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
