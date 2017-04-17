<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TalkComment extends Model
{
    use SoftDeletes;
    protected $table = 'talk_comment';
    protected $fillable = ['talk_id','r_user_id','comment_c','likes'];

    public function answer_user()
    {
        return $this->belongsTo('App\Models\User','r_user_id');
    }

    public function talk()
    {
        return $this->belongsTo('App\Models\Talk','talk_id');
    }

    public function like_record()
    {
        return $this->hasMany('App\Models\LikeRecord','like_id');
    }
}
