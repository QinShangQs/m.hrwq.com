<?php

namespace App\Http\Controllers;

use App\Library\UploadFile;
use App\Models\Course;
use App\Models\Question;
use App\Models\User;
use App\Models\UserTutorApply;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TutorController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 指导师引导页
     */
    public function apply()
    {
        $user = User::find(session('user_info')['id']);
        if($user->role == 2) {
            abort(403, '已经是指导师，无需再次申请！');
        } else if($user->role == 3) {
            abort(403, '已经是合伙人，无法申请成为指导师！');
        }
        $tutorCourse = Course::where('is_tutor_course', 1)->where('status', 2)->first();
        return view('tutor.apply', ['tutorCourse' => $tutorCourse]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 指导师完善资料
     */
    public function complete()
    {
        $user = User::with('c_province', 'c_city', 'c_district')->find(session('user_info')['id']);
        if($user->role != 2)
            abort(403, '不是指导师，无法完善指导师资料！');

        $userTutorApply = UserTutorApply::where('user_id', $user->id)->where('progress', 1)->orderBy('id', 'desc')->first();
        if ($userTutorApply != null)
            return view('tutor.progress', ['user'=>$user, 'userTutorApply'=>$userTutorApply]);
        return view('tutor.complete', ['user' => $user]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * 保存指导师资料
     */
    public function tutorSave()
    {
        $user = User::with('c_province', 'c_city', 'c_district')->find(session('user_info')['id']);
        if($user->role != 2)
            return response()->json(['code' => 2, 'message' => '不是指导师，无法完善指导师资料！']);

        $this->validate(request(), [
            'realname'=>'required',
            'sex'=>'required',
            'email'=>'required',
            'address'=>'required',
            'tutor_honor'=>'required',
            'tutor_cover'=>'required',
            //'tutor_price'=>'required|numeric|min:1|max:100',
            'tutor_introduction'=>'required|min:20|max:150',
        ], [], [
            'realname'=>'真实姓名',
            'sex'=>'性别',
            'email'=>'邮箱',
            'address'=>'通讯地址',
            'tutor_honor'=>'头衔',
            'tutor_cover'=>'封面图片',
            'tutor_price'=>'价格',
            'tutor_introduction'=>'个人介绍',
        ]);
        //if(request('tutor_price') < 1)
            //return response()->json(['code' => 3, 'message' => '指导师价格应不少于 1 元']);

        $userTutorApply = new UserTutorApply();
        $userTutorApply->user_id = $user->id;
        $userTutorApply->realname = $user->realname = request('realname');
        $userTutorApply->sex = $user->sex = request('sex');
        $userTutorApply->email = $user->email = request('email');
        $userTutorApply->address = $user->address = request('address');
        $userTutorApply->honor = $user->tutor_honor = request('tutor_honor');
        $userTutorApply->cover = $user->tutor_cover = request('tutor_cover');
        $userTutorApply->price = $user->tutor_price = request('tutor_price');
        $userTutorApply->introduction = $user->tutor_introduction = request('tutor_introduction');
        $userTutorApply->progress = 1;
        if(!$user->isDirty()) {
            return response()->json(['code' => 1, 'message' => '指导师资料未作更改！']);
        }

        if ($userTutorApply->save())
            return response()->json(['code' => 0, 'message' => '指导师资料提交成功！']);
        return response()->json(['code' => 4, 'message' => '指导师资料提交失败！']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 指导师——我的回答
     */
    public function answers()
    {
        $user = User::with('c_province', 'c_city', 'c_district')->find(session('user_info')['id']);
        if($user->role != 2)
            abort(403, '非指导师角色！');

        //将待回答问题 flag 置2
        Question::where('tutor_id', session('user_info')['id'])->where('to_answer_flg', 1)->update(['to_answer_flg'=>2]);
        $builder = Question::with('ask_user','answer_user');
        //已支付筛选 即提问成功的 问题
        $builder->with('order')->whereHas('order', function ($query) {
            $query->where('order_type', 2);
        });

        $question = $builder->where('tutor_id', $user->id)->orderBy('id','desc')->get();

        $answer_data = $no_answer_data = [];
        if($question){
            foreach($question as $item)
            {
                if($item->answer_flg == 1)
                {
                    $no_answer_data[] = $item;
                } else {
                    $item->audio_type = 4;
                    $item->audio_msg  = '点击收听';
                    $answer_data[] = $item;
                }
            }
        }
        return view('tutor.answers',['answer_data'=>$answer_data,'no_answer_data'=>$no_answer_data, 'user'=>$user]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * 处理封面图上传
     */
    public function upload()
    {
        $upload = new UploadFile();
        $upload->savePath = 'uploads/tutor/';// 设置附件上传目录
        //$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg', 'bmp', 'doc', 'docx','xls', 'xlsx', 'zip', 'rar', '7z', );
        $upload->autoSub = true;
        $upload->subType = 'date';
        $upload->dateFormat = 'Y-m';
        $upload->thumb = true;//是否开启图片文件缩略图
        $upload->thumbPrefix = 'thumb_';
        $upload->thumbMaxWidth = '300';
        $upload->thumbMaxHeight = '300';

        $message = array();
        if (!$upload->upload()) {
            $message['code'] = 1;
            $message['message'] = $upload->getErrorMsg();
        } else {
            $info_arr = $upload->getUploadFileInfo();
            $file_arr = [];
            foreach ($info_arr as $info) {
                $uri = $info['savepath'] . $info['savename'];
                $file_arr[] = $uri;
            }
            $message['code'] = 0;
            $message['data'] = implode(',', $file_arr);
        }
        return response()->json($message);
    }
}
