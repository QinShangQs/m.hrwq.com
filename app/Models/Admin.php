<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    protected $table = 'admin';
    use Authenticatable, Authorizable, CanResetPassword, SoftDeletes;
    const ADMIN_TYPE_SUPER = 1;         //超级管理员
    protected $fillable = ['admin_type', 'name', 'mobile', 'email', 'password'];

    public static function getAdminTypeStr($adminType)
    {
        switch ($adminType) {
            case self::ADMIN_TYPE_SUPER:
                return '超级管理员';
                break;
            default:
                return '未知';
        }
    }
}
