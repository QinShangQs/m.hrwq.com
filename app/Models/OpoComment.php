<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpoComment extends Model
{
    use SoftDeletes;
    protected $table = 'opo_comment';

    public function opo()
    {
        return $this->belongsTo('App\Models\Opo', 'opo_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function like_records()
    {
        return $this->hasMany('App\Models\LikeRecord', 'like_id')->where('like_type', 3);
    }
}
