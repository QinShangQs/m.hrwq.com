<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseReportAdmin extends Model 
{
    protected $table = 'course_report_admin';
    use SoftDeletes;
}
