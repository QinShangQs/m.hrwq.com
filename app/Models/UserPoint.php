<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{
    protected $table = 'user_point';

    protected $fillable = ['user_id', 'point_value', 'source', 'move_way', 'remark', 'read_flg'];
}
