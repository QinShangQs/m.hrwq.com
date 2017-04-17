<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionListener extends Model
{
    protected $table = 'question_listener';
    protected $fillable = ['question_id', 'is_free','user_id'];

    public function question()
    {
        return $this->belongsTo('App\Models\Question','id');
    }

}
