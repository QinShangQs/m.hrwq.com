<?php

namespace App\Listeners;

use Wechat;

class QuestionEventListener
{
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\QuestionAnswered',
            'App\Listeners\QuestionEventListener@onQuestionAnswered'
        );
    }

    public function onQuestionAnswered($event)
    {
        $question = $event->question;
        $notice = Wechat::notice();
        $notice->send([
            'touser' => $question->ask_user->openid,
            'template_id' => '7hXsOVA4WE3nGyta1UQRqUOtDP6C1D5ymR-E46_X1Ts',
            'url' => route('wechat.question', ['id'=>$question->id]),
            'topcolor' => '#f7f7f7',
            'data' => [
                'first' => '订单状态提示',
                'keyword1'=>'问答',
                'keyword2'=> (string)$question->answer_date,
                'keyword3'=> '亲爱的家人，'.$question->answer_user->nickname.'已认真回答你的提问。',
                'remark'=> '您的问答每被旁听1次，您都将获得0.5元奖励，快分享给好友旁听吧！'
            ],
        ]);
    }
}
