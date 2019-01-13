<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBalance extends Model {

    protected $table = 'user_balance';
    protected $fillable = ['user_id', 'amount', 'operate_type', 'source', 'remark', 'read_flg'];

    /**
     * 增加
     */
    const OPERATE_TYPE_INCREMENT = 1;

    /**
     * 减少
     */
    const OPERATE_TYPE_DECREMENT = 2;

    /**
     * 修改账户余额
     * @param type $user_id
     * @param type $amount
     * @param type $operate_type 1增加2减少
     * @param type $source
     * @param type $remark
     */
    public static function change($user_id, $amount, $operate_type, $source, $remark) {
        $user = User::find($user_id);
        if ($operate_type == static::OPERATE_TYPE_INCREMENT) {
            $user->increment('current_balance', $amount);  //总收益 & 余额 ++
            $user->increment('balance', $amount);
        } else {
            $user->decrement('current_balance', $amount);  //总收益 & 余额 ++
            $user->decrement('balance', $amount);
        }

        //用户余额记录
        $user_balance = [];
        $user_balance['user_id'] = $user_id;
        $user_balance['amount'] = $amount;
        $user_balance['operate_type'] = $operate_type;
        $user_balance['source'] = $source;
        $user_balance['remark'] = $remark;
        UserBalance::create($user_balance);
    }

}
