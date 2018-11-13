<?php

namespace App\Listeners;

use Wechat;
use Log;

class CouponsEventListener {

    public function subscribe($events) {
        $events->listen('App\Events\Coupons', 'App\Listeners\CouponsEventListener@onTemplate');
    }

    public function onTemplate($event) {
        $user = $event->user;
        $notice = Wechat::notice();

        $templateId = config('app.debug') === false ? "tXoTWyFqxISHKZBpmkSdWbKbZPFi4KljM-QTzJ51xw4":"ftb1GkO51ozHY-Kb2zbTamuTParDd_E81Pr1OgrXwFU";
        Log::info("coupon_left_day 通知 user_id = {$user->user_id}");
        try {
            //推荐成功通知
            $notice->send([
                'touser' => $user->openid,
                'template_id' => $templateId,
                'url' => "http://m.hrwq.com/user/coupon?1=1",
                'topcolor' => '#f7f7f7',
                'data' => [
                    'first' => "您有一张优惠券即将到期" . "\n",
                    'keyword1' => $user->created_at . "\n",
                    'keyword2' => $user->expire_at . "\n",
                    'remark' => '点击立即使用优惠券'
                ],
            ]);
        } catch (\Exception $e) {
            Log::warning("发生异常：" . $e->getMessage());
        }
    }

}
