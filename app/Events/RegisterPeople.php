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
	
	public function __construct(User $user, $pay = false){
		$this->user = $user;
		$this->pay = $pay;
	}
	
	public function broadcastOn(){
		return [];
	}
}