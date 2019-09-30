<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Coupon;

class CouponUser extends Model {

    use SoftDeletes;

    protected $table = 'coupon_user';
    protected $fillable = ['coupon_id', 'user_id', 'is_used', 'expire_at', 'come_from', 'read_flg'];

    public function c_user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function c_coupon() {
        return $this->belongsTo('App\Models\Coupon', 'coupon_id');
    }

    /**
     * 领取优惠券
     * @param type $coupon_id
     * @param type $user_id
     * @return \stdClass
     */
    public static function receiveOne($coupon_id, $user_id) {
        $result = new \stdClass();
        $result->success = false;
        $result->message = '';
        
        if(CouponUser::where('coupon_id', $coupon_id)->where('user_id', $user_id)->first()){
            $result->success = false;
            $result->message = '您已领取过该优惠券了！';
            return $result;
        }
        
        $coupon = Coupon::find($coupon_id);
        if ($coupon->available_period_type == 1) {
            $expire_at = date('Y-m-d h:i:s', time() + 86400 * $coupon->available_days);
        } else {
            $expire_at = $coupon->available_end_time;
        }

        $data = [];
        $data['coupon_id'] = $coupon_id;
        $data['user_id'] = $user_id;
        $data['come_from'] = 4;
        $data['expire_at'] = $expire_at;
        $data['created_at'] = date('Y-m-d h:i:s');
        
        CouponUser::create($data);
        $result->success = true;
        $result->message = '您已成功领取优惠券！';
        return $result;
    }

}
