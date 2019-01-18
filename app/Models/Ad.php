<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model {

    protected $table = 'ad';

    use SoftDeletes;

    /**
     * 广告类型-图片
     */
    const AD_TYPE_IMAGE = 1;

    /**
     * 广告类型-视频
     */
    const AD_THYP_VIDEO = 2;

    /**
     * 是否显示-是
     */
    const SHOW_TYPE_YES = 1;

    /**
     * 是否显示-否
     */
    const SHOW_TYPE_NO = 2;
   

    public static function getRandomOne($ad_type) {
        $ads = Ad::where(['ad_type' => $ad_type, 'show_type' => static::SHOW_TYPE_YES])->get();
        if (!empty($ads) && count($ads) > 0) {
            return $ads[rand(0, count($ads) - 1)];
        }

        return null;
    }

}
