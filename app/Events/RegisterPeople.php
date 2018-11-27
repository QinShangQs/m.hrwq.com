<?php

namespace App\Events;

use App\Events\Event;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RegisterPeople extends Event{
	use SerializesModels;
	
	public $user;
	public $pay = false;
        public $haoke = false;
        public $haokeNicker = "";
	
	public function __construct(User $user, $pay = false, $haoke = false, $haokeNicker = ""){
		$this->user = $user;
		$this->pay = $pay;
                $this->haoke = $haoke;
                $this->haokeNicker = $haokeNicker;
	}
	
	public function broadcastOn(){
		return [];
	}
}