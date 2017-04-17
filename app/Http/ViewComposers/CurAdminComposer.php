<?php

namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Auth;

class CurAdminComposer
{
    public function compose(View $view)
    {
        $view->with('curAdmin', Auth::user());
    }
}