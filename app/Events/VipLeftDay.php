<?php

namespace App\Events;

use App\Events\Event;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class VipLeftDay extends Event{
	use SerializesModels;
	
	public $user;
	
	public function __construct($user){
		$this->user = $user;
	}
	
	public function broadcastOn(){
		return [];
	}
}