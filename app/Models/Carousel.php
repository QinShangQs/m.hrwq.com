<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Carousel
 *
 * @property integer $id
 * @property string $title
 * @property string $image_url
 * @property string $image_thumb_url
 * @property boolean $redirect_type 跳转类型 1=> '不跳转',2=> '外部链接',3=>'本地静态页'
 * @property string $redirect_url 跳转地址
 * @property string $redirect_content 图片静态内容
 * @property integer $sort
 * @property integer $add_uid
 * @property integer $update_uid
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property-read \App\Models\Admin $add_user
 * @property-read \App\Models\Admin $update_user
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Carousel whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Carousel whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Carousel whereImageUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Carousel whereImageThumbUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Carousel whereRedirectType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Carousel whereRedirectUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Carousel whereRedirectContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Carousel whereSort($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Carousel whereAddUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Carousel whereUpdateUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Carousel whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Carousel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Carousel whereDeletedAt($value)
 * @mixin \Eloquent
 */
class Carousel extends Model
{
    use SoftDeletes;
    protected $table = 'carousel';
    protected $fillable = ['title','image_url' ,'image_thumb_url','sort','redirect_type','redirect_url','redirect_content', 'add_uid','update_uid','use_type'];

    public function add_user() {
        return $this->belongsTo('App\Models\Admin','add_uid');
    }

    public function update_user() {
        return $this->belongsTo('App\Models\Admin','update_uid');
    }
}
