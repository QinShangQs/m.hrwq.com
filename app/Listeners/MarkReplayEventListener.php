<?php

namespace App\Listeners;

use Wechat;
use Log;

class MarkReplayEventListener {
	public function subscribe($events) {
		$events->listen ( 'App\Events\MarkReplay',
		 'App\Listeners\MarkReplayEventListener@onTemplate' );
	}
	
	public function onTemplate($event) {

		$first = '';
		
		$notice = Wechat::notice();
		
		Log::info("markReplay 通知  openid = {$event->reciverOpenId}");
		
		try {
			//推荐成功通知
			$notice->send([
					'touser' => $event->reciverOpenId,
					//'template_id' => 'QmPMOqxfBp3iWyDoEMU4GgTno0QMpP480oso1vUNDzU',
					'template_id' => 'AFC1OryYOd-qX5HACdRx8eMv0uPf2alh4igt-r6VoWc',//正式
					'url' => "http://m.hrwq.com/user",
					'topcolor' => '#f7f7f7',
					'data' => [
							'first' => "厉害了我的家人！您在《{$event->vcourseTitle}》中提交的作业收到其他家长的回复。"."\n\n",
							'keyword1'=> '作业回复'."\n\n",
							'keyword2'=> $event->replayUserNicker."\n\n",
							'keyword3'=> date("Y年m月d日")."\n",
							'remark'=> $event->replayContent . "\n\n" . '点击此处查看详情'
					],
			]);
		}catch(\Exception $e){
			Log::warning("发生异常：".$e->getMessage());
		}
		
	}
}