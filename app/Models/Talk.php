<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Talk extends Model
{
    use SoftDeletes;
    protected $table = 'talk';

    protected $perPage = 10;

    protected $fillable = ['user_id','title','content','view'];

    public function ask_user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\TagWithTrash', 'talk_tag', 'talk_id', 'tag_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\TalkComment');
    }
}
