<?php

namespace App\Listeners;

use Wechat;

class VipLeftDayEventListener {
	public function subscribe($events) {
		$events->listen ( 'App\Events\VipLeftDay',
		 'App\Listeners\VipLeftDayEventListener@onTemplate' );
	}
	
	public function onTemplate($event) {
		$left_day = $event->user->left_day;
		$first = "";
		if($left_day === 3){
		 	$first = "您的和会员有效期仅剩3天，到期后将无法享受会员权限，请及时开通会员或进行续费。";
		}else if($left_day === 1){
			$first = "您的和会员有效期仅剩最后1天，到期后将无法享受会员权限，请及时开通会员或进行续费。";
		}else if($left_day === 0){
			$first = "您的和会员已到期，已无法享受会员权限，请及时开通会员或进行续费。";
		}else{
			return;//未开通会员
		}

		$user = $event->user;
		$notice = Wechat::notice();
		//推荐成功通知
		$notice->send([
				'touser' => $user->openid,
				//'template_id' => '6k5ewbm_oKb9oWRBEzGD3ev2J9ST6vQAwz7GOc9P8eU',
				'template_id' => 'tXoTWyFqxISHKZBpmkSdWbKbZPFi4KljM-QTzJ51xw4',//正式
				'url' => route('user'),
				'topcolor' => '#f7f7f7',
				'data' => [
						'first' => $first."\n",
						'keyword1'=> date("Y-m-d",strtotime($user->created_at) )."\n",
						'keyword2'=> $user->vip_left_day."\n",
						'remark'=> '点击此处查看详情'
				],
		]);
	}
}