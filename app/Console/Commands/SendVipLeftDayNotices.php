<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Events\VipLeftDay;
use Event;

class SendVipLeftDayNotices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notices:vipleftday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发送会员天数到期通知';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	Log::info("[运行消息]notices:vipleftday");
        $users = DB::select("select *, DATEDIFF(vip_left_day,CURDATE()) as left_day from `user` order by vip_left_day desc");
       	foreach ($users as $k=>$user){
       		if(is_numeric($user->left_day) && $user->left_day < 0){
       			DB::update("update `user` set vip_left_day = ? where id = ? ", [NULL, $user->id]);
				Log::info("置空NULL vip_left_day user_id = {$user->id}");
       		}else{
       			Event::fire(new VipLeftDay($user));
       		}
       	} 
    }
}
