<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Models\Coupon;
use App\Models\CouponRule;
use App\Models\CouponUser;
use App\Models\LikeRecord;
use App\Models\Opo;
use App\Models\OpoComment;
use App\Models\Order;
use App\Models\OrderOpo;
use App\Models\User;
use App\Models\UserBalance;
use App\Models\UserPoint;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB, Event,Wechat;

class OpoController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 壹加壹首页
     */
    public function index(Request $request)
    {
        $opo = Opo::first();
        if($opo==null){
            abort(404, '壹加壹项目不存在！');
        }

        $opo->project_intr = replace_content_image_url($opo->project_intr);

        $order = Order::where('pay_type', 3)
            ->where('pay_id', $opo->id)
            ->where('user_id', session('user_info')['id'])
            ->where('order_type', '!=', 3)
            ->with('order_opo', 'user')->orderBy('id', 'desc')->first();
        $comments = OpoComment::where('opo_id', $opo->id)
            ->with(['like_records' => function ($query) {
                $query->where('user_id', session('user_info')['id']);
            }])
            ->with('user')
            ->orderBy('likes', 'desc')->get();
        //课程分享(分享人)
        $share_flg = '0';
        if ($request->input('share') == '1' && (@$order->order_type == '2' || @$order->order_type == '4')) {
            $share_flg = '1';
        }
        //课程分享(被分享人)
        $share_user = $request->input('share_user');
        if ($share_user) {
            $user_share_info = User::find($share_user);
            if ($user_share_info&&$share_user!=session('user_info')['id']) {
                $couponUser = CouponUser::where('user_id',session('user_info')['id'])->
                where('come_from','5')->first();
                if ($couponUser) {
                    //被分享过
                    $share_user = '';
                }
            }
        }
        if ($share_user==session('user_info')['id']) {
            $share_user = '';
        }
        if($order == null || ($order!=null && $order->order_type==1)) {
            return view('opo.index', ['opo' => $opo, 'order' => $order, 'comments' => $comments,'share_flg'=>$share_flg,'wx_js' => Wechat::js(),'share_user'=>$share_user]);
        }else {
            $previews = qiniu_previews($order->order_opo->service_url);
            return view('opo.bought', ['opo' => $opo, 'order' => $order, 'comments' => $comments, 'previews'=>$previews,'share_flg'=>$share_flg,'wx_js' => Wechat::js(),'share_user'=>$share_user]);
        }
    }

    // 课程分享获取优惠券
    public function get_coupon(Request $request)
    {
        $user_id = session('user_info')['id'];
        $share_user_id = $request->input('user_id');
        if ($user_id) {
            $couponRule = CouponRule::where('rule_id','3')->first();
            $coupon_ids = explode(',', $couponRule->coupon_id);
            $couponUser = CouponUser::where('user_id',session('user_info')['id'])->
                where('come_from','5')->first();
                if (!$couponUser) {
                    foreach ($coupon_ids as $coupon_id) {
                        $this->_send_coupon($coupon_id, $user_id, '5');
                        if ($share_user_id > 0) {
                            $this->_send_coupon($coupon_id, $share_user_id, '5');
                        }
                    }
                } else {
                    return response()->json(['code' => 1, 'message' => '红包已经领取过,领取红包失败!']);
                }
            return response()->json(['code' => 0, 'message' => '领取红包成功!']);
        } else {
            return response()->json(['code' => 1, 'message' => '领取红包失败!']);
        }
    }

    public function reportPreviews($id)
    {
        $order = Order::where('pay_type', 3)
            ->where('user_id', session('user_info')['id'])
            ->where('order_type', '!=', 3)
            ->with('opo','order_opo', 'user')->find($id);
        if($order == null)
            abort(404, '壹加壹订单查询失败！');

        $previews = qiniu_previews($order->order_opo->service_url);
        return view('opo.previews', ['previews'=>$previews,'order'=>$order,'wx_js' => Wechat::js()]);
    }

    public function reportShares($id)
    {
        $order = Order::where('pay_type', 3)
            ->where('user_id', session('user_info')['id'])
            ->where('order_type', '!=', 3)
            ->with('opo','order_opo', 'user')->find($id);
        if($order == null)
            abort(404, '壹加壹订单查询失败！');
        return view('opo.report_previews', ['order'=>$order]);
    }

    /**
     * @return array
     *
     * 壹加壹获取评论
     */
    public function comments()
    {
        $opo = Opo::first();
        $comments = OpoComment::where('opo_id', $opo->id)->orderBy('id', 'desc')->limit(4)->get();
        return ['code' => '0', 'message' => 'OK', 'data' => $comments];
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 发表评论
     */
    public function comment($id)
    {
        $opo = Opo::find($id);
        return view('opo.comment', ['opo' => $opo]);
    }

    /**
     * @param $id
     * @return array
     *
     * 提交评论
     */
    public function saveComment($id)
    {
        $opo = Opo::find($id);
        if ($opo == null)
            return ['code' => 1, 'message' => '壹加壹查询失败！'];
        try {
            $comment = new OpoComment();
            $comment->opo_id = $opo->id;
            $comment->user_id = session('user_info')['id'];
            $comment->content = request('content');
            $comment->save();
            get_score(5);
            return ['code' => 0, 'message' => '评论成功！'];
        } catch (\Exception $e) {
            return ['code' => 2, 'message' => '评论失败！'];
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 下单购买页面（选择优惠券、积分、余额）
     */
    public function buy($id)
    {
        $opo = Opo::find($id);
        if ($opo == null) {
            echo "<script>alert('不存在该壹加壹！');history.go(-1);</script>";
            exit;
        }
        $userId = session('user_info')['id'];
        $user = User::find($userId);
        /** 检查是否已下过单 */
        $exitedOrder = Order::where('user_id', $userId)
            ->where('pay_type', 3)
            ->where('pay_id', $opo->id)
            ->where('order_type', '!=', 3)
            ->first();
        if ($exitedOrder != null) {
            if ($exitedOrder->order_type == 1) {
                return redirect()->route('wechat.opo.pay', ['id' => $exitedOrder->id]);
            } else {
                echo "<script>alert('已购买过壹加壹！');history.go(-1);</script>";
                exit;
            }
        }

        /** 各种金额初始化 */
        $couponCut = 0;
        /** 获取可用优惠券与已优惠券，计算优惠券优惠金额 */
        $allCoupons = $this->getUserCoupons('all');
        $couponUser = null;
        if (request('coupon_user_id')) {
            $couponUser = $this->getUserCoupons('fit', intval(request('coupon_user_id')));
            if (count($couponUser)) {
                $couponUser = $couponUser[0];
                if ($couponUser->c_coupon->type == 1) {
                    $couponCut = $couponUser->c_coupon->cut_money;
                } else {
                    $couponCut = number_format($opo->price * (100 - $couponUser->c_coupon->discount) / 100, 2);
                    if ($couponCut >= $opo->price)
                        $couponCut = $opo->price;
                }
            }
        }
        /** 计算可用的积分余额 */
        if ($user->score > $opo->price * 50) {
            $pointsAvailable = intval($opo->price * 50);
            $pointCutAvailable = number_format($opo->price / 2, 2);
        } else {
            $pointsAvailable = $user->score;
            $pointCutAvailable = number_format($user->score / 100, 2);
        }
        return view('opo.buy', ['opo' => $opo, 'user' => $user, 'allCoupons' => $allCoupons, 'couponUser' => $couponUser, 'couponCut' => $couponCut, 'pointCutAvailable' => $pointCutAvailable, 'pointsAvailable' => $pointsAvailable]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 
     * 选择优惠券页面
     */
    public function chooseCoupon($id)
    {
        $opo = Opo::find(intval($id));
        if ($opo == null)
            abort(404, '壹加壹查询失败！');
        $fitCoupons = $this->getUserCoupons('fit', $opo->price);
        $unfitCoupons = $this->getUserCoupons('unfit', $opo->price);
        return view('opo.coupons', ['opo'=>$opo, 'fitCoupons' => $fitCoupons, 'unfitCoupons'=>$unfitCoupons]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * 
     * 提交订单
     */
    public function confirmOrder($id)
    {
        $opo = Opo::find(intval($id));
        if ($opo == null)
            return response()->json(['code' => 1, 'message' => '壹加壹查询失败！']);
        $userId = session('user_info')['id'];
        $user = User::find($userId);
        $isExitedOrder = Order::where('user_id', $userId)->where('pay_type', 3)->where('pay_id', $opo->id)->whereIn('order_type', [1, 2])->count();
        if ($isExitedOrder) {
            return response()->json(['code' => 2, 'message' => '已下单，请勿重复下单！']);
        }
        //开始下单
        DB::beginTransaction();
        try {
            $order = new Order;
            $order->order_code = get_order_code('3');
            $order->user_id = $userId;
            $order->agency_id = 0;
            $order->pay_id = $opo->id;
            $order->pay_type = 3;
            $order->order_type = 1;
            $order->order_name = $opo->title;
            $order->each_price = $opo->price;
            $order->quantity = 1;
            $order->total_price = $opo->price;
            $order->free_flg = 2;
            $remainingPrice = $order->total_price;
            //处理优惠券
            if (request('coupon_user_id')) {
                $couponUserId = intval(request('coupon_user_id'));
                $chosenCoupon = $this->getUserCoupons('fit', $opo->price, $couponUserId);
                if ($chosenCoupon == null) {
                    return response()->json(['code' => 4, 'message' => '优惠券查询失败！']);
                }
                $order->coupon_user_id = $couponUserId;
                if ($chosenCoupon->c_coupon->type == 1) {
                    $couponCut = $chosenCoupon->c_coupon->cut_money;
                } else {
                    $couponCut = number_format($opo->price * (100 - $chosenCoupon->c_coupon->discount) / 100, 2);
                }
                //将优惠券标为已用
                $chosenCoupon->is_used = 1;
                $chosenCoupon->used_at = (string)Carbon::now();
                $chosenCoupon->save();
                //减去优惠金额
                if ($remainingPrice <= $couponCut) {
                    $order->coupon_price = $remainingPrice;
                    $order->order_type = '2';
                    $order->pay_method = '3';
                    $order->point_price = 0;
                    $order->balance_price = 0;
                    $order->price = 0;
                    $order->save();
                    $orderOpo = new OrderOpo();
                    $orderOpo->order_id = $order->id;
                    $orderOpo->opo_id = $opo->id;
                    $orderOpo->process = 0;
                    $orderOpo->save();
                    DB::commit();
                    Event::fire(new OrderPaid($order));
                    return response()->json(['code' => 0, 'message' => '下单成功！', 'data' => route('opo')]);
                } else {
                    $order->coupon_price = $couponCut;
                    $remainingPrice -= $couponCut;
                }
            }
            //处理积分
            if (request('use_point') == 1) {
                //积分扣除部分不超过50%
                if ($user->score > $opo->price * 50) {
                    $pointCutAvailable = number_format($opo->price / 2, 2);
                } else {
                    $pointCutAvailable = number_format($user->score / 100, 2);
                }
                //扣除积分减免
                if ($remainingPrice <= $pointCutAvailable) {
                    $order->point_price = $remainingPrice;
                    $order->balance_price = 0;
                    $user->score = $user->score - intval($remainingPrice * 100);
                    $user->save();
                    $order->order_type = '2';
                    $order->pay_method = '3';
                    $order->price = 0;
                    $order->save();
                    //积分日志
                    $this->pointLog(intval($remainingPrice * 100), 3, 2);
                    //创建order_opo
                    $orderOpo = new OrderOpo();
                    $orderOpo->order_id = $order->id;
                    $orderOpo->opo_id = $opo->id;
                    $orderOpo->process = 0;
                    $orderOpo->save();
                    DB::commit();
                    Event::fire(new OrderPaid($order));
                    return response()->json(['code' => 0, 'message' => '下单成功！', 'data' => route('opo')]);
                } else {
                    $remainingPrice -= $pointCutAvailable;
                    $order->point_price = $pointCutAvailable;
                    //积分日志
                    $this->pointLog($user->score, 3, 2);
                    $user->score = 0;
                    $user->save();
                }
            }
            //处理余额
            if (request('use_balance') == 1) {
                if ($remainingPrice <= $user->current_balance) {
                    $order->balance_price = $remainingPrice;
                    $user->current_balance = $user->current_balance - $remainingPrice;
                    $user->save();
                    $order->order_type = '2';
                    $order->pay_method = '3';
                    $order->price = 0;
                    $order->save();
                    //余额日志
                    $this->balanceLog($remainingPrice, 2, 5);
                    $orderOpo = new OrderOpo();
                    $orderOpo->order_id = $order->id;
                    $orderOpo->opo_id = $opo->id;
                    $orderOpo->process = 0;
                    $orderOpo->save();
                    DB::commit();
                    Event::fire(new OrderPaid($order));
                    return response()->json(['code' => 0, 'message' => '下单成功！', 'data' => route('opo')]);
                } else {
                    $remainingPrice -= $user->current_balance;
                    $this->balanceLog($user->current_balance, 2, 5);
                    $order->balance_price = $user->current_balance;
                    $user->current_balance = 0;
                    $user->save();
                }
            }
            //经过上面的扣款（优惠券、积分、余额），还没支付完，则订单还需走支付流程
            $order->price = $remainingPrice;
            $order->save();
            $orderOpo = new OrderOpo();
            $orderOpo->order_id = $order->id;
            $orderOpo->opo_id = $opo->id;
            $orderOpo->process = 0;
            $orderOpo->save();
            DB::commit();
            return response()->json(['code' => 0, 'message' => '下单成功！', 'data' => route('wechat.opo.pay', ['id' => $order->id])]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['code' => 3, 'message' => '下单失败！']);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 
     * 线下支付
     */
    public function offlinePay($id)
    {
        $id = intval($id);
        $order = Order::where('user_id', session('user_info')['id'])->where('pay_type', 3)->where('order_type', 1)->find($id);
        if ($order == null) {
            echo "<script>alert('不存在该订单！');history.go(-1);</script>";
            exit;
        }
        return view('opo.line_pay', ['order' => $order]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * 
     * 确认线下支付
     */
    public function confirmOfflinePay($id)
    {
        //订单详情
        $order = Order::where('user_id', session('user_info')['id'])->where('pay_type', 3)->where('order_type', 1)->find($id);
        if ($order) {
            $order->pay_method = 2;
            if ($order->save()) {
                return response()->json(['code' => 0, 'message' => '已选择线下支付!']);
            }
        }
        return response()->json(['code' => 1, 'message' => '选择线下支付失败!']);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * 壹加壹评论点赞
     */
    public function like($id)
    {
        $opo = Opo::find($id);
        if ($opo == null)
            return response()->json(['code' => 1, 'message' => '壹加壹查询失败！']);
        $userId = user_info('id');
        $comment = OpoComment::find(request('comment_id'));
        if ($comment == null) {
            return response()->json(['code' => 1, 'message' => '壹加壹评论查询失败!']);
        }
        $likeRecord = LikeRecord::where('user_id', $userId)->where('like_id', $comment->id)->where('like_type', 3)->first();
        if ($likeRecord) {
            return response()->json(['code' => 2, 'message' => '请勿重复点赞!']);
        } else {
            $likeRecord = new LikeRecord;
            $likeRecord->user_id = $userId;
            $likeRecord->like_id = $comment->id;
            $likeRecord->like_type = 3;
            DB::beginTransaction();
            try {
                $likeRecord->save();
                $comment->increment('likes', 1);
                DB::commit();
                return response()->json(['code' => 0, 'message' => '点赞成功!', 'data' => $comment->likes]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['code' => 3, 'message' => '点赞失败!']);
            }
        }
    }

    /**
     * @param string $range all,fit,unfit
     * @param float $price
     * @param null $couponUserId
     * @return mixed
     *
     * 获取特定的优惠券
     */
    private function getUserCoupons($range = 'all', $price = 0.00, $couponUserId = null){
        $baseQuery = CouponUser::where('user_id', session('user_info')['id'])//当前用户的
            ->where('is_used', 2)//未使用的
            ->where('expire_at', '>=', (string)Carbon::now());//未过期的

        $baseQuery = $baseQuery->whereHas('c_coupon', function ($query) use ($range, $price) {
            $query->whereIn('use_scope', [1, 4]);   //满足 scope 类型
            if($range != 'all') {
                if ($range == 'fit') {
                    $query->where(function ($conditionQuery) use ($price) { //满足满减条件的
                        $conditionQuery
                            ->orWhere('type', 2)
                            ->orWhere(function ($amountQuery) use ($price) {
                                $amountQuery->where('type', 1)->where('full_money', '<=', $price);
                            });
                    });
                } else if($range == 'unfit') {
                    $query->where(function ($conditionQuery) use ($price) { //不满足条件的
                        $conditionQuery->where('type', 1)->where('full_money', '<=', $price);
                    });
                }
            }
        });
        if($couponUserId)
            $result = $baseQuery->with('c_coupon')->find($couponUserId);
        else
            $result = $baseQuery->with('c_coupon')->get();
        return $result;
    }

    /**
     * @param $value
     * @param $source
     * @param $moveWay
     * 
     * 积分日志
     */
    private function pointLog($value, $source, $moveWay)
    {
        if ($value>0) {
            $userPoint = new UserPoint();
            $userPoint->user_id = session('user_info')['id'];
            $userPoint->point_value = $value;
            $userPoint->source = $source;
            $userPoint->move_way = $moveWay;
            $userPoint->save();
        }
    }

    /**
     * @param $value
     * @param $operationType
     * @param $source
     * 
     * 余额日志
     */
    private function balanceLog($value, $operationType, $source)
    {
        $userBalance = new UserBalance();
        $userBalance->user_id = session('user_info')['id'];
        $userBalance->amount = $value;
        $userBalance->operate_type = $operationType;
        $userBalance->source = $source;
        $userBalance->save();
    }

    private function _send_coupon($coupon_id,$user_id,$come_from)
    {
        if ($coupon_id&&$user_id) {
            //计算过期时间
            $coupon = Coupon::find($coupon_id);
            if($coupon->available_period_type == 1)
            {
                $expire_at = date('Y-m-d h:i:s',time() + 86400 * $coupon->available_days);
            }else{
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
}
