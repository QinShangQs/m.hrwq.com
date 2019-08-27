<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vip extends Model {

    protected $table = 'vip';

    use SoftDeletes;

    const ALLOW_ONLY_YES = 1;
    const ALLOW_ONLY_NO = 2;
    const DEAFULT_DAYS = 365;

    protected $guarded = [];

    public function user() {
        return $this->belongsTo('App\Models\User', 'activated_vip');
    }

}
