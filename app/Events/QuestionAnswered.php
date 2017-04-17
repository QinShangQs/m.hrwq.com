<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Question;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class QuestionAnswered extends Event
{
    use SerializesModels;

    public $question;

    /**
     * QuestionAnswered constructor.
     * @param Question $question
     */
    public function __construct(Question $question)
    {
        $this->question = $question;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
