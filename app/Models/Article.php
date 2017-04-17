<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Article
 *
 * @property integer $id
 * @property string $title 标题
 * @property boolean $type 文章类型 1.服务协议 2.关于我们
 * @property string $content 主题内容
 * @property integer $add_uid 添加人
 * @property integer $update_uid 最后更新人
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property-read \App\Models\Admin $add_user
 * @property-read \App\Models\Admin $update_user
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Article whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Article whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Article whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Article whereContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Article whereAddUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Article whereUpdateUid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Article whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Article whereDeletedAt($value)
 * @mixin \Eloquent
 */
class Article extends Model
{
    use SoftDeletes;
    protected $table = 'article';
    protected $fillable = ['title', 'type', 'content', 'add_uid','update_uid'];


    public function add_user() {
        return $this->belongsTo('App\Models\Admin','add_uid');
    }

    public function update_user() {
        return $this->belongsTo('App\Models\Admin','update_uid');
    }
}
