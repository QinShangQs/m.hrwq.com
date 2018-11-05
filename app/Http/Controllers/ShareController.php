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
        $lovebg64 = '';
        $name_color = '';
        $instance = \App\Models\Tooler::getByType(\App\Models\Tooler::TYPE_LOVE_BG);

        if(!empty($instance['content']['base64'])){
            $lovebg64 = $instance['content']['base64'];
            $name_color = $instance['content']['name_color'];
        }else{
            $lovebg64 = $this->base64EncodeImage(public_path('images/share/love-bg.jpg')) ;
        }
    	return view('share.love_angle', ['data' => user_info(),'lovebg64'=>$lovebg64,'name_color'=>$name_color]);
    }
    
    public function hot($id){    	
    	$back = request('back');
    	if(!empty($back)){
    		return redirect($back);
    	}
    	
    	$userinfo = user_info();
    	if($userinfo['vip_flg'] == 2){
    		return redirect("/vcourse");
    	}else if($userinfo['mobile'] || $userinfo['vip_flg'] == 1){
    		return redirect("/article/6");
    	}else{
    		return view('share.hot',['user'=>$userinfo]);
    	}
    }
    
    public function audio(){
    	return view('share.hot');
    }
    
    private function base64EncodeImage ($image_file) {
    	$base64_image = '';
    	$image_info = getimagesize($image_file);
    	$image_data = fread(fopen($image_file, 'r'), filesize($image_file));
    	$base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
    	return $base64_image;
    }
}
