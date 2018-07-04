<?php

namespace App\Models;
use DB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tooler extends Model {

    /**
     * 直播链接
     */
    const TYPE_TELECAST = 1;

    /**
     * 视频预告
     */
    const TYPE_FORESHOW = 2;

    /**
     * 爱心大使
     */
    const TYPE_LOVE_BG = 3;

    protected $table = 'tooler';
    protected $fillable = ['type', 'content', 'create_at', 'updated_at', 'deleted_at'];

    public static function getByType($type) {
        $data = self::where(['type' => $type])->get()->toArray();
        if ($type == self::TYPE_LOVE_BG) {
            if (empty($data)) {
                $data['type']  =  self::TYPE_LOVE_BG;
                $data['id']  = 0;
                $data['content'] = ['base64' => '', 'name_color' => ''];
            }else{
                $data = $data[0];
                $data['content'] = json_decode($data['content'], true);
            }
        }
        return $data;
    }

    /**
     * 
     * @param string $data json字符串
     * @return type
     */
    public static function lovebgMerge($data) {
        $result = false;
        $instance = self::getByType(self::TYPE_LOVE_BG);
        if (!empty($instance['id'])) {
            $instance['content'] = $data;
            DB::update('update tooler set content = ? where id = ?',[$instance['content'],$instance['id']]);
        } else {
            $instance['type'] = self::TYPE_LOVE_BG;
            $instance['content'] = $data;
            $result = self::create($instance);
        }

        return $result;
    }

}
