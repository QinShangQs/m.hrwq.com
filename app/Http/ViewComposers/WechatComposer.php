<?php

namespace App\Http\ViewComposers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Cache, Wechat;

class WechatComposer
{
    public function __construct()
    {

    }

    public function compose(View $view)
    {
        $user_info = session('user_info');
        $has_mobile = $user_info && User::hasMobile($user_info['id']);

        $view->with('is_guest', !$has_mobile);
        $view->with('wx_js', Wechat::js());
    }
}