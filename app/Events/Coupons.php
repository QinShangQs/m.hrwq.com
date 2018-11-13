<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class Coupons extends Event{
	use SerializesModels;
	
	public $user;
	
	public function __construct($user){
		$this->user = $user;
	}
	
	public function broadcastOn(){
		return [];
	}
}