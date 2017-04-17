<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TalkTag extends Model
{
    protected $table='talk_tag';

    public function tag()
    {
        return $this->belongsTo('App\Models\Tag','tag_id');
    }
}
