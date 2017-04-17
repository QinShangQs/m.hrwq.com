<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    protected $table = 'tag';
    protected $fillable = ['title', 'sort'];

    public function questions()
    {
        return $this->belongsToMany('App\Models\Question', 'question_tag', 'tag_id', 'question_id');
    }

    public function talks()
    {
        return $this->belongsToMany('App\Models\Talk', 'talk_tag', 'tag_id', 'talk_id');
    }
}
