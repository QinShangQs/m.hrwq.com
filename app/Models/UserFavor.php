<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFavor extends Model
{
    protected $table = 'user_favor';
    protected $fillable = ['favor_type', 'favor_id','user_id'];

    public function favor_teacher()
    {
        return $this->belongsTo('App\Models\User','favor_id');
    }

    public function course()
    {
        return $this->belongsTo('App\Models\Course','favor_id');
    }

    public function vcourse()
    {
        return $this->belongsTo('App\Models\Vcourse','favor_id');
    }
}
