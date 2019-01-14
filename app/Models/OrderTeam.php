<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class OrderTeam extends Model {

    protected $table = 'order_team';
    protected $guarded = ['id'];

    use SoftDeletes;

    /**
     * 发起中
     */
    const STATUS_INIT = 0;

    /**
     * 组团成功
     */
    const STATUS_SUCCESS = 1;

    /**
     * 组团失败
     */
    const STATUS_FAILED = 2;

    public function order() {
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

    public static function getTeamByOrderId($order_id) {
        $data = OrderTeam::where(['order_id' => $order_id])->first();
        return empty($data) ? null : $data->toArray();
    }

    public static function getTeamById($id) {
        $data = OrderTeam::find($id);
        return empty($data) ? null : $data->toArray();
    }

    /**
     * 参加团购
     * @param type $team_id 0是新开团，非0加入老团
     * @param type $order_id
     * @param type $user_id
     * @param float $price 团购价
     * @param integer $tuangou_days 团购天数 新开团必填
     * @param integer $need_members_cnt 组团人数 新开团必填
     * @throws \Exception
     */
    public static function makeOrderTeam($team_id, $order_id, $user_id, $price, $tuangou_days, $need_members_cnt) {
        if (!static::judgeUserIsJoined($team_id, $user_id)) {
            throw new \Exception('您已参与该课程团购，不可重复参与。');
        }

        $isNewTeam = true;
        if (empty($team_id)) {
            $team = new OrderTeam();
            $team->order_id = $order_id;
            $team->initiator_user_id = $user_id;
            $team->status = static::STATUS_INIT;
            $team->price = $price;
            $team->need_members_cnt = $need_members_cnt;
            $team->ended_at = date('Y-m-d H:i:s', strtotime("+ {$tuangou_days} day"));
            $team->save();
            $team_id = $team->id;
        } else {
            $isNewTeam = false;
            if (!static::judgeTeamInActive($team_id)) {
                throw new \Exception('该团不存在或该团活动已结束。');
            }
        }

        $teamMember = new OrderTeamMember();
        $teamMember->order_team_id = $team_id;
        $teamMember->user_id = $user_id;
        $teamMember->member_type = $isNewTeam ? OrderTeamMember::MEMBER_TYPE_INITIATOR : OrderTeamMember::MEMBER_TYPE_JOINER;
        $teamMember->order_id = $order_id;
        $teamMember->save();
    }

    /**
     * 该团是否活跃可以参加
     */
    public static function judgeTeamInActive($team_id) {
        $team = OrderTeam::find($team_id);
        if (empty($team)) {
            return false;
        }

        return $team->status == static::STATUS_INIT;
    }

    /**
     * 用户是否已参与该团
     * @param type $team_id
     * @param type $user_id
     * @return boolean
     */
    public static function judgeUserIsJoined($team_id, $user_id) {
        if (empty($team_id)) {
            return true;
        }

        $member = OrderTeamMember::where(['order_team_id' => $team_id, 'user_id' => $user_id])->first();
        return empty($member);
    }

    /**
     * 获取团购成员
     * @param type $team_id
     * @return array
     */
    public static function findAllMembers($team_id) {
        $members = OrderTeamMember::where(['order_team_id' => $team_id])->get();
        if (empty($members)) {
            return null;
        }

        $datas = [];
        foreach ($members as $m) {
            $user = User::find($m->user_id);
            $datas[] = array_merge($user->toArray(), [
                'member_type' => $m->member_type,
                'join_time' => $m->created_at
            ]);
        }

        return $datas;
    }

}
