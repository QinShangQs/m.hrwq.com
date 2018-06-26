<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;
use Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        if(config('app.debug') === true){
            DB::listen(function ($sql, $bindings, $time) {
                Log::info('db listen', [
                    'sql' => $sql,
                    'bindings' => $bindings,
                    'time' => $time
                ]);
            });
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
