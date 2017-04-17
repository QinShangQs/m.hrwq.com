<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\LikeRecord
 *
 */
class LikeRecord extends Model
{

    use SoftDeletes;
    protected $table = 'like_record';
    public $timestamps = true;

}
