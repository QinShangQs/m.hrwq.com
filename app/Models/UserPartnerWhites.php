<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPartnerWhites extends Model {

    public $incrementing = false;
    protected $primaryKey = 'user_id';
    protected $table = 'user_partner_whites';
    protected $fillable = [];

    use SoftDeletes;
    
    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
}
