<?php

namespace App\Listeners;

use Wechat;

class RegisterPeopleEventListener {
	public function subscribe($events) {
		$events->listen ( 'App\Events\RegisterPeople',
		 'App\Listeners\RegisterPeopleEventListener@onQuestionAnswered' );
	}
	
	//测试
	#private $_template_id = "_4amRiyCsiyXPX39Kdyqrv1ULhm_lQvdRBCwY4wLycw";
	//正式
	private $_template_id = "4INUjSnTAocBSKT4n0XXAL1xsQSiFgruRHX6QJfZ2_U";
	
	public function onQuestionAnswered($event) {
		if($event->pay){
			$this->onPaid($event);			
		}else{
			$this->onRegister($event);
		}
	}
	private function onRegister($event){
		$user = $event->user;
		$notice = Wechat::notice();
		//推荐成功通知
		$notice->send([
				'touser' => $user->openid,
				'template_id' => $this->_template_id,
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
	
	private function onPaid($event) {
		$user = $event->user;
		$notice = Wechat::notice();
		$notice->send([
				'touser' => $user->openid,
				'template_id' => $this->_template_id,
				'url' => route('user.balance'),
				'topcolor' => '#f7f7f7',
				'data' => [
						'first' => '您已成功推荐一名家长成为和会员,送您一笔爱心奖金。'."\n",
						'keyword1'=> $user->nickname."\n",
						'keyword2'=> date('Y-m-d H:i:s')."\n",
						'remark'=> '单击此处查看我的奖金'
				],
		]);
	}
}