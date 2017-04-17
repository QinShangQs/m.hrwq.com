<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $table = 'coupon';
    protected $fillable = ['name', 'type','full_money','cut_money','discount','use_scope','use_scope_val','available_period_type','available_days','available_start_time','available_end_time'];


    public function coupon_user()
    {
        return $this->hasMany('App\Models\CouponUser');
    }
}
