<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Events\Coupons;
use Log, Event;

class SendCouponNotices extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notices:coupons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发送优惠券到期通知';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    public function handle(){
        Log::info("[运行消息]". $this->signature);
        //提前n天通知
        $users = DB::select("select u.openid, cu.user_id, cu.created_at, cu.expire_at ,DATEDIFF(expire_at,CURRENT_TIMESTAMP()) as leftday 
            from coupon_user as cu 
            inner join `user` as u on u.id = cu.user_id
            where cu.is_used = 2 and cu.expire_at > CURRENT_TIMESTAMP()
            and DATEDIFF(expire_at,CURRENT_TIMESTAMP()) = 1
        ");
       	foreach ($users as $user){
            Event::fire(new Coupons($user));
       	} 
    }
}
