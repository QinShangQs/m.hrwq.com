<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPointVip extends Model
{
    protected $table = 'user_point_vip';

    protected $fillable = ['user_id', 'point_value', 'source', 'remark'];
    
    
    /**
     * 被分享奖励-来源
     */
    const SOURCE_BESHARED = 3;
    
    public static function add($user_id, $point_value, $source){
        if($source == static::SOURCE_BESHARED){
            $old = UserPointVip::where(['user_id' => $user_id, 'source' => $source])->select(['created_at'])->first();
            if(!empty($old)){
                return;
            }
        }
        
    	$upv = new UserPointVip();
    	$upv->user_id = $user_id;
    	$upv->point_value = $point_value;
    	$upv->source = $source;
    	$upv->created_at = time();
    	$upv->save();
    }
}
