<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderTeamMember extends Model {

    protected $table = 'order_team_member';
    protected $guarded = ['id'];

    use SoftDeletes;
    
    /**
     * 发起人
     */
    const MEMBER_TYPE_INITIATOR = 1;
    /**
     * 参与人
     */
    const MEMBER_TYPE_JOINER = 0;

    public function team() {
        return $this->belongsTo('App\Models\OrderTeam', 'order_team_id');
    }
}
