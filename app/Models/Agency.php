<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Agency
 *
 */
class Agency extends Model
{

    use SoftDeletes;
    protected $table = 'agency';
    public $timestamps = true;

    protected $fillable = ['agency_name', 'agency_title'];

}
