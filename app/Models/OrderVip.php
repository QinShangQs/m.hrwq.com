<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderVip extends Model
{
    use SoftDeletes;
    protected $table = 'order_vip';

    protected $guarded = ['id'];
}
