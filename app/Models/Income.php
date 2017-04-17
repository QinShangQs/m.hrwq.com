<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $table = 'income';
    protected $fillable = ['user_id', 'log_type', 'income_type', 'order_id','order_no' ,'amount', 'pay_mod','remark','user_nickname','total_amount'];

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order','order_id');
    }

}
