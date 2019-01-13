<?php

namespace App\Listeners;

use Wechat;
use Log;

class TuangouEventListener {

    public function subscribe($events) {
        $events->listen('App\Events\Tuangou', 'App\Listeners\TuangouEventListener@onTemplate');
    }

    public function onTemplate($event) {
        $std = $event->std;
        $notice = Wechat::notice();

        $templateId = null;
        Log::info("TuangouEventListener 通知" . json_encode($std, JSON_UNESCAPED_UNICODE));

        try {
            if ($std->status == \App\Models\OrderTeam::STATUS_SUCCESS) {
                $templateId = is_dev() ? "5aJ5Aarj-yN-omB4QzJOh1y0blyABb2Heta3oPyp80o" : "6DolqcrRYGNjkSGaumgVLT0HHrTBg_rc51rxLT";
                $notice->send([
                    'touser' => $std->openid,
                    'template_id' => $templateId,
                    'url' => route('my.orders'),
                    'topcolor' => '#f7f7f7',
                    'data' => [
                        'first' => "恭喜您拼团成功。" . "\n",
                        'keyword1' => $std->order_code . "\n",
                        'keyword2' => $std->course_name  . "\n",
                        'keyword3' => $std->team_price  . "\n",
                        'keyword4' => $std->team_ended_at  . "\n",
                        'remark' => ''
                    ],
                ]);
            } else if ($std->status == \App\Models\OrderTeam::STATUS_FAILED) {
                $templateId = is_dev() ? "PpddOiYWgjGMh-s5u9othrvCAE-KIcaBXqx_dTQpMIM" : "Pop3YlZyzQL4Zh2RsQOEvnP0rAN68pf17D1XD";
                $notice->send([
                    'touser' => $std->openid,
                    'template_id' => $templateId,
                    'url' => route('my.orders'),
                    'topcolor' => '#f7f7f7',
                    'data' => [
                        'first' => "抱歉，您拼团失败" . "\n",
                        'keyword1' => $std->course_name . "\n",
                        'keyword2' => $std->team_price  . "\n",
                        'keyword3' => $std->need_members_cnt  . "\n",
                        'keyword4' => $std->team_ended_at  . "\n",
                        'remark' => ''
                    ],
                ]);
            }
        } catch (\Exception $e) {
            Log::warning("发生异常：" . $e->getMessage());
        }
    }

}
