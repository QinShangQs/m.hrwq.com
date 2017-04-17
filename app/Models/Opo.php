<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opo extends Model 
{
    protected $table = 'opo';
    use SoftDeletes;


}
