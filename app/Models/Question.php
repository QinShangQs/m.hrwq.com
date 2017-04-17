<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;
    protected $table = 'question';
    protected $perPage = 10;
    protected $fillable = ['content', 'free_flg','free_from','free_end','price','answer_flg','answer_url','voice_long','vip_free','user_id','tutor_id','listener_nums','answer_origin_key','answer_date', 'new_answer_flg', 'to_answer_flg', 'sort'];

    public function ask_user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function answer_user()
    {
        return $this->belongsTo('App\Models\User','tutor_id');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\TagWithTrash', 'question_tag', 'question_id', 'tag_id');
    }

    public function listener()
    {
        return $this->belongsToMany('App\Models\User','question_listener', 'question_id', 'user_id');
    }

    public function order()
    {
        return $this->hasMany('App\Models\Order','pay_id')->where('pay_type',4);
    }
}
