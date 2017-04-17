<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomeScale extends Model
{
    protected $table = 'income_scale';
    use SoftDeletes;
    protected $fillable = ['key', 'value'];
}
