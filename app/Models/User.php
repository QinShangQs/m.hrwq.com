<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;
    protected $perPage = 10;
    protected $table = 'user';

    protected $guarded = [];

    public function c_province()
    {
        return $this->belongsTo('App\Models\Area','province','area_id');
    }

    public function c_city()
    {
        return $this->belongsTo('App\Models\Area','city','area_id');
    }

    public function c_district()
    {
        return $this->belongsTo('App\Models\Area','district','area_id');
    }

    public function partner_city()
    {
        return $this->belongsTo('App\Models\Area','partner_city','area_id');
    }

    public function user_balance()
    {
        return $this->hasMany('App\Models\UserBalance','user_id');
    }


    public function user_point()
    {
        return $this->hasMany('App\Models\UserPoint','user_id');
    }

    public function user_favor()
    {
        return $this->hasMany('App\Models\UserFavor','favor_id');
    }


    public function question()
    {
        return $this->hasMany('App\Models\Question','tutor_id');
    }

    public function balance_record()
    {
        return $this->hasMany('App\Models\UserBalance','user_id');
    }

    public function cash_record()
    {
        return $this->hasMany('App\Models\IncomeCash','user_id');
    }

    public function like_record(){
        return $this->hasMany('App\Models\LikeRecord','like_id');
    }

    public static function hasMobile($user_id)
    {
        if ($user_id > 0) {
            $userInfo = User::whereId($user_id)->first();
            return $userInfo && $userInfo['mobile'] > 0;
        }
        return false;
    }
}
