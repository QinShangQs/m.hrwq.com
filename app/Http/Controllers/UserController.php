<?php
/**
 * 用户中心
 */
namespace App\Http\Controllers;

use App\Events\QuestionAnswered;
use App\Events\RegisterPeople;
use App\Models\Carousel;
use App\Models\TalkComment;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Wechat;
use zgldh\QiniuStorage\QiniuStorage;
use App\Models\User;
use App\Models\SmsCode;
use App\Models\IncomeCash;
use App\Models\IncomeScale;
use App\Models\UserBalance;
use App\Models\CouponUser;
use App\Models\CouponRule;
use App\Models\Coupon;
use App\Models\Agency;
use App\Models\Course;
use App\Models\Talk;
use Qiniu\Auth;
use Qiniu\Processing\PersistentFop;
use App\Models\Question;
use App\Models\QuestionListener;
use App\Models\UserFavor;
use App\Models\UserPointVip;
use App\Models\Order;
use Log, DB, Event;
use Carbon\Carbon;
use App\Events\App\Events;

class UserController extends Controller
{
    public function index()
    {
        $tutorCourse = Course::where('is_tutor_course', 1)->where('status', 2)->first();
        //指导师待回答问题数
        $questionsToAnswerCount = $this->_get_questions_to_answer_count();
        //提问者有未读回答问题数
        $newAnswerQuestionsCount = $this->_get_new_answer_questions_count();
        //新优惠券
        $balanceCount = $this->_get_new_user_balance_count();
        $pointCount = $this->_get_new_user_points_count();
        $couponCount = $this->_get_new_coupons_count();
        $myWalletCount = $balanceCount + $pointCount + $couponCount;
        $partnerNewOrderCount = $this->_get_partner_new_order_count();
        $unreadTalkCommentCount = $this->_get_unread_talk_comments_count();

        return view('user.index', [
            'data' => user_info(),
            'ask_question_num' => $this->_get_user_ask_questions()->count(),
            'order_read_num' => $this->_get_order_read_num()->count(),
            'questions_to_answer_count' => $questionsToAnswerCount,
            'new_answer_questions_count' => $newAnswerQuestionsCount,
            'tutorCourse' => $tutorCourse,
        	'balanceCount' => $balanceCount,
            'myWalletCount' => $myWalletCount,
            'partnerNewOrderCount'=>$partnerNewOrderCount,
            'unreadTalkCommentCount' => $unreadTalkCommentCount
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     *
     * 手机绑定页面
     */
    public function login(Request $request)
    {
        if ($request->session()->get('user_info.mobile')) {
            return redirect('/');
        }

        $loginBanner = Carousel::where('use_type', 3)->first();
        $bannerUrl = is_null($loginBanner)?url('/images/login/login_banner.jpg'):admin_url($loginBanner->image_url);
        $data['url'] = $request->input('url');
        $data['invite_user'] = $request->input('invite_user');
        $data['bannerUrl'] = $bannerUrl;
        $data['userInfo'] = user_info(); 

        return view('user.login', $data);
    }

    /**
     * 短信发送验证码接口
     */
    public function get_code(Request $request)
    {
        $mobile = $request->input('login_phone');

        $userModel = User::whereMobile($mobile)->whereNotNull('openid')->first();
        if ($userModel) {
            return response()->json(['status' => false, 'msg' => '该手机号码已注册']);
        }

        //发送短信，60s内只能发送一条
        $until = date('Y-m-d H:i:s', time() - 60);
        $code_count = SmsCode::where('tel', $mobile)->where('created_at', '>=', $until)->count();

        //一天最多发送15条 1小时最多发送5条
        $day = date('Y-m-d');
        $three_hours_ago = date("Y-m-d H:i:s", time() - 1 * 3600);
        //获取一天、一小时内获取的数量
        $sms_count = DB::table('sms_code')->select([
            DB::raw('count(1) as total'),
            DB::raw("count(IF(created_at >'{$three_hours_ago}',1,null)) as cnt")
        ])->where('tel', $mobile)
            ->where('created_at', '>=', $day)
            ->first();
        if (!$code_count) {
            if ($sms_count->cnt >= 5) {
                return response()->json(['status' => false, 'msg' => '一小时最多只能发送5条验证码']);
            } elseif ($sms_count->total >= 15) {
                return response()->json(['status' => false, 'msg' => '一天最多只能发送15条验证码']);
            }

            //注册验证码
            $mobile_data = array();
            $code = mt_rand(100000, 999999);
            $SmsCode = new SmsCode();
            $SmsCode->code = $code;
            $SmsCode->tel = $mobile;
            $mobile_data[] = $mobile;
            $mobile_msg = "尊敬的用户，您的手机验证码是：" . $code . '，请勿向任何单位或个人泄漏。';
            require_once(app_path('Library/SmsClient.php'));
            $client = new \SmsClient(config('sms.gwUrl'), config('sms.serialNumber'), config('sms.password'), config('sms.sessionKey'));

            $res = $client->sendSMS($mobile_data, $mobile_msg);

            if ($res == '0') {
                $SmsCode->save();
                return response()->json(['status' => true, 'msg' => '发送成功']);
            } else {
                return response()->json(['status' => false, 'msg' => '发送失败,错误码:' . $res]);
            }
        } else {
            return response()->json(['status' => true, 'msg' => '验证码还未过期']);
        }
    }

    /**
     * 我的钱包
     */
    public function wallet()
    {
        $balanceCount = $this->_get_new_user_balance_count();
        $pointCount = $this->_get_new_user_points_count();
        $couponCount = $this->_get_new_coupons_count();
        return view('user.wallet', [
            'balanceCount' => $balanceCount,
            'pointCount' => $pointCount,
            'couponCount' => $couponCount,
        ]);
    }
    
    /**
     * 设置
     */
    public function setting(){
    	$tutorCourse = Course::where('is_tutor_course', 1)->where('status', 2)->first();
    	return view('user.setting', [
            'data' => user_info(),
    		'tutorCourse' => $tutorCourse
    		]
    	);
    }

    /**
     * 我的收益-余额
     */
    public function balance()
    {
        UserBalance::where('user_id',session('user_info')['id'])->where('read_flg','1')->update(['read_flg' => 2]);

        $data = User::with(
            ['cash_record' => function ($query) {
                $query->where('apply_status', 1)->first();
            },
                'balance_record' => function ($query) {
                    $query->orderBy('id', 'desc');
                }
            ])->find(session('user_info')['id']);

        return view('user.balance', ['data' => $data]);
    }

    /**
     * 我的积分
     */
    public function score()
    {
        UserPoint::where('user_id',session('user_info')['id'])->where('read_flg','1')->update(['read_flg' => 2]);

        $data = User::with([
            'user_point' => function ($query) {
                $query->orderBy('id', 'desc');
            }
        ])->find(session('user_info')['id']);

        return view('user.score', ['data' => $data]);
    }

    /**
     * 我的优惠券
     */
    public function coupon()
    {
        CouponUser::where('user_id',session('user_info')['id'])->where('read_flg','1')->update(['read_flg' => 2]);

        $coupon = CouponUser::with('c_coupon')->where('user_id', session('user_info')['id'])->get();

        $data[1] = $data[2] = $data[3] = [];
        foreach ($coupon as $k => $item) {
            //类型   1已使用 2未使用 3已过期
            $this->_set_coupon_state($item);
            $data[$item->is_used][] = $item;
        }

        return view('user.coupon', ['data' => $data]);
    }

    /**
     * 收听记录,关注指导师
     */
    public function listen_teacher()
    {
        $data = QuestionListener::with('question', 'question.answer_user')
            ->where('user_id', user_info('id'))
            ->orderBy('id', 'desc')
            ->get();

        foreach ($data as $item) {
            set_audio_state($item->question);
        }

        $teachers = UserFavor::with([
            'favor_teacher'
        ])
            ->whereHas('favor_teacher', function ($query) {
                $query->where('tutor_price', '>', '0');
            })
            ->where('favor_type', 3)
            ->where('user_id', user_info('id'))
            ->orderBy('id', 'desc')
            ->get();

        return view('user.listen_teacher', ['questions' => $data, 'teachers' => $teachers]);
    }

    /**
     * 我的提问 语音问题   回答 or 待回答
     */
    public function question()
    {
        Question::where('user_id',session('user_info')['id'])
            ->where('new_answer_flg', 1)->update(['new_answer_flg' => 2]);

        $question = $this->_get_user_ask_questions();

        $answer_data = $no_answer_data = [];
        if ($question) {
            foreach ($question as $item) {
                if ($item->answer_flg == 1) {
                    $no_answer_data[] = $item;
                } else {
                    $item->audio_type = 4;
                    $item->audio_msg = '点击收听';
                    $answer_data[] = $item;
                }
            }
        }
        
        $data = QuestionListener::with('question', 'question.answer_user')
            ->where('user_id', user_info('id'))
            ->orderBy('id', 'desc')
            ->get();
        if(!empty($data->toArray())){
            foreach ($data as $item) {
                if(!empty($item)){
                    set_audio_state($item->question);
                }
            }
        }


        $teachers = UserFavor::with([
            'favor_teacher'
        ])
            ->whereHas('favor_teacher', function ($query) {
                $query->where('tutor_price', '>', '0');
            })
            ->where('favor_type', 3)
            ->where('user_id', user_info('id'))
            ->orderBy('id', 'desc')
            ->get();
            
        return view('user.question', ['answer_data' => $answer_data, 'no_answer_data' => $no_answer_data,'questions' => $data, 'teachers' => $teachers]);
    }

    /**
     * 我的帖子
     */
    public function talk()
    {
        TalkComment::where('read_flg', 1)->whereHas('talk', function($query) {
            $query->where('user_id', user_info('id'));
        })->update(['read_flg' => 2]);

        $uid = user_info('id');
        //我发布的
        $talk = Talk::with('ask_user', 'ask_user.c_city')
            ->where('user_id', $uid)
            ->orderBy('id', 'desc')
            ->get();

        //我参与的 --- 评论
        $comment_talk = Talk::select('talk.*')->with(['ask_user', 'ask_user.c_city', 'comments' => function ($query) use ($uid) {
            $query->where('r_user_id', $uid);
        }])
            ->leftjoin('talk_comment', 'talk_comment.talk_id', '=', 'talk.id')
            ->where('talk_comment.r_user_id', $uid)
            ->orderBy('talk_comment.id', 'desc')
            ->groupBy('talk.id')
            ->get();

        return view('user.talk', ['talk' => $talk, 'comment_talk' => $comment_talk]);
    }

    /**
     * 指导师-我的名片
     */
    public function my_card()
    {


    }

    /**
     * 指导师-账户信息
     */
    public function my_info()
    {

    }

    /**
     * 提现
     */
    public function cash()
    {
        return view('user.cash');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * 提现-保存    1.申请记录 用于后台处理  2.余额扣减  3.余额记录
     * 1.余额判断  2.当前是否有待处理提现
     */
    public function cash_store(Request $request)
    {
        $this->validate($request, [
            'cash_amount' => 'required|integer|max:200|min:1',
        ], [], [
            'cash_amount' => '提现金额',
        ]);

        $uid = session('user_info')['id'];
        if (IncomeCash::where('user_id', $uid)->where('apply_status', 1)->first()) {
            return response()->json(['code' => 2, 'message' => '有未处理的提现！']);
        }

        $cash_amount = $request->input('cash_amount');
        $user = User::find(user_info('id'));

        if ($cash_amount > $user->current_balance) {
            return response()->json(['code' => 3, 'message' => '当前余额不足！']);
        }

        $incomeCash = new IncomeCash();
        $incomeCash->user_id = $uid;
        $incomeCash->cash_amount = $cash_amount;
        $incomeCash->apply_status = 1;

        $user_balance = [];
        $user_balance['user_id'] = $uid;
        $user_balance['amount'] = $cash_amount;
        $user_balance['operate_type'] = 2;
        $user_balance['source'] = 6;

        DB::beginTransaction();
        try {
            //申请记录
            $incomeCash->save();
            //余额扣减
            $user->decrement('current_balance', $cash_amount);
            //余额记录
            UserBalance::create($user_balance);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['code' => 1, 'message' => '申请失败！']);
        }
        return response()->json(['code' => 0, 'message' => '申请成功,预计7个工作日内到账！']);
    }


    /**
     * 绑定操作
     */
    public function do_login(Request $request)
    {
        if ($request->isMethod('post')) {
            //判断验证码是否正确
            $msg_result = $this->verify_code($request->input('login_phone'), $request->input('login_code'));
            $msg_result = json_decode($msg_result);
            if ($msg_result->status != 3) {
                return response()->json(['status' => false, 'msg' => $msg_result->error]);
            } else {
                $userModel = [];
                $openId = session('wechat_user.openid');
                if ($openId) {
                    $userModel = User::whereOpenid(session('wechat_user.openid'))->first();
                }
                if ($userModel) {
                    $userModel->mobile = $request->input('login_phone');
                    $userModel->province = $request->input('province');
                    $userModel->city = $request->input('city');
                    $userModel->register_at = date('Y-m-d H:i:s');
                } else {
                    return response()->json(['status' => false, 'msg' => '绑定失败']);
                }
                DB::beginTransaction();
                try {
                    //推荐人
                    $invite_user = $request->input('invite_user');
                    if (!empty($invite_user)) {
                        $userModel->invite_id = $invite_user;
                    } else {
                     //自我注册
                     $couponRule = CouponRule::where('rule_id', '2')->first();

                     //查看用户是否领过自我注册的优惠券
                     $couponUsers = CouponUser::where('come_from',2)->where('user_id',$userModel->id)->get();
                    if(!empty($couponUsers->toArray())){
                        $coupon_ids = explode(',', $couponRule->coupon_id);
                            foreach ($coupon_ids as $coupon_id) {
                                $this->_send_coupon($coupon_id, $userModel->id, '2');
                            }
                        }
                    }

                    $userModel->save();
                    $user_info['openid'] = $userModel->openid;
                    $user_info['mobile'] = $userModel->mobile;
                    $user_info['id'] = $userModel->id;
                    $user_info['nickname'] = $userModel->nickname;
                    $request->session()->set('user_info', $user_info);
                    
                    $this->_register_vip_ward();
                    
                    $url = $request->input('url');
                    DB::commit();
                    get_score(1);
                    return response()->json(['status' => true, 'msg' => '绑定成功', 'url' => $url]);
                } catch (\Exception $e) {
                	Log::error('手机号注册',[$e]);
                    DB::rollBack();
                    //return response()->json(['status' => false, 'msg' => '手机绑定失败']);
                    
                    $url = $request->input('url');
                    return response()->json(['status' => true, 'msg' => '已绑定成功', 'url' => $url]);
                }
            }
        }
    }

    /**
     * 注册奖励天数
     */
    private function  _register_vip_ward(){
    	$user = user_info();
    	$days = 7;
    	$left_days = get_new_vip_left_day($user['vip_left_day'], $days);
    	UserPointVip::add($user['id'], $days, 5);//注册奖励
    	User::find($user['id'])->update(['vip_left_day' => $left_days]);
    	
    	if($user['lover_id'] > 0){
    		$lover = User::where("id",'=',$user['lover_id'])->first();
    		//if($lover->role == 1){//普通用户或和会员
	    	$lover_left_days = get_new_vip_left_day($lover->vip_left_day, $days);
	    	$updateResult = User::find($lover->id)->update(['vip_left_day' => $lover_left_days]);
	    	Log::info('register_vip_ward lover_id='.$lover->lover_id." lover_left_days=".$lover_left_days." updateResult=".$updateResult);
	    	if($updateResult){
	    		UserPointVip::add($lover->id, $days, 4);
	    	}
	    	$lover->nickname = $user['nickname'];
	    	Event::fire(new RegisterPeople($lover));
    		//}    		
    	}
    }
    
    private function _send_coupon($coupon_id, $user_id, $come_from)
    {
        if ($coupon_id && $user_id) {
            //计算过期时间
            $coupon = Coupon::find($coupon_id);
            if ($coupon->available_period_type == 1) {
                $expire_at = date('Y-m-d h:i:s', time() + 86400 * $coupon->available_days);
            } else {
                $expire_at = $coupon->available_end_time;
            }
            $couponUser = new CouponUser();
            $couponUser->coupon_id = $coupon_id;
            $couponUser->user_id = $user_id;
            $couponUser->come_from = $come_from;
            $couponUser->expire_at = $expire_at;
            $couponUser->save();
        }
    }

    public function verify_code($mobile, $code)
    {
        if ($mobile == '') {
            return ('{"status":4,"error":"手机号码不能为空"}');
            exit;
        }
        if ($code == '') {
            return ('{"status":5,"error":"验证码不能为空"}');
            exit;
        }
        $SmsCode = new SmsCode();
        $sms = $SmsCode->where('tel', '=', $mobile)->orderBy('id', 'desc')->first();
        if ($sms !== null) {
            $created_at = $sms['created_at'];
            $time = time();
            $created_at = strtotime($created_at);
            //半小时的有效时间
            if ($time - $created_at > 180) {
                return ('{"status":1,"error":"验证码已过期，请重新获取验证码。"}');
                exit;
            } //验证码是否正确
            elseif ($sms['code'] !== $code) {
                return ('{"status":2,"error":"验证码不正确"}');
                exit;
            } else {
                return ('{"status":3,"error":"验证码正确"}');
                exit;
            }
        } else {
            return ('{"status":2,"error":"验证码不正确"}');
            exit;
        }

    }

    /**
     * 指导师-我的回答 回答问题-录音页面
     *
     * @param $id   问题id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function answer_voice($id)
    {
        $this->_auth_teacher();

        $question = Question::findOrFail($id);
        if ($question->tutor_id != user_info('id')) {
            abort(403, '非提问指导师');
        }

        return view('user.answer_voice', ['wx_js' => Wechat::js(), 'question' => $question]);
    }


    /**
     * 提交语音问题答案
     * 步骤流程
     * 1.根据media_id 获取语音资源流数据
     * 2.将流数据写入七牛服务器存储
     * 3.音频转码为mp3（兼容播放)
     * 4.根据问题id，更新 临时未转码音频key,回答时间 到question AR
     * 5.等待七牛转码的回调 见 $this->av_thumb_mp3_notify 方法
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload_voice(Request $request)
    {

        $media_id = $request->input('media_id');
        $voice_long = $request->input('voice_long');
        $question_id = $request->input('question_id');

        if (!$media_id || (int)($voice_long) == 0 || !$question_id) {
            return response()->json(['code' => 1, 'msg' => '参数错误']);
        }

        $question = Question::find($question_id);
        if (!$question) {
            return response()->json(['code' => 2, 'msg' => '不存在的问题']);
        }

        if ($question->answer_origin_key) {
            return response()->json(['code' => 3, 'msg' => '已回答的问题']);
        }

        //step1
        $material = Wechat::material_temporary();
        $contents = $material->getStream($media_id);

        //step2
        $disk = QiniuStorage::disk('qiniu');
        $file_key = uniqid();
        $flag = $disk->put($file_key, $contents);

        if ($flag) {
            //step3
            $auth = new Auth(config('qiniu.AK'), config('qiniu.SK'));
            $pfop = new PersistentFop($auth, config('qiniu.BUCKET_NAME_AUDIO'), config('qiniu.PIPELINE'), route('user.av_thumb_mp3_notify'));
            $fops = "avthumb/mp3";
            list($id, $err) = $pfop->execute($file_key, $fops);

            //回答后收益分成
            $incomeScale = IncomeScale::where('key', '1')->first();
            $incomeScaleArr = @unserialize($incomeScale->value);

            if ($err == null && empty($question->answer_origin_key)) {
                DB::beginTransaction();
                try {
                    //step4
                    $update = [];
                    $update['answer_origin_key'] = $id;
                    $update['answer_date'] = date('Y-m-d H:i:s');
                    $update['voice_long'] = $voice_long;
                    $question->update($update);
                    //指导师分成
                    if (isset($incomeScaleArr['t_scale']) && $incomeScaleArr['t_scale'] > 0) {
                        $item_t = User::find($question->tutor_id);
                        $amount = $question->price * $incomeScaleArr['t_scale'] / 100;
                        $item_t->increment('current_balance', $amount);  //总收益 & 余额 ++
                        $item_t->increment('balance', $amount);
                        $item_t->increment('question_amount', $amount);
                        $balance = UserBalance::where('source',3)->where('ref_id',$question->id)->get();
                        if(!empty($balance->toArray())){
                            DB::rollBack();
                            return response()->json(['code' => 4, 'msg' => '提交失败,可能重复提交记录!']);
                        }
                        //用户余额记录
                        $user_balance = [];
                        $user_balance['user_id'] = $question->tutor_id;
                        $user_balance['amount'] = $amount;
                        $user_balance['operate_type'] = '1';
                        $user_balance['source'] = '3';
                        $user_balance['remark'] = $question->content;
                        $user_balance['ref_id'] = $question->id;
                        UserBalance::create($user_balance);
                    }
                    //提问者分成
                    if (isset($incomeScaleArr['a_scale']) && $incomeScaleArr['a_scale'] > 0) {
                        $item_a = User::find($question->user_id);
                        $amount = $question->price * $incomeScaleArr['a_scale'] / 100;
                        $item_a->increment('current_balance', $amount);  //总收益 & 余额 ++
                        $item_a->increment('balance', $amount);
                        $item_a->increment('question_amount', $amount);
                        $balance = UserBalance::where('source',3)->where('ref_id',$question->id)->get();
                        if(!empty($balance->toArray())){
                            DB::rollBack();
                            return response()->json(['code' => 4, 'msg' => '提交失败,可能重复提交记录!']);
                        }
                        //用户余额记录
                        $user_balance = [];
                        $user_balance['user_id'] = $question->user_id;
                        $user_balance['amount'] = $amount;
                        $user_balance['operate_type'] = '1';
                        $user_balance['source'] = '3';
                        $user_balance['remark'] = $question->content;
                        $user_balance['ref_id'] = $question->id;
                        UserBalance::create($user_balance);
                    }
                    DB::commit();
                    Event::fire(new QuestionAnswered($question));
                    return response()->json(['code' => 0, 'msg' => '提交成功']);
                } catch (\Exception $e) {
                    DB::rollBack();
                        Log::warning($e);
                    return response()->json(['code' => 4, 'msg' => '提交失败']);
                }
            } else {
                Log::info('audio_submit:put key' . $file_key . ' avthumb fail' . serialize($err));
                return response()->json(['code' => 4, 'msg' => '提交失败']);
            }
        } else {
            Log::info('audio_submit:put key' . $file_key . ' put fail');
            return response()->json(['code' => 4, 'msg' => '提交失败']);
        }

    }

    /**
     * 七牛音频amr-mp3转码回调
     * 步骤流程
     * 1.解析回调接受的数据得到 音频的原始key 和 转码后的key
     * 2.更新数据 转码后的key和音频回答完成标识到question AR
     *
     */
    public function av_thumb_mp3_notify()
    {
        //step1
        $str = file_get_contents('php://input');
//        $str = '{"id":"z0.57bac4a87823de7b57c79e4c","pipeline":"1380819640.duilie","code":0,"desc":"The fop was completed successfully","reqid":"4ygAAERHtrvmFm0U","inputBucket":"loimap","inputKey":"57bab896058ec","items":[{"cmd":"avthumb/mp3","code":0,"desc":"The fop was completed successfully","hash":"FrG-DBgad8KGXST4qc59Pj7YRiTd","key":"f0ZhdfBuStBT1zwU3OUQUMDT9tQ=/FrGcQxnxxHJrt-I2JZ9QFCuIchHY","returnOld":1}]}';
        Log::info('audio_notify:' . $str);

        $arr = json_decode($str, true);
        if ($arr['code'] == 0) {
            $id = $arr['id'];
            $key = $arr['items'][0]['key'];

            //step2
            $question = Question::where('answer_origin_key', $id)->first();
            $update = [];
            $update['answer_url'] = $key;
            $update['answer_flg'] = 2;
            $update['new_answer_flg'] = 1;
            $question->update($update);
        }
    }

    public function upload_voice_status(Request $request)
    {
        $question_id = $request->input('question_id');

        $question = Question::find($question_id);
        if ($question && $question->answer_flg == 2) {
            return response()->json(['code' => 0]);
        }
        return response()->json(['code' => 1]);
    }


    private function _set_coupon_state($item)
    {
        //类型   1已使用 2未使用 3已过期
        if ($item->is_used == 2 && $item->expire_at < Carbon::now()) {
            $item->is_used = 3;
        }

        //使用期限
        if ($item->c_coupon->available_period_type == 1) {
            $item->use_start = $item->created_at;
            $item->use_end = $item->expire_at;
        } else {
            $item->use_start = $item->c_coupon->available_start_time;
            $item->use_end = $item->expire_at;
        }

        //满足条件
        $item->use_condition = '';
        if ($item->c_coupon_type == 1) {
            $item->use_condition = '满' . $item->full_money . '可使用';
        }

        //使用范围
        switch ($item->c_coupon->use_scope) {
            case 1:
                $item->use_scope = config('constants.coupon_use_scope')[$item->c_coupon->use_scope];
                break;
            case 2:
            case 3:
            case 4:
            case 5:
                $item->use_scope = '限' . config('constants.coupon_use_scope')[$item->c_coupon->use_scope];
                break;
            case 6:
                $item->use_scope = '限好课中' . $this->_get_use_agency($item->c_coupon->use_scope_val);
                break;
            case 7:
                $item->use_scope = '限好课中' . $this->_get_use_course($item->c_coupon->use_scope_val);
                break;
            case 8:
                $item->use_scope = '限好看中' . $this->_get_use_agency($item->c_coupon->use_scope_val);
                break;
            case 9:
                $item->use_scope = '限好看中' . $this->_get_use_course($item->c_coupon->use_scope_val);
                break;
        }
    }

    private function _get_use_agency($use_scope_val)
    {
        $tmp_arr = explode(',', $use_scope_val);
        $data = [];
        if ($tmp_arr) {
            $data = Agency::whereIn('id', $tmp_arr)->lists('agency_name')->toArray();
        }
        return $data ? implode(',', $data) : '';
    }

    private function _get_use_course($use_scope_val)
    {
        $tmp_arr = explode(',', $use_scope_val);
        $data = [];
        if ($tmp_arr) {
            $data = Course::whereIn('id', $tmp_arr)->lists('title')->toArray();
        }
        return $data ? implode(',', $data) : '';
    }

    private function _get_user_ask_questions()
    {
        $builder = Question::with('answer_user');

        //已支付筛选 即提问成功的 问题
        $builder->with('order')->whereHas('order', function ($query) {
            $query->where('order_type', 2);
        });

        return $builder->where('user_id', user_info('id'))->orderBy('id', 'desc')->get();
    }

    private function _get_order_read_num()
    {
        return Order::where('user_id', user_info('id'))
            ->whereIn('pay_type', ['1', '2', '3', '6'])
            ->whereIn('order_type', ['1', '2', '4'])
            ->where('read_flg', '1')->get();
    }

    /**
     * @return mixed
     *
     * 新的待回答问题数量（红字提示用）
     */
    private function _get_questions_to_answer_count()
    {
        return Order::where('pay_type', 4)
            ->where('order_type', 2)
            ->whereHas('question', function ($query) {
                $query->where('answer_flg', 1)
                    ->where('tutor_id', session('user_info')['id'])
                    ->where('to_answer_flg', 1);
            })
            ->count();
    }

    /**
     * @return mixed
     *
     * 我的提问中有新回答的问题数量（红点提示用）
     */
    private function _get_new_answer_questions_count()
    {
        return Order::where('pay_type', 4)
            ->where('order_type', 2)
            ->whereHas('question', function ($query) {
                $query->where('answer_flg', 2)
                    ->where('user_id', session('user_info')['id'])
                    ->where('new_answer_flg', 1);
            })
            ->count();
    }

    /**
     * @return mixed
     *
     * 新的未读优惠券数目
     */
    private function _get_new_coupons_count()
    {
        return CouponUser::where('user_id', session('user_info')['id'])
            ->where('read_flg', 1)
            ->count();
    }

    /**
     * @return mixed
     *
     * 我的收益未读数
     */
    private function _get_new_user_balance_count()
    {
        return UserBalance::where('user_id', session('user_info')['id'])
            ->where('read_flg', 1)
            ->count();
    }


    /**
     * @return mixed
     *
     * 我的和贝阅读数
     */
    private function _get_new_user_points_count()
    {
        return UserPoint::where('user_id', session('user_info')['id'])
            ->where('read_flg', 1)
            ->count();
    }

    private function _get_partner_new_order_count()
    {
        $user = User::where('role','3')->where('partner_city','>','0')->find(session('user_info')['id']);
        if($user == null)
            return false;

        $ordersCount = Order::where('partner_flg', 1)->where(function($query) use ($user){
            $query->where(function ($queryOne) use ($user) {
                $queryOne->whereHas('course', function ($q) use ($user) {
                    $q->where('promoter', $user->id)->where('head_flg', 2);
                });
            })
                ->orWhere(function ($queryTwo) use ($user) {
                    $queryTwo->whereHas('course', function ($q) {
                        $q->where('head_flg', 1)
                            ->where('distribution_flg', 1);
                    })
                        ->whereHas('order_course', function ($q) use ($user) {
                            $q->where('user_city', $user->partner_city);
                        });
                });
        })->count();
        return $ordersCount;
    }

    private function _get_unread_talk_comments_count()
    {
        $talkCommentsCount = TalkComment::where('read_flg', 1)->whereHas('talk', function($query) {
            $query->where('user_id', user_info('id'));
        })->count();
        return $talkCommentsCount;
    }

    private function _auth_teacher()
    {
        if (user_info('role') != 2) {
            abort(403, '非指导师角色！');
        }
    }


}
