<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseComment extends Model 
{
    protected $table = 'course_comment';
    use SoftDeletes;

    protected $fillable = ['title', 'picture', 'promoter', 'hardware', 'type'];

    public function course()
    {
        return $this->belongsTo('App\Models\course','course_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

}
