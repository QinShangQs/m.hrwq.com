<?php

namespace App\Listeners;

use Wechat;
use Log;

class RegisterPeopleEventListener {

    public function subscribe($events) {
        $events->listen('App\Events\RegisterPeople', 'App\Listeners\RegisterPeopleEventListener@onQuestionAnswered');
    }

    //测试
    #private $_template_id = "_4amRiyCsiyXPX39Kdyqrv1ULhm_lQvdRBCwY4wLycw";
    //正式
    private $_template_id = "4INUjSnTAocBSKT4n0XXAL1xsQSiFgruRHX6QJfZ2_U";

    public function onQuestionAnswered($event) {
        if($event->haoke){
            $this->onHaoke($event);
        }else if ($event->pay) {
            $this->onPaid($event);
        } else {
            $this->onRegister($event);
        }
    }

    private function onRegister($event) {
        $user = $event->user;
        try {
            $notice = Wechat::notice();
            //推荐成功通知
            $notice->send([
                'touser' => $user->openid,
                'template_id' => $this->_template_id,
                'url' => route('user'),
                'topcolor' => '#f7f7f7',
                'data' => [
                    'first' => '您已成功推荐一名家长注册和润万青父母学院，送您7天和会员奖励。' . "\n",
                    'keyword1' => $user->nickname . "\n",
                    'keyword2' => date('Y-m-d H:i:s') . "\n",
                    'remark' => '点击此处查看我的和会员期限'
                ],
            ]);
        } catch (\Exception $e) {
            Log::warning("发生异常：".$e->getMessage());
        }
    }
    
    private function onHaoke($event) {
        $user = $event->user;
        try {
            $notice = Wechat::notice();
            //推荐成功通知
            $notice->send([
                'touser' => $user->openid,
                'template_id' => $this->_template_id,
                'url' => route('user'),
                'topcolor' => '#f7f7f7',
                'data' => [
                    'first' => '您已成功推荐一名家长报名线下课程，已赠送您30天会员时长奖励。' . "\n",
                    'keyword1' => $event->haokeNicker . "\n",
                    'keyword2' => date('Y-m-d H:i:s') . "\n",
                    'remark' => '点击此处查看我的和会员期限'
                ],
            ]);
        } catch (\Exception $e) {
            Log::warning("发生异常：".$e->getMessage());
        }
    }

    private function onPaid($event) {
        $user = $event->user;
        try {
            $notice = Wechat::notice();
            $notice->send([
                'touser' => $user->openid,
                'template_id' => $this->_template_id,
                'url' => route('user.balance'),
                'topcolor' => '#f7f7f7',
                'data' => [
                    'first' => '您已成功推荐一名家长成为和会员,送您一笔爱心奖金。' . "\n",
                    'keyword1' => $user->nickname . "\n",
                    'keyword2' => date('Y-m-d H:i:s') . "\n",
                    'remark' => '单击此处查看我的奖金'
                ],
            ]);
        } catch (\Exception $e) {
            Log::warning("发生异常：".$e->getMessage());
        }
    }

}
