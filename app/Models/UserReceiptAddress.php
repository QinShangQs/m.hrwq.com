<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserReceiptAddress extends Model 
{
    protected $table = 'user_receipt_address';
    use SoftDeletes;


}
