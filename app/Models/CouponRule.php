<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponRule extends Model
{
    use SoftDeletes;

    protected $table = 'coupon_rule';
    protected $fillable = ['name', 'coupon_id','agency_id','bouns','rule_id'];
}
