<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponUser extends Model
{
    use SoftDeletes;

    protected $table = 'coupon_user';
    protected $fillable = ['coupon_id', 'user_id','is_used','expire_at','come_from', 'read_flg'];

    public function c_user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function c_coupon()
    {
        return $this->belongsTo('App\Models\Coupon','coupon_id');
    }

}
