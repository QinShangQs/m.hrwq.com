<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagWithTrash extends Model
{
    protected $table = 'tag';
    protected $fillable = ['title', 'sort'];

    public function questions()
    {
        return $this->belongsToMany('App\Models\Question', 'question_tag', 'tag_id', 'question_id');
    }
}
