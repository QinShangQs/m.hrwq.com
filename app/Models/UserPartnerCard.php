<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPartnerCard extends Model {

    public $incrementing = false;
    protected $primaryKey = 'user_id';
    protected $table = 'user_partner_card';
    protected $fillable = ['tel', 'wechat','email','address','website','remark','cover_url','video_url','video_hash'];

    use SoftDeletes;
    
    public function images()
    {
        return $this->hasMany('App\Models\UserPartnerCardImages','user_id');
    }
}
