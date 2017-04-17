<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeCash extends Model
{
    protected $table = 'income_cash';
    protected $fillable = ['user_id', 'apply_status','refuse_reason','cash_amount'];


    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
}
