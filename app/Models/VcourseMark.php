<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\VcourseMark
 *
 */
class VcourseMark extends Model
{

    use SoftDeletes;
    protected $table = 'vcourse_mark';
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function vcourse()
    {
        return $this->belongsTo('App\Models\Vcourse','vcourse_id');
    }

    public function like_record()
    {
        return $this->hasMany('App\Models\LikeRecord','like_id');
    }
}
