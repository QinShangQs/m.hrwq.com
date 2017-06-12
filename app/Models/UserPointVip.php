<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPointVip extends Model
{
    protected $table = 'user_point_vip';

    protected $fillable = ['user_id', 'point_value', 'source', 'remark'];
}
