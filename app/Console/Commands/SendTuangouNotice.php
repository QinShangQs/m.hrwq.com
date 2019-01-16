<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserBalance;
use App\Models\OrderTeam;
use App\Models\Order;
use App\Models\OrderTeamMember;
use App\Events\Tuangou;
use Log, Event;

class SendTuangouNotice extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notices:tuangou';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发送团购通知';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        Log::info("[运行消息]" . $this->signature);

        $teams = OrderTeam::where(['status' => OrderTeam::STATUS_INIT])
                ->where('ended_at', '<', date('Y-m-d H:i:s'))->get();
        foreach ($teams as $team) {
            $hasAllPay = true;
            $members = OrderTeamMember::with(['user','order','team'])->where(['order_team_id' => $team->id])->get();
            foreach ($members as $member) {
                if(empty($member->order)){
                    $hasAllPay = false;
                    OrderTeamMember::destroy($member->id);
                }else if ($member->order->order_type == Order::PYA_NO) {
                    $hasAllPay = false;
                } 
            }

            if ($hasAllPay === true && count($members) == $team->need_members_cnt) {
                $team->status = OrderTeam::STATUS_SUCCESS;
                Log::info('开团成功:' . $team->id);
            } else {
                $team->status = OrderTeam::STATUS_FAILED;
            }

            $this->sendMessage($members, $team, $team->status);
            $team->dealed_at = date('Y-m-d H:i:s');//记录处理时间
            $team->save();
        }
    }

    private function sendMessage($members, $team, $status) {
        foreach ($members as $member) {
            $std = new \stdClass();
            $std->status = $status;
            $std->user_id = $member->user_id;
            $std->openid =  $member->user->openid;
            $std->order_id = $member->order_id;
            $std->order_code = $member->order->order_code;
            $std->course_name = $member->order->course->title;
            $std->need_members_cnt = $team->need_members_cnt;
            $std->team_price = $team->price;
            $std->team_created_at = $team->created_at;
            $std->team_ended_at = $team->ended_at;

            //退款到余额
            if($status == OrderTeam::STATUS_FAILED){
                UserBalance::change($member->user_id, $team->price, UserBalance::OPERATE_TYPE_INCREMENT, 8, '团购退款，订单号:'.$member->order->order_code);
            }
            
            try {
                Event::fire(new Tuangou($std));
            } catch (\Exception $e) {
                Log::error("[运行消息]" . $this->signature ."失败:" . $e->getMessage());
            }
        }
    }

}
