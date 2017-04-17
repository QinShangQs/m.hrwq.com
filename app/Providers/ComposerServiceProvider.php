<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Using class based composers...
        view()->composer(
            '*', 'App\Http\ViewComposers\CurAdminComposer'
        );
        view()->composer(
            '*', 'App\Http\ViewComposers\WechatComposer'
        );
        view()->composer(['my.addresses', 'user.login', 'user.edit_profile', 'course.receipt_address', 'vip.receipt_address', 'partner.complete'], 'App\Http\ViewComposers\AreaComposer');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
