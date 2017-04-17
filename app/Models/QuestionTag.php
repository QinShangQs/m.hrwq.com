<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionTag extends Model
{
    protected $table='question_tag';

    public function tag()
    {
        return $this->belongsTo('App\Models\Tag','tag_id');
    }
}
