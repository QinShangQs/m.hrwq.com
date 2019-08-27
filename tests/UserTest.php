<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\UserPointVip;
use Event;

class UserTest extends TestCase {

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample() {
        $this->assertTrue(true);
    }

    public function testVipSource() {
        UserPointVip::add(12023, 7, 2);
    }

    public function testAssign() {
        $result = App\Models\VipTv::assign('12016');
        dd($result);
    }

    public function testOrder() {
        $order = App\Models\Order::find(35842);
        Event::fire(new App\Events\OrderPaid($order));
    }
    
    public function testSms() {
        send_sms(['13146182306'], "你好");
    }
}
