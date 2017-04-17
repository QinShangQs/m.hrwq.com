<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HotSearch extends Model
{
    use SoftDeletes;

    protected $table = 'hot_search';
    protected $fillable = ['title', 'sort','type'];
}
