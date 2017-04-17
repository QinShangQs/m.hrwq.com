<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vip extends Model 
{
    protected $table = 'vip';
    use SoftDeletes;

    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo('App\Models\User','activated_vip');
    }

}
