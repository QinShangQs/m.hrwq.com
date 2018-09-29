<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Events\RegisterPeople;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App,
    Wechat,
    DB,
    Log,
    Event;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order as WechatOrder;
use App\Models\Order;
use App\Models\User;
use App\Models\Income;
use App\Models\Question;
use App\Models\WechatPayLog;
use App\Models\IncomeScale;
use App\Models\UserBalance;
use App\Models\UserPointVip;

class WechatController extends Controller {

    public function auth(Request $request) {
        if (empty($request->session()->get('wechat_user'))) {
            $wechat = new Application(config('wechat'));
            if (request()->has('state') && request()->has('code')) {
                $wechatUser = $wechat->oauth->user();
                $wechat_user = $wechatUser->getOriginal();
                $request->session()->set('wechat_user', $wechat_user);
                return redirect($request->input('url'));
            }

            $scopes = config('wechat.oauth.scopes', ['snsapi_userinfo']);

            if (is_string($scopes)) {
                $scopes = array_map('trim', explode(',', $scopes));
            }

            return $wechat->oauth->scopes($scopes)->redirect(request()->fullUrl());
        }
    }

    /**
     * 语音详情
     */
    public function question(Request $request) {
        $id = $request->input('id');

        $data = Question::with('ask_user', 'answer_user', 'order')->whereHas('order', function ($query) {
                    $query->where('order.pay_type', '=', 4);
                    $query->where('order.order_type', '=', 2);
                })->find($id);
        if ($data == null)
            abort(403, '问题查询失败！');
        set_audio_state($data);

        //获取推荐问题   1.按标签匹配    2.所有  规则：收听次数最多
        $hot_question = [];
        $tags = $data->tags->lists('id')->toArray();
        if ($tags) {
            $builder = Question::with('answer_user')->where('answer_flg', 2)->where('id', '<>', $id);
            $builder->with('tags')->whereHas('tags', function ($query) use ($tags) {
                $query->whereIn('question_tag.tag_id', $tags);
            });
            $hot_question = $builder->orderBy('listener_nums', 'desc')->first();
        }

        if (!$hot_question) {
            $hot_question = Question::with('answer_user')->where('id', '<>', $id)->where('answer_flg', 2)->orderBy('listener_nums', 'desc')->first();
        }

        return view('question.question_show', ['question' => $data, 'hot_question' => $hot_question, 'wx_js' => Wechat::js()]);
    }

    /**
     * 旁听支付创建
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function question_listen_pay(Request $request) {
        $order = $this->_create_question_listen_order($request);
        if ($order) {
            $view_data = $this->_create_wx_order($order);
            if ($view_data) {
                return response()->json(['code' => 0, 'message' => '订单创建成功!', 'data' => $view_data]);
            } else {
                return response()->json(['code' => 1, 'message' => '微信订单创建失败!']);
            }
        } else {
            return response()->json(['code' => 2, 'message' => '商家订单不存在!']);
        }
    }

    /** 订单支付-确认页 */
    public function question_ask_confirm(Request $request) {
        $data = Question::findOrFail($request->input('id'));
        return view('question.ask_question_confirm', ['data' => $data]);
    }

    /** 订单支付 */
    public function question_ask_pay(Request $request) {
        $order = $this->_create_question_ask_order($request);
        if ($order) {
            $view_data = $this->_create_wx_order($order);
            if ($view_data) {
                return response()->json(['code' => 0, 'message' => '订单创建成功!', 'data' => $view_data]);
            } else {
                return response()->json(['code' => 1, 'message' => '微信订单创建失败!']);
            }
        } else {
            return response()->json(['code' => 2, 'message' => '商家订单不存在!']);
        }
    }

    /** 和会员支付页面 */
    public function vip_pay() {
        $order = Order::where('user_id', user_info('id'))
                ->where('pay_type', 6)
                ->where('order_type', '1')
                ->first();

        if ($order) {
            $view_data = $this->_create_wx_order($order);
            if ($view_data) {
                return view('vip.pay', $view_data);
            } else {
                abort(403, '支付失败！');
            }
        } else {
            abort(403, '订单不存在！');
        }
    }

    /** 好课订单支付 */
    public function course_pay(Request $request) {
        $order = Order::where('id', $request->input('id'))
                ->where('user_id', user_info('id'))
                ->where('pay_type', '1')
                ->where('pay_method', '1')
                ->where('order_type', '1')
                ->first();

        if ($order) {
            $view_data = $this->_create_wx_order($order);
            if ($view_data) {
                return view('course.pay_way', $view_data);
            } else {
                abort(403, '支付失败！');
            }
        } else {
            abort(403, '订单不存在！');
        }
    }

    /** 订单支付 */
    public function vcourse_pay(Request $request) {
        $order = Order::where('id', $request->input('id'))
                ->where('user_id', user_info('id'))
                ->where('pay_type', '2')
                ->where('pay_method', '1')
                ->where('order_type', '1')
                ->first();

        if ($order) {
            $view_data = $this->_create_wx_order($order);
            if ($view_data) {
                return view('vcourse.order_pay', $view_data);
            } else {
                abort(403, '支付失败！');
            }
        } else {
            abort(403, '订单不存在！');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 壹加壹支付
     */
    public function opoPay(Request $request) {
        $order = Order::where('id', $request->input('id'))
                ->where('user_id', user_info('id'))
                ->where('pay_type', '3')
                ->where('order_type', '1')
                ->first();
        if ($order) {
            $payData = $this->_create_wx_order($order);
            if ($payData) {
                return view('opo.pay', $payData);
            } else {
                abort(403, '支付失败！');
            }
        } else {
            abort(403, '订单不存在！');
        }
    }

    /**
     * 获取回调支付状态
     */
    public function status() {
        $order = Order::find(request('order_id'));
        if ($order == null)
            return response()->json(['code' => 1, 'message' => '订单信息查询失败']);
        return response()->json(['code' => 0, 'data' => $order->order_type, 'user' => user_info()]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function notify() {
        $payment = Wechat::payment();
        $response = $payment->handleNotify(function ($notify, $successful) {
            if ($successful) {
                // 你的逻辑
                $outTradeNo = $notify->out_trade_no;
                Log::info('pay_notify_wechat', [$outTradeNo]);
                $wechatPayLog = WechatPayLog::where('out_trade_no', $outTradeNo)->first();
                $order = Order::find($wechatPayLog->order_id);
                if ($order->order_type == '1') {
                    DB::beginTransaction();
                    try {
                        $this->_wx_pay_log($wechatPayLog, $notify);

                        $this->_order_update($order);
                        //消费加积分
                        $this->_plus_score('13', $order->price, $order->user_id);
                        //平台收益
                        $this->_log_income($order);

                        //和会员类型 变更 vip_flg
                        if ($order->pay_type == 6) {
                            $this->_user_update($order->user_id, ['vip_flg' => 2]);
                            //计算奖励和收益分成
                            $this->_vip_listen($order->user_id, $order);
                        }

                        //好问旁听 分成
                        if ($order->pay_type == 5) {
                            $this->_user_listen($order->pay_id);
                        }

                        DB::commit();
                    } catch (\Exception $e) {
                        Log::warning($e);
                        DB::rollBack();
                        return false;
                    }
                    //通知
                    try {
                        Event::fire(new OrderPaid($order));
                    } catch (\Exception $ex) {
                        
                    }

                    return true;
                }
            }
            return false;
        });

        return $response;
    }

    public function menu() {
        $wechat = new Application(config('wechat'));
        $menus = $wechat->menu->all();
        echo($menus);
    }

    public function material() {
        $wechat = new Application(config('wechat'));
        $material = $wechat->material;
        $lists = $material->lists('news', 0, 100);
        dd($lists);
    }

    /**
     * 产生支付，记录平台收益
     *
     * @param object $order 订单ar
     */
    private function _log_income($order) {
        if(_in_paywhitelist($order->user_id)){
            Log::alert("支付测试 拒绝平台收益 _log_income" .$order->user_id);
            return;
        }
        
        $income = [];
        $income['user_id'] = $order->user_id;
        $income['log_type'] = 1;
        $income['order_id'] = $order->id;
        $income['order_no'] = $order->order_code;
        $income['amount'] = $order->price;
        $income['remark'] = '订单在线支付';
        $income['pay_mod'] = 1;
        //订单类型 对应的 收益类型 值
        $order_income_type_map = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 4, 6 => 5];
        $income['income_type'] = $order_income_type_map[$order->pay_type];
        //计算当前的平台收益
        $income['total_amount'] = get_platform_current_amount() + $order->price;
        Income::create($income);
    }

    /**
     * 产生支付，记录平台收益
     * @param unknown $user_id $order->user_id
     * @param unknown $log_type 1 收益，2支出
     * @param unknown $order_id $order->id
     * @param unknown $order_code $order->order_code
     * @param unknown $order_price $order->price
     * @param unknown $pay_type
     */
    private function _log_income_log_type($user_id, $log_type, $order_id, $order_code, $order_price, $pay_type, $remark) {
        if(_in_paywhitelist($user_id)){
            Log::alert("支付测试 拒绝平台收益 _log_income_log_type" .$user_id);
            return;
        }
        
        $income = [];
        $income['user_id'] = $user_id;
        $income['log_type'] = $log_type;
        $income['order_id'] = $order_id;
        $income['order_no'] = $order_code;
        $income['amount'] = $order_price;
        $income['remark'] = $remark;
        $income['pay_mod'] = 1;
        //订单类型 对应的 收益类型 值
        $order_income_type_map = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 4, 6 => 5];
        $income['income_type'] = $order_income_type_map[$pay_type];
        //计算当前的平台收益
        if ($log_type == 1) {
            $income['total_amount'] = get_platform_current_amount() + $order_price;
        } else {
            $income['total_amount'] = get_platform_current_amount() - $order_price;
        }

        Income::create($income);
    }

    private function _plus_score($type, $money = 0, $user_id) {
        $type = intval($type);
        $money = intval($money);
        //来源:1注册 2分享 4发帖 5评论 6作业 7笔记 8推荐好友注册  12观看视频 13消费 
        $score_list = array('1' => '10', '2' => '10', '4' => '5', '5' => '5', '6' => '5', '7' => '5', '8' => '10', '12' => '5', '13' => '1', '14' => '10');

        $date_start = date('Y-m-d 00:00:00');
        $date_end = date('Y-m-d 23:59:59');
        $score_taday = App\Models\UserPoint::select('point_value')
                ->where('created_at', '>=', $date_start)
                ->where('created_at', '<=', $date_end)
                ->where('move_way', 1)
                ->where('user_id', $user_id)
                ->where('source', '<>', 10)//10为取消订单等返还积分  不计在内
                ->get();

        // 计算当天已经获得的总的积分
        $score_total = 0;
        foreach ($score_taday as &$value) {
            $score_total += $value->point_value;
        }

        // 每天的积分上限是200
        if ($score_total >= 200) {
            return true;
        } else {
            $score = $score_list[$type];
            if ($type == 13) {
                $score = intval($money / 10);
            }
            if ($score == '0') {
                return false;
            }
            $userpoint = new App\Models\UserPoint;
            $userpoint->user_id = $user_id;
            $userpoint->point_value = $score;
            $userpoint->source = $type;
            $userpoint->move_way = 1;

            $user = App\Models\User::find($user_id);
            $user->score += $score;
            $user->grow += $score;

            if ($userpoint->save() && $user->save()) {
                //发送微信提醒
                $scoreSources = config('constants.income_point_source');
                if (isset($scoreSources[$type])) {
                    try {
                        $notice = \Wechat::notice();
                        $notice->send([
                            'touser' => $user->openid,
                            'template_id' => 'oxk8-ixLvD_XqQ8enFSy1wJ6qrwziLdeHv7KJqybfwE',
                            'url' => route('user.score'),
                            'topcolor' => '#f7f7f7',
                            'data' => [
                                'first' => '恭喜你获得和贝奖励',
                                'keyword1' => '+' . $score,
                                'keyword2' => $scoreSources[$type],
                                'keyword3' => (string) \Carbon\Carbon::now(),
                                'keyword4' => $user->score,
                                'remark' => '和贝可抵扣听课费，点击查看积分详情'
                            ],
                        ]);
                    } catch (\Exception $ex) {
                        Log::alert($ex->getMessage());
                    }
                }
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 创建语音提问订单
     *
     * @param $request
     * @return null|static
     */
    private function _create_question_ask_order($request) {
        $order = null;
        $question_id = $request->input('qid');
        $user_id = user_info('id');

        //是否已生成订单
        $order = Order::where('user_id', $user_id)->where('pay_type', 5)->where('pay_id', $question_id)->where('order_type', 1)->first();
        if (!$order) {
            $question = Question::find($question_id);
            $order_data = [];
            $order_data['order_code'] = get_order_code(5);
            $order_data['user_id'] = user_info('id');
            $order_data['pay_id'] = $question_id; //商品id
            $order_data['pay_type'] = 4; //1好课 2好看 3壹家壹 4好问提问 5好问偷听 6和会员
            $order_data['order_type'] = 1;
            $order_data['order_name'] = $question->content;
            $order_data['free_flg'] = 2;
            $order_data['price'] = $question->price;

            $order = Order::create($order_data);
        }
        return $order;
    }

    /**
     * 创建语音收听订单    1判断是否存在
     *
     * @param $request
     * @return null|static
     */
    private function _create_question_listen_order($request) {
        $order = null;
        $question_id = $request->input('qid');
        $user_id = user_info('id');

        //是否已生成订单
        $order = Order::where('user_id', $user_id)->where('pay_type', 5)->where('pay_id', $question_id)->where('order_type', 1)->first();
        if (!$order) {
            $question = Question::find($question_id);
            $order_data = [];
            $order_data['order_code'] = get_order_code(5);
            $order_data['user_id'] = user_info('id');
            $order_data['pay_id'] = $question_id; //商品id
            $order_data['pay_type'] = 5; //1好课 2好看 3壹家壹 4好问提问 5好问偷听 6和会员
            $order_data['order_type'] = 1;
            $order_data['order_name'] = $question->content;
            $order_data['free_flg'] = 2;
            $order_data['price'] = 1;

            $order = Order::create($order_data);
        }
        return $order;
    }

    /**
     * 创建微信订单,生成prepay id
     *
     * @param  object $order 商家订单-对象类型
     * @return array
     */
    private function _create_wx_order($order) {
        $view_data = [];
        if ($order) {
            $payment = Wechat::payment();
            
            $total_fee = $order->price * 100;
            //测试人员
            if(_in_paywhitelist(session('user_info')['openid'])){
                $total_fee = intval($order->price);
            }
            $attributes = [
                'trade_type' => 'JSAPI',
                'openid' => session('user_info')['openid'],
                'body' => '和润万青订单',
                'detail' => $order->order_name,
                'out_trade_no' => $order->order_code,
                'total_fee' => $total_fee,
                'notify_url' => route('wechat.notify'),
            ];

            //调用统一下单
            $wx_order = new WechatOrder($attributes);
            $result = $payment->prepare($wx_order);
            if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
                $wx_log = WechatPayLog::where('order_id', $order->id)->first();
                if (!$wx_log) {
                    WechatPayLog::create([
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'out_trade_no' => $attributes['out_trade_no'],
                    ]);
                }

                $config = $payment->configForJSSDKPayment($result->prepay_id);
                $wechatJs = Wechat::js();

                $view_data = compact('order', 'wechatJs', 'config');
            } else
                Log::debug($result);
        }
        return $view_data;
    }

    /**
     *  微信回调日志
     * @param $wechatPayLog
     * @param $notify
     */
    private function _wx_pay_log($wechatPayLog, $notify) {
        $wechatPayLog->appid = $notify->appid;
        $wechatPayLog->bank_type = $notify->bank_type;
        $wechatPayLog->cash_fee = $notify->cash_fee;
        $wechatPayLog->fee_type = $notify->fee_type;
        $wechatPayLog->is_subscribe = $notify->is_subscribe;
        $wechatPayLog->mch_id = $notify->mch_id;
        $wechatPayLog->nonce_str = $notify->nonce_str;
        $wechatPayLog->openid = $notify->openid;
        $wechatPayLog->out_trade_no = $notify->out_trade_no;
        $wechatPayLog->result_code = $notify->result_code;
        $wechatPayLog->return_code = $notify->return_code;
        $wechatPayLog->time_end = $notify->time_end;
        $wechatPayLog->total_fee = $notify->total_fee;
        $wechatPayLog->trade_type = $notify->trade_type;
        $wechatPayLog->transaction_id = $notify->transaction_id;
        $wechatPayLog->save();
    }

    //http://m.qs.tunnel.qydev.com/wechat/vip_listen?user_id=5
    public function vip_listen(Request $request) {
        $user_id = $request->input('user_id');
        $order = Order::where(['user_id' => $user_id, 'pay_type' => 6])->first();
        $this->_order_update($order);
        $this->_log_income($order);
        $result = $this->_vip_listen($request->input('user_id'), $order);

        dd($result);
    }

    /**
     * 和会员会员天数计算、爱心大使分享奖励
     * @param unknown $user_id 用户ID
     * @param unknown $order 订单
     * @return 用户的爱心大使ID
     */
    private function _vip_listen($user_id, $order) {
        //增加和会员天数
        $cUser = User::where("id", '=', $user_id)->first();
        $days = 365;
        $left_days = get_new_vip_left_day($cUser->vip_left_day, $days);
        UserPointVip::add($cUser->id, $days, 1);
        $this->_user_update($cUser->id, ['vip_left_day' => $left_days]);

        //关联爱心大使
        $this->_order_update_lover($order, $cUser->lover_id);

        //爱心大使分享来的用户
        if ($cUser->lover_id != 0) {
            //$diff_day = diff_tow_days($cUser->lover_time, date('Y-m-d H:i:s'));
            $diff_day = 1;
            if ($diff_day <= 7) {
                //被分享奖励
                $days = 7;
                $left_days = get_new_vip_left_day($left_days, $days);
                UserPointVip::add($cUser->id, $days, 3);
                $this->_user_update($cUser->id, ['vip_left_day' => $left_days]);

                //是否发送通知
                $sendNotice = false;
                //分享者奖励
                $lover = User::where("id", '=', $cUser->lover_id)->first();
                //dd($lover->role);
                if ($lover->role == 1) {//普通用户或和和会员
                    $days = 7;
                    $lover_left_days = get_new_vip_left_day($lover->vip_left_day, $days);
                    UserPointVip::add($lover->id, $days, 4);
                    $this->_user_update($lover->id, ['vip_left_day' => $lover_left_days]);
                } else if ($lover->role == 2 || $lover->role == 3) {//导师
                    //爱心大使和会员支付后收益分成    	
                    $incomeScaleArr = null;
                    $amount = 0;
                    if ($lover->role == 2) {//指导师
                        $incomeScale = IncomeScale::where('key', '3')->first();
                        $incomeScaleArr = unserialize($incomeScale->value);
                        $amount = $order->price * $incomeScaleArr['t_scale'] / 100;
                        $sendNotice = true;
                    } else if ($lover->role == 3) {//合伙人，以提问人比例替代
                        if ($cUser->city == $lover->city) {//同城					
                            $incomeScale = IncomeScale::where('key', '4')->first();
                            $incomeScaleArr = unserialize($incomeScale->value);
                            $amount = $order->price * $incomeScaleArr['t_scale'] / 100;
                            $sendNotice = true;
                            Log::info('lover_relation', [$lover->nickname . "与" . $cUser->nickname . "在同一城市，得收益"]);
                        } else {
                            Log::info('lover_relation', [$lover->nickname . "与" . $cUser->nickname . "不再同一城市"]);
                        }
                    }
                    //dd($incomeScale);
                    $lover->increment('current_balance', $amount);  //总收益 & 余额 ++
                    $lover->increment('balance', $amount);
                    //用户余额记录
                    $user_balance = [];
                    $user_balance['user_id'] = $lover->id;
                    $user_balance['amount'] = $amount;
                    $user_balance['operate_type'] = '1';
                    $user_balance['source'] = '9';
                    $user_balance['remark'] = $lover->nickname . "推荐{$cUser->nickname}成为和会员";
                    UserBalance::create($user_balance);

                    //平台减去分成记账
                    $this->_log_income_log_type($user_id, 2, $order->id, $order->order_code, $amount, $order->pay_type, $user_balance['remark'] . "分成");
                    Log::info('lover_relation', ['id=' . $lover->id . ":" . $lover->nickname . $user_balance['remark']]);

                    if ($sendNotice) {
                        $lover->nickname = $cUser->nickname;
                        try {
                            Event::fire(new RegisterPeople($lover, true));
                        } catch (\Exception $ex) {
                            
                        }
                    }
                }
            }
        }
        
        //活动
        if(_is_festival()){
            $this->_updateVipLeftDay($cUser->id, $left_days, 68, 7);
        }
    }
    
    private function _updateVipLeftDay($uid, $old_vip_left_day, $add_days, $point_vip_source){
        $left_days = get_new_vip_left_day($old_vip_left_day, $add_days);
        UserPointVip::add($uid, $add_days, $point_vip_source);
        $this->_user_update($uid, ['vip_left_day' => $left_days]);
    }

    /**
     * 订单状态更新
     * @param $order
     */
    private function _order_update($order) {
        $order->pay_method = '1';
        $order->order_type = '2';
        $order->save();
    }

    /**
     * 更新订单爱心大使
     * @param unknown $order
     * @param unknown $lover_id
     */
    private function _order_update_lover($order, $lover_id) {
        $order->lover_id = $lover_id;
        $order->save();
    }

    private function _user_update($uid, $update) {
        User::find($uid)->update($update);
    }

    private function _user_listen($id) {
        $question = Question::find($id);
        if ($question) {
            //回答后收益分成
            $incomeScale = IncomeScale::where('key', '2')->first();
            $incomeScaleArr = unserialize($incomeScale->value);
            //指导师分成
            if (isset($incomeScaleArr['t_scale']) && $incomeScaleArr['t_scale'] > 0) {
                $item_t = User::find($question->tutor_id);
                $amount = 1 * $incomeScaleArr['t_scale'] / 100;
                $item_t->increment('current_balance', $amount);  //总收益 & 余额 ++
                $item_t->increment('balance', $amount);
                $item_t->increment('question_amount', $amount);

                //用户余额记录
                $user_balance = [];
                $user_balance['user_id'] = $question->tutor_id;
                $user_balance['amount'] = $amount;
                $user_balance['operate_type'] = '1';
                $user_balance['source'] = '4';
                $user_balance['remark'] = $question->content;
                UserBalance::create($user_balance);
            }
            //提问者分成
            if (isset($incomeScaleArr['a_scale']) && $incomeScaleArr['a_scale'] > 0) {
                $item_a = User::find($question->user_id);
                $amount = 1 * $incomeScaleArr['a_scale'] / 100;
                $item_a->increment('current_balance', $amount);  //总收益 & 余额 ++
                $item_a->increment('balance', $amount);
                $item_a->increment('question_amount', $amount);

                //用户余额记录
                $user_balance = [];
                $user_balance['user_id'] = $question->user_id;
                $user_balance['amount'] = $amount;
                $user_balance['operate_type'] = '1';
                $user_balance['source'] = '4';
                $user_balance['remark'] = $question->content;
                UserBalance::create($user_balance);
            }
        }
    }

    /**
     * 公众号未关注提醒
     *
     * @param
     * @return array
     */
    public function qrcode() {
        $wechat = new Application(config('wechat'));
        $qrcode = $wechat->qrcode;
        $result = $qrcode->temporary(56, 6 * 24 * 3600);
        $ticket = $result->ticket;
        $url = $qrcode->url($ticket);
        return view('wechat.qrcode', compact('url'));
    }

    public function access_token() {
        $app = new Application(config('wechat'));
        $accessToken = $app->access_token;
        $token = $accessToken->getToken();
        echo ($token);
        exit;
    }

}
