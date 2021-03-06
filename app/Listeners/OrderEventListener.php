<?php

namespace App\Listeners;

use App\Models\Opo;
use Carbon\Carbon;
use App\Models\User;
use Wechat,
    Log;

class OrderEventListener {

    public function subscribe($events) {
        $events->listen(
                'App\Events\OrderPaid', 'App\Listeners\OrderEventListener@onOrderPaid'
        );
    }

    public function onOrderPaid($event) {
        $order = $event->order;
        $user_info = User::where('id', $order->user_id)->first();
        $openid = $user_info->openid;
        if ($order->id > 0) {
            //更新支付时间
            $order->pay_time = (string) Carbon::now();
            $order->save();
        }
        switch ($order->pay_type) {
            case 1: //线下
                //发送短信通知
                try {
                    send_sms([$order->user->mobile], '让教育孩子变得简单。恭喜你成功报名' . $order->course->title . '，请提前安排好行程。客服电话400-6363-555');
                    //发送微信通知
                    $notice = Wechat::notice();
                    $notice->send([
                        'touser' => $order->user->openid,
                        'template_id' => '7hXsOVA4WE3nGyta1UQRqUOtDP6C1D5ymR-E46_X1Ts',
                        'url' => route('course.qrcode', $order->id),
                        'topcolor' => '#f7f7f7',
                        'data' => [
                            'first' => '订单已支付',
                            'keyword1' => '线下',
                            'keyword2' => (string) $order->pay_time,
                            'keyword3' => $order->course->title,
                            'remark' => '让教育孩子变得简单。恭喜你成功报名' . $order->course->title . '，请提前安排好行程。
点击查看听课凭证'
                        ],
                    ]);
                } catch (\Exception $e) {
                    \Log::info('推送给合伙人失败。');
                }
                //推送给地区合伙人
                $partners = [];
                if ($order->course->head_flg == 2) {
                    $partners = User::where('role', 3)->where('block', 1)
                            ->where('partner_city', $order->course->city)
                            ->where('id', $order->course->promoter)
                            ->get();
                } elseif ($order->course->head_flg == 1 && $order->course->distribution_flg == 1) {
                    $partners = User::where('role', 3)->where('block', 1)
                            ->where('partner_city', $order->order_course->user_city)
                            ->get();
                }

                if (count($partners)) {
                    foreach ($partners as $partner) {
                        try {
                            $notice = Wechat::notice();
                            $notice->send([
                                'touser' => $partner->openid,
                                'template_id' => '7hXsOVA4WE3nGyta1UQRqUOtDP6C1D5ymR-E46_X1Ts',
                                'url' => route('partner.orders'),
                                'topcolor' => '#f7f7f7',
                                'data' => [
                                    'first' => '合伙人订单推送',
                                    'keyword1' => '线下',
                                    'keyword2' => (string) $order->pay_time,
                                    'keyword3' => $order->course->title,
                                    'remark' => '您所负责的区域有顾客下单，请及时关注。'
                                ],
                            ]);
                        } catch (\Exception $e) {
                            \Log::info('推送给合伙人失败。');
                        }
                    }
                }

                break;
            case 2: //听课
                //微信公众号推送消息
                $notice = Wechat::notice();
                $userId = $openid;
                $templateId = '7hXsOVA4WE3nGyta1UQRqUOtDP6C1D5ymR-E46_X1Ts';
                $url = route('vcourse.detail', ['id' => $order->pay_id]);
                $color = '#FF0000';
                $data = array(
                    "first" => "亲爱的家人，恭喜你成功购买" . $order->order_name,
                    "keyword1" => "听课",
                    "keyword2" => "" . $order->created_at,
                    "keyword3" => $order->order_name,
                    "remark" => "记得在线记笔记、写作业，可以获得积分哦！用学习跟上孩子的成长。祝您生活愉快。",
                );
                $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($userId)->send();
                break;
            case 3: //壹家壹
                $opo = Opo::find($order->pay_id);
                if ($opo != null) {
                    $opo->increment('purchase_num', 1);
                }
                \Log::debug($order->order_code);
                //发送短信和微信提醒
                send_sms([$order->user->mobile], '恭喜你成功预约定制成长，也定制幸福的壹家壹服务，请保持手机畅通，专家老师将在24小时内与你取得联系并开展服务。客服电话400-6363-555');
                send_sms([config('constants.opo_manager_mobile')], '有新的壹家壹预约订单啦！请尽快登陆管理后台查看，并与用户取得联系，记得及时更新服务进度。');
                $notice = Wechat::notice();
                $notice->send([
                    'touser' => $order->user->openid,
                    'template_id' => '7hXsOVA4WE3nGyta1UQRqUOtDP6C1D5ymR-E46_X1Ts',
                    'url' => route('opo'),
                    'topcolor' => '#f7f7f7',
                    'data' => [
                        'first' => '订单状态提示',
                        'keyword1' => '壹家壹',
                        'keyword2' => (string) $order->pay_time,
                        'keyword3' => '定制成长，也定制幸福的壹家壹服务',
                        'remark' => '请保持手机畅通，专家老师将在24小时内与你取得联系，开展服务。做中国好爸妈，获家庭真幸福。'
                    ],
                ]);
                break;
            case 4: //问答提问
                $notice = Wechat::notice();
                //给提问者通知
                $notice->send([
                    'touser' => $order->user->openid,
                    'template_id' => '7hXsOVA4WE3nGyta1UQRqUOtDP6C1D5ymR-E46_X1Ts',
                    'url' => route('wechat.question', ['id' => $order->question->id]),
                    'topcolor' => '#f7f7f7',
                    'data' => [
                        'first' => '订单状态提示',
                        'keyword1' => '问答',
                        'keyword2' => (string) $order->pay_time,
                        'keyword3' => $order->order_name,
                        'remark' => '亲爱的家人，你的问题已成功送达给' . ($order->question->answer_user->nickname) . '，我们将提醒其在48小时内回答你的问题。'
                    ],
                ]);
                //给回答者通知
                $notice = Wechat::notice();
                $notice->send([
                    'touser' => $order->question->answer_user->openid,
                    'template_id' => '7hXsOVA4WE3nGyta1UQRqUOtDP6C1D5ymR-E46_X1Ts',
                    'url' => route('user.answer_voice', ['id' => $order->question->id]),
                    'topcolor' => '#f7f7f7',
                    'data' => [
                        'first' => '（指导师）家长咨询提示',
                        'keyword1' => '问答',
                        'keyword2' => (string) $order->pay_time,
                        'keyword3' => $order->order_name,
                        'remark' => '魅力爆棚了！亲爱的家人，你有新的提问！请在48小时内组织好语言，清晰明了地回答ta的问题，分享家庭成长经历，传播幸福正能量，赚取满满成就感！'
                    ],
                ]);
                break;
            //问答偷听
            case 5:
                break;
            //和会员
            case 6:
                //会员开通成功，给用户提示
                $tvCodeUrl = \App\Models\VipTv::assign($order->user_id); //直播码
                $sms_msg = '亲爱的家人，恭喜您成功开通和会员，关注“和润好父母”公众号（微信号：HRWQ-2002）立即领取一年直播课程 立即成为学习型父母，构建学习型家庭！'; //_festival_replace(,'亲爱的家人，恭喜您成功开通和会员！“双十一”活动所赠天数已经直接为您添加，可进入“和润好父母”公众号查看！');
                send_sms([$order->user->mobile], $sms_msg);
                $notice = Wechat::notice();
                $notice->send([
                    'touser' => $order->user->openid,
                    'template_id' => (is_dev() ? "FIR7wZ8gw5CoxwhIiKNXl1xXlNzZntUh5n-ngV0xFWs" : '7hXsOVA4WE3nGyta1UQRqUOtDP6C1D5ymR-E46_X1Ts'),
                    'url' => (!empty($tvCodeUrl) ? $tvCodeUrl : "http://m.hrwq.com/user/profile/edit"),
                    'topcolor' => '#f7f7f7',
                    'data' => [
                        'first' => '和会员开通提示',
                        'keyword1' => '和会员',
                        'keyword2' => (string) $order->pay_time,
                        'keyword3' => '恭喜你成功加入和润万青父母学院',
                        'remark' => [
                            'value' => "\n" . '点击此处立即领取一年直播课程收听资格，享受完整会员权益。',
                            'color' => '#C80000'
                        ]
                    ],
                ]);

                break;
        }

        try {
            //团购订单
            if ($order->is_team == \App\Models\Order::IS_TEAM_YES) {
                $this->tuangou($order);
            }
        } catch (\Exception $exx) {
            Log::error("团购订单微信消息异常：" . $exx->getMessage());
        }
    }

    /**
     * 团购通知
     * @param \stdClass $order
     */
    public function tuangou($order) {
        $member = \App\Models\OrderTeamMember::with(['user', 'order', 'team'])->
                        where(['order_id' => $order->id, 'user_id' => $order->user_id])->first();
        if (empty($member)) {
            return;
        }
        try {
            $notice = Wechat::notice();
            if ($member->member_type === \App\Models\OrderTeamMember::MEMBER_TYPE_INITIATOR) {
                $notice->send([
                    'touser' => $member->user->openid,
                    'template_id' => is_dev() ? "WqoyXK8WYNbOnoqLrehHa8DphcIOtb71BeSwq5fPaVY" : "RwNIylS6uopN0Hlglr4j9MPPXL3ftvUfv9GrwykXDdI",
                    'url' => route('my.orders'),
                    'topcolor' => '#f7f7f7',
                    'data' => [
                        'first' => '恭喜您开团成功',
                        'keyword1' => $member->order->course->title,
                        'keyword2' => $member->user->need_members_cnt,
                        'keyword3' => $member->team->price,
                        'keyword4' => $member->team->ended_at,
                        'remark' => ''
                    ],
                ]);
            } else {
                $notice->send([
                    'touser' => $member->user->openid,
                    'template_id' => is_dev() ? "7-SxiQcGCkUf8fbzmTFEUqSlcSZNKZQnP-7ZapO_VHQ" : "oCoHJtp9VCKNlT7ykJ6Vq6iCczDmuY_bEljJdqeIMA4",
                    'url' => route('my.orders'),
                    'topcolor' => '#f7f7f7',
                    'data' => [
                        'first' => '恭喜您参团成功',
                        'keyword1' => $member->order->course->title,
                        'keyword2' => $member->user->nickname,
                        'keyword3' => $member->team->price,
                        'keyword4' => $member->team->ended_at,
                        'remark' => ''
                    ],
                ]);
            }
        } catch (\Exception $ex) {
            Log::error("团购订单微信消息异常：" . $ex->getMessage());
        }
    }

}
