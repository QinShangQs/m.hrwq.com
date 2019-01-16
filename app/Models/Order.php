<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $table = 'order';
    protected $fillable = ['order_code', 'user_id', 'pay_id', 'pay_type', 'order_type', 'order_name', 'free_flg', 'price', 'pay_time', 'pay_method', 'each_price', 'coupon_user_id', 'coupon_price', 'balance_price', 'pay_notify_flg', 'course_start_notify_flg', 'partner_flg'];

    /**
     * 团购订单
     */
    const IS_TEAM_YES = 1;
    /**
     * 非团购订单
     */
    const IS_TEAM_NO = 0;
    
     /**
     * 付款-未付款
     */
    const PYA_NO = 1;
    /**
     * 付款-已付款
     */
    const PYA_HAVED = 2;
    /**
     * 付款-已取消
     */
    const PYA_CANCEL = 3;
    /**
     * 付款-已完成
     */
    const PAY_FINISH = 4;
    
    public function order_course()
    {
        return $this->hasOne('App\Models\OrderCourse', 'order_id');
    }

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'pay_id');
    }

    public function order_opo()
    {
        return $this->hasOne('App\Models\OrderOpo', 'order_id');
    }

    public function order_vip()
    {
        return $this->hasOne('App\Models\OrderVip', 'order_id');
    }

    public function opo()
    {
        return $this->belongsTo('App\Models\Opo', 'pay_id');
    }

    public function vcourse()
    {
        return $this->belongsTo('App\Models\Vcourse', 'pay_id');
    }

    public function question()
    {
        return $this->belongsTo('App\Models\Question', 'pay_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
