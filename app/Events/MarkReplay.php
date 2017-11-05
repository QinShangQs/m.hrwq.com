<?php

namespace App\Events;

use App\Events\Event;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MarkReplay extends Event{
	use SerializesModels;
	
	public $vcourseTitle;
	public $reciverOpenId;
	public $replayUserNicker;
	public $replayContent;
	
	public function __construct($_vcourseTitle ,$_reciverOpenId, $_replayUserNicker, $_replayContent){
		$this->vcourseTitle = $_vcourseTitle;
		$this->reciverOpenId = $_reciverOpenId;
		$this->replayUserNicker = $_replayUserNicker;
		$this->replayContent = $_replayContent;
	}
	
	public function broadcastOn(){
		return [];
	}
}