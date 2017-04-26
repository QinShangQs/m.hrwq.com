<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ShareController extends Controller
{
    public function index()
    {
        get_score(2);
        return response()->json(['code'=>0, 'message'=>'分享成功！']);
    }
    
    public function loveAngle(){
    	return view('share.love_angle');
    }
}
