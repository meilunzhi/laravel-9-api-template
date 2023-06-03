<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->isLocal()) {
            DB::listen(function ($query) {
                $sql = $query->sql;
                foreach ($query->bindings as $value) {
                    $sql = Str::replaceFirst('?', sprintf('%s', is_string($value) ? "'$value'" : $value), $sql);
                }
                Log::channel('db')->info(sprintf('%s; [%ss]', $sql, number_format($query->time / 1000, 5)));
            });
        }
    }
}
