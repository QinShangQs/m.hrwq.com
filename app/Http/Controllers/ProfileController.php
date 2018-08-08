<?php
/**
 * 用户个人资料相关
 */

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function index()
    {
        $user = User::with('c_province', 'c_city', 'c_district')->find(session('user_info')['id']);
        $user->birth = $user->birth?Carbon::createFromFormat('Y-m-d H:i:s', $user->birth)->format('Y/m/d'):'';
        $user->c_birth = $user->c_birth?Carbon::createFromFormat('Y-m-d H:i:s', $user->c_birth)->format('Y/m/d'):'';

        return view('user.profile', ['user'=>$user, 'userLabels'=>config('constants.user_label')]);
    }

    public function edit()
    {
        $userInfo = user_info();
        $userLabels = config('constants.user_label');
        $userInfo['birth'] = $userInfo['birth']?Carbon::createFromFormat('Y-m-d H:i:s', $userInfo['birth'])->format('Y/m/d'):Carbon::now()->format('Y/m/d');
        $userInfo['c_birth'] = $userInfo['c_birth']?Carbon::createFromFormat('Y-m-d H:i:s', $userInfo['c_birth'])->format('Y/m/d'):Carbon::now()->format('Y/m/d');

        return view('user.edit_profile', ['userInfo'=>$userInfo, 'userLabels'=>$userLabels]);
    }

    public function update()
    {
        $user = User::find(session('user_info')['id']);
        if ($user == null)
            return ['code'=>1, 'message'=>'用户信息查询失败'];
        $file = request('head_canvas_data');
        if(!empty($file)){
            $upload =  $this->base64_upload($file); //头像
            if(!$upload ){
                return response()->json(["code"=>2,"message"=>'头像上传失败！']);
            }
        }
        $user->profileIcon = isset($upload)?$upload:$user->profileIcon;
        $user->nickname = request()->has('nickname')?request('nickname'):$user->nickname;
        $user->realname = request()->has('realname') ? request('realname') : $user->realname;
        $user->label = request()->has('label')?request('label'):$user->label;
        $user->age = request()->has('age')?request('age'):$user->age;
        $user->birth = request()->has('birth')?Carbon::createFromFormat('Y/m/d', request('birth')):$user->birth;
        $user->address = request()->has('address')?request('address'):$user->address;
        // $user->province = request()->has('province')?request('province'):$user->province;
        // $user->city = request()->has('city')?request('city'):$user->city;
        $user->c_sex = request()->has('c_sex')?request('c_sex'):$user->c_sex;
        $user->c_birth = request()->has('c_birth')?Carbon::createFromFormat('Y/m/d', request('c_birth')):$user->c_birth;
        if($user->isDirty()) {
            $user->save();
            return ['code' => 0, 'message' => '个人资料更新成功'];
        } else {
            return ['code' => 3, 'message' => '个人资料未作修改'];
        }
    }

    private function base64_upload($base64) {
        $base64_image = str_replace(' ', '+', $base64);
        //post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)){
            //匹配成功
            if($result[2] == 'jpeg'){
                $image_name = uniqid().'.jpg';
                //纯粹是看jpeg不爽才替换的
            }else{
                $image_name = uniqid().'.'.$result[2];
            }
            $savePath = "uploads/profile/user/".session('user_info')['id']."/";
            if (!file_exists($savePath)) {
                $this->createFolder($savePath);
            }
            $image_file = "uploads/profile/user/".session('user_info')['id']."/".$image_name;
            //服务器文件存储路径
            if (file_put_contents($image_file, base64_decode(str_replace($result[1], '', $base64_image)))){
                return $image_file;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    private function createFolder($path = null, $mode = 0777)
    {
        $dirs = explode('/', $path);
        $dir = '';
        foreach ($dirs as $part) {
            $dir .= $part . '/';
            if (!is_dir($dir) && strlen($dir) > 0) {
                mkdir($dir, $mode);
            }
        }
    }
}
