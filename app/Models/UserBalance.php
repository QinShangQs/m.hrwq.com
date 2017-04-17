<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBalance extends Model
{
    protected $table = 'user_balance';

    protected $fillable = ['user_id', 'amount', 'operate_type', 'source', 'remark', 'read_flg'];
}
