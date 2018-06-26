<?php

namespace App\Listeners;

use App\Models\Opo;
use Carbon\Carbon;
use EasyWeChat\Foundation\Application;
use App\Models\User;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Text;
use QrCode, Wechat;

class OrderEventListener
{
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\OrderPaid',
            'App\Listeners\OrderEventListener@onOrderPaid'
        );
    }

    public function onOrderPaid($event)
    {
        $order = $event->order;
        $user_info = User::where('id',$order->user_id)->first();
        $openid = $user_info->openid;
        if ($order->id > 0) {
            //更新支付时间
            $order->pay_time = (string)Carbon::now();
            $order->save();
            switch ($order->pay_type) {
                case 1: //线下
                    //发送二维码
//                    $wechat = new Application(config('wechat'));
//                    $staff = $wechat->staff;
//                    $material = $wechat->material;
//                    if(!file_exists(public_path('uploads/qrcodes')))
//                        mkdir(public_path('uploads/qrcodes'));
//                    //报道页
//                    $url=route('course.course_report',['id'=>$order->id]);
//                    QrCode::format('png')->size(200)->merge('/public/images/hrwq.jpg',.15)->generate($url, public_path('uploads/qrcodes/qrcode_'.$order->order_code.'.png'));
//                     //初始化微信 payment
//                    $result = $material->uploadImage(public_path('uploads/qrcodes/qrcode_'.$order->order_code.'.png'));
//                    $media_id = $result->media_id;
//                    $text = new Text(['content' => '以下为 '.$order->order_name.' 活动二维码凭证']);
//                    $image = new Image(['media_id' => $media_id]);
//                    $staff->message($text)->to($openid)->send();
//                    $staff->message($image)->to($openid)->send();
//                    //删除生成的二维码
//                    @unlink(public_path('uploads/qrcodes/qrcode_'.$order->order_code.'.png'));
//			\Log::debug($order->order_code);
                    //发送短信通知
                    try {
                        send_sms([$order->user->mobile], '让教育孩子变得简单。恭喜你成功报名'.$order->course->title.'，请提前安排好行程。客服电话400-6363-555');
                        //发送微信通知
                        $notice = Wechat::notice();                        
                        $notice->send([
                            'touser' => $order->user->openid,
                            'template_id' => '7hXsOVA4WE3nGyta1UQRqUOtDP6C1D5ymR-E46_X1Ts',
                            'url' => route('course.qrcode',$order->id),
                            'topcolor' => '#f7f7f7',
                            'data' => [
                                'first' => '订单已支付',
                                'keyword1'=> '线下',
                                'keyword2'=> (string)$order->pay_time,
                                'keyword3'=> $order->course->title,
                                'remark'=> '让教育孩子变得简单。恭喜你成功报名'.$order->course->title.'，请提前安排好行程。
点击查看听课凭证'
                            ],
                        ]);
                    }catch (\Exception $e) {
                        \Log::info('推送给合伙人失败。');
                    }
                    //推送给地区合伙人
                    $partners = [ ];
                    if ($order->course->head_flg==2) {
                        $partners = User::where('role', 3)->where('block', 1)
                        ->where('partner_city', $order->course->city)
                        ->get();
                    } elseif ($order->course->head_flg==1&&$order->course->distribution_flg==1) {
                        $partners = User::where('role', 3)->where('block', 1)
                        ->where('partner_city', $order->order_course->user_city)
                        ->get();
                    }
                    
                    if(count($partners)) {
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
                                    'keyword1'=> '线下',
                                    'keyword2'=> (string)$order->pay_time,
                                    'keyword3'=> $order->course->title,
                                    'remark'=> '您所负责的区域有顾客下单，请及时关注。'
                                ],
                            ]);
                            }catch (\Exception $e) {
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
                    $url = route('vcourse.detail',['id'=>$order->pay_id]);
                    $color = '#FF0000';
                    $data = array(
                             "first"  => "亲爱的家人，恭喜你成功购买".$order->order_name,
                             "keyword1"   => "听课",
                             "keyword2"  => "".$order->created_at,
                             "keyword3"  => $order->order_name,
                             "remark" => "记得在线记笔记、写作业，可以获得积分哦！用学习跟上孩子的成长。祝您生活愉快。",
                            );
                    $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($userId)->send();
                    break;
                case 3: //壹家壹
                    $opo = Opo::find($order->pay_id);
                    if($opo!=null) {
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
                            'keyword1'=>'壹家壹',
                            'keyword2'=> (string)$order->pay_time,
                            'keyword3'=> '定制成长，也定制幸福的壹家壹服务',
                            'remark'=> '请保持手机畅通，专家老师将在24小时内与你取得联系，开展服务。做中国好爸妈，获家庭真幸福。'
                        ],
                    ]);
                    break;
                case 4: //问答提问
                    $notice = Wechat::notice();
                    //给提问者通知
                    $notice->send([
                        'touser' => $order->user->openid,
                        'template_id' => '7hXsOVA4WE3nGyta1UQRqUOtDP6C1D5ymR-E46_X1Ts',
                        'url' => route('wechat.question', ['id'=>$order->question->id]),
                        'topcolor' => '#f7f7f7',
                        'data' => [
                            'first' => '订单状态提示',
                            'keyword1'=>'问答',
                            'keyword2'=> (string)$order->pay_time,
                            'keyword3'=> $order->order_name,
                            'remark'=> '亲爱的家人，你的问题已成功送达给'.($order->question->answer_user->nickname).'，我们将提醒其在48小时内回答你的问题。'
                        ],
                    ]);
                    //给回答者通知
                    $notice = Wechat::notice();
                    $notice->send([
                        'touser' => $order->question->answer_user->openid,
                        'template_id' => '7hXsOVA4WE3nGyta1UQRqUOtDP6C1D5ymR-E46_X1Ts',
                        'url' => route('user.answer_voice', ['id'=>$order->question->id]),
                        'topcolor' => '#f7f7f7',
                        'data' => [
                            'first' => '（指导师）家长咨询提示',
                            'keyword1'=>'问答',
                            'keyword2'=> (string)$order->pay_time,
                            'keyword3'=> $order->order_name,
                            'remark'=> '魅力爆棚了！亲爱的家人，你有新的提问！请在48小时内组织好语言，清晰明了地回答ta的问题，分享家庭成长经历，传播幸福正能量，赚取满满成就感！'
                        ],
                    ]);
                    break;
                //问答偷听
                case 5:
                    break;
                //和会员
                case 6:
                    //会员开通成功，给用户提示
                    send_sms([$order->user->mobile], '亲爱的家人，恭喜您成功开通和会员！成为学习型父母，构建学习型家庭，助您生活愉快！');
                    $notice = Wechat::notice();
                    $notice->send([
                        'touser' => $order->user->openid,
                        'template_id' => '7hXsOVA4WE3nGyta1UQRqUOtDP6C1D5ymR-E46_X1Ts',
                        'url' => "http://m.hrwq.com/user/profile/edit",
                        'topcolor' => '#f7f7f7',
                        'data' => [
                            'first' => '和会员开通提示',
                            'keyword1'=>'和会员',
                            'keyword2'=> (string)$order->pay_time,
                            'keyword3'=> '恭喜你成功加入和润万青父母学院',
                            'remark'=> '点击此处立即完善个人信息，客服老师会在1-2个工作日内，联系您开通直播权限'
                        ],
                    ]);
                    break;
            }
        } 
    }
}
