<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class Tuangou extends Event {

    use SerializesModels;

    public $std;

    public function __construct(\stdClass $std) {
        $this->std = $std;
    }

    public function broadcastOn() {
        return [];
    }

}
