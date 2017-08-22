<?php

namespace App\Listeners;

use Wechat;

class RegisterPeopleEventListener {
	public function subscribe($events) {
		$events->listen ( 'App\Events\RegisterPeople',
		 'App\Listeners\RegisterPeopleEventListener@onQuestionAnswered' );
	}
	
	public function onQuestionAnswered($event)
	{
		$user = $event->user;
		$notice = Wechat::notice();
		//推荐成功通知
		$notice->send([
				'touser' => $user->openid,
				//'template_id' => '_4amRiyCsiyXPX39Kdyqrv1ULhm_lQvdRBCwY4wLycw',
				'template_id' => '4INUjSnTAocBSKT4n0XXAL1xsQSiFgruRHX6QJfZ2_U',//正式
				'url' => route('user'),
				'topcolor' => '#f7f7f7',
				'data' => [
						'first' => '您已成功推荐一名家长注册和润万青父母学院，送您7天和会员奖励。'."\n",
						'keyword1'=> $user->nickname."\n",
						'keyword2'=> date('Y-m-d H:i:s')."\n",
						'remark'=> '点击此处查看我的和会员期限'
				],
		]);
	}
}