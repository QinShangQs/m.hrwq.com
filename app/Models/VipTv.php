<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VipTv extends Model {

    protected $table = 'vip_tv';

    use SoftDeletes;

    protected $guarded = [];

    public function user() {
        return $this->belongsTo('App\Models\User', 'activated_vip');
    }

    const ACTIVATED_NO = 1;
    const ACTIVATED_YES = 2;
    
    /**
     * 分配直播激活码
     * @param integer $user_id
     * @return string
     */
    public static function assign($user_id){
        $viptv = VipTv::where(['is_activated' => static::ACTIVATED_NO])->orderBy('created_at', 'desc')->first();
        if(empty($viptv)){
            return null;
        }
        $viptv->is_activated = static::ACTIVATED_YES;
        $viptv->activated_vip = $user_id;
        $viptv->save();
        return $viptv->code;
    }
}
