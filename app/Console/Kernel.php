<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
    	Commands\SendVipLeftDayNotices::class,
        Commands\SendCouponNotices::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();
        //发送通知
        $schedule->command('notices:vipleftday')
        		->dailyAt('07:00');
        
        //发送通知
        $schedule->command('notices:coupons')
        		->dailyAt('07:30');
    }
}
