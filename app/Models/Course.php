<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model {

    protected $table = 'course';
    protected $guarded = ['id'];

    use SoftDeletes;

    /**
     * 收费类型-团购
     */
    const TYPE_TEAM = 3;
    /**
     * 收费类型-付费
     */
    const TYPE_NEEDPAY = 2;
    /**
     * 收费类型-免费
     */
    const TYPE_FREE = 1;
    
    public function user() {
        return $this->belongsTo('App\Models\User', 'promoter');
    }

    public function area() {
        return $this->belongsTo('App\Models\Area', 'city');
    }

    public function agency() {
        return $this->belongsTo('App\Models\Agency', 'agency_id');
    }

    public function getTel() {
        $city = @session('user_info')['city'];
        if (!empty($city) && $this->distribution_flg == 1) {
            $partner = User::where('role', 3)->where('block', 1)
                    ->where('partner_city', $city)
                    ->orderBy('created_at', 'desc')
                    ->first();
            if ($partner) {
                return $partner->mobile;
            }
        }
        return $this->tel;
    }

}
