<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPartnerCardImages extends Model {

    protected $table = 'user_partner_card_images';

    protected $fillable = ['user_id', 'url'];
    
    use SoftDeletes;
}
