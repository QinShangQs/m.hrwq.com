<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveWord extends Model
{
    protected $table = 'leave_word';

    public static  function add($user_id, $content){
    	$upv = new LeaveWord();
    	$upv->user_id = $user_id;
    	$upv->content = $content;
    	$upv->created_at = time();
    	return $upv->save();
    }
}
