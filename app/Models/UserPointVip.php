<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPointVip extends Model
{
    protected $table = 'user_point_vip';

    protected $fillable = ['user_id', 'point_value', 'source', 'remark'];
    
    public static  function add($user_id, $point_value, $source){
    	$upv = new UserPointVip();
    	$upv->user_id = $user_id;
    	$upv->point_value = $point_value;
    	$upv->source = $source;
    	$upv->created_at = time();
    	$upv->save();
    }
}
