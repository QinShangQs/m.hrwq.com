<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Models\Vcourse;
use App\Models\VcourseMark;
use App\Models\Carousel;
use App\Models\UserFavor;
use App\Models\LikeRecord;
use App\Models\HotSearch;
use App\Models\Agency;
use App\Models\Order;
use App\Models\UserPoint;
use App\Models\User;
use App\Models\Coupon;
use App\Models\CouponUser;
use App\Models\UserBalance;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Wechat,
    Event,
    DB;
use App\Events\MarkReplay;

class VcourseController extends Controller {

    /** 好看课程首页 */
    public function index(Request $request) {
        //轮播图
        $carouselList = Carousel::whereUseType('2')->orderBy('sort', 'desc')->get();

        $builder = Vcourse::whereStatus('2')
                ->whereNotNull('vcourse.video_tran')
                ->whereNotNull('vcourse.video_free')
                ->whereNotNull('vcourse.bucket');

        $sortField = $request->input('ob', 'created_at');
        $request['ob'] = $sortField;

        if ($sortField == 'biting') {
            //推荐课程
            $vcourseList = $builder->whereRecommend('2')->orderBy('vcourse.sort', 'desc')->get();
        } else {
            if (($search_key = $request->input('search_key'))) {
                $builder->where('title', 'like', '%' . $search_key . '%');
            }
            $vcourseList = $builder->take(100)->orderBy('vcourse.' . $sortField, 'desc')->get();
        }

        if ($userid = session('user_info')['id']) {
            foreach ($vcourseList as $k => $v) {
                $v->userFavor = UserFavor::whereUserId($userid)->whereFavorId($v->id)->whereFavorType('2')->first();
            }
        }

        $wx_js = Wechat::js();
        if (config('app.debug') === false) {
            if (preg_match('/^win/i', PHP_OS)) {
                $data = file_get_contents('E:/sug_link.log');
            } else {
                $data = file_get_contents('/mnt/sug_link.log');
            }
        }
        if (!empty($data)) {
            list($telecast, $foreshow) = explode("\n", $data);
        } else {
            $telecast = '';
            $foreshow = '';
        }

        return view('vcourse.index', compact('carouselList', 'vcourseList', 'wx_js', 'telecast', 'foreshow'));
    }

    /** 好看课程搜索 */
    public function search(Request $request) {
        $builder = Vcourse::whereStatus('2')
                ->whereNotNull('vcourse.video_tran')
                ->whereNotNull('vcourse.video_free')
                ->whereNotNull('vcourse.bucket');

        if (($search_key = $request->input('search_key'))) {
            $builder->where('title', 'like', '%' . $search_key . '%');
        }
        if (($type = $request->input('type'))) {
            $builder->where('type', $type);
        }
        if (($agency_id = $request->input('agency_id'))) {
            $builder->where('agency_id', $agency_id);
        }
        $vcourseList = $builder->orderBy('vcourse.sort', 'desc')->get();
        //热门搜索
        $hot_search = HotSearch::where('type', 1)->orderBy('sort', 'desc')->lists('title');

        $agencyArr = Agency::lists('agency_name', 'id');
        $typeArr = config('constants.type_list');

        return view('vcourse.search', compact('vcourseList', 'hot_search', 'agencyArr', 'typeArr'));
    }

    /** 好看课程一览页 */
    public function more(Request $request, $type) {
        //轮播图
        $carouselList = Carousel::whereUseType('2')->orderBy('sort', 'desc')->get();
        $builder = Vcourse::whereStatus('2')->with('agency')
                ->whereNotNull('vcourse.video_tran')
                ->whereNotNull('vcourse.video_free')
                ->whereNotNull('vcourse.bucket');

        $builder_b = clone $builder;
        $builder_c = clone $builder;

        if ($type == '1') {
            $vcourseList = $builder_b->whereType('1')->orderBy('vcourse.sort', 'desc')->paginate(10);
        } elseif ($type == '2') {
            $vcourseList = $builder_c->whereType('2')->orderBy('vcourse.sort', 'desc')->paginate(10);
        }

        //ajax请求，返回json
        if ($request->ajax()) {
            return $vcourseList->toJson();
        }
        $wx_js = Wechat::js();

        return view('vcourse.more', compact('vcourseList', 'type', 'carouselList', 'wx_js'));
    }

    /** 好看课程详情页 */
    public function detail($id) {
        $user_info = '';
        if (session('user_info')['id']) {
            $user_info = User::where('id', session('user_info')['id'])->first()->toArray();
        }

        //课程详情  
        $vcourseDetail = Vcourse::whereStatus('2')
                ->with(['order' => function ($query) use ($user_info) {
                        $query->where('pay_type', '2');
                        $query->where('user_id', @$user_info['id']);
                        $query->whereIn('order_type', ['1', '2', '4']);
                    }])
                ->whereNotNull('vcourse.video_tran')
                ->whereNotNull('vcourse.video_free')
                ->whereNotNull('vcourse.bucket')
                ->whereId($id)
                ->first();
        if (!$vcourseDetail)
            abort(404, '课程查找失败！');
        //收藏情况
        $userFavor = UserFavor::whereUserId(@$user_info['id'])->whereFavorId($id)->whereFavorType('2')->first();
        //作业&笔记
        $vcourseMarkListA = $this->get_mark_lists($user_info, $id, 1, '!=');
        $vcourseMarkListB = $this->get_mark_lists($user_info, $id);

        //推荐课程
        $recommendVcourseList = Vcourse::whereStatus('2')
                        ->whereNotNull('vcourse.video_tran')
                        ->whereNotNull('vcourse.video_free')
                        ->whereNotNull('vcourse.bucket')
                        ->whereRecommend('2')->orderBy('vcourse.sort', 'desc')->get();
        $wx_js = Wechat::js();

        $vip_left_day = computer_vip_left_day(@$user_info['vip_left_day']);

        return view('vcourse.detail', compact('vcourseDetail', 'vcourseMarkListA', 'vcourseMarkListB', 'recommendVcourseList', 'userFavor', 'user_info', 'wx_js', 'vip_left_day'));
    }

    private function get_mark_lists($user_info, $vcourseId, $visible = -1, $user_id_equal = '=') {
        $builder = VcourseMark::whereVcourseId($vcourseId)->with('user')->with(['like_record' => function ($query) use ($user_info) {
                $query->where('like_type', '2');
                $query->where('user_id', @$user_info['id']);
            }]);

        if ($visible != -1) {
            $builder->whereVisible('1');
        }

        $parentMarkList = $builder->where('user_id', $user_id_equal, @$user_info['id'])
                ->where('parent_id', '=', 0)
                //->orderBy('vcourse_mark.likes', 'desc')
                ->orderBy('vcourse_mark.created_at', 'desc')
                ->get();

        foreach ($parentMarkList as $k => $v) {
            $parentMarkList[$k]['subs'] = VcourseMark::whereParentId($v['id'])
                            ->orderBy('vcourse_mark.created_at', 'asc')->get();
        }

        return $parentMarkList;
    }

    /** 好看课程添加收藏 */
    public function add_favor(Request $request) {
        $id = $request->input('vcourse_id');
        $user_id = session('user_info')['id'];
        $vcourse = Vcourse::where('status', 2)->find($id);

        if ($vcourse == null) {
            return response()->json(['code' => 1, 'message' => '不存在该课程!']);
        }

        $userfavor = UserFavor::where('user_id', $user_id)->where('favor_id', $id)->where('favor_type', 2)->first();
        if ($userfavor) {
            if ($userfavor->delete()) {
                return response()->json(['code' => 0, 'message' => '取消收藏成功!']);
            } else {
                return response()->json(['code' => 1, 'message' => '取消收藏失败!']);
            }
        } else {
            $userfavor = new UserFavor;
            $userfavor->user_id = $user_id;
            $userfavor->favor_id = $id;
            $userfavor->favor_type = 2;
            if ($userfavor->save()) {
                return response()->json(['code' => 2, 'message' => '收藏成功!']);
            } else {
                return response()->json(['code' => 1, 'message' => '收藏失败!']);
            }
        }
    }

    /** 好看评论点赞 */
    public function add_like(Request $request) {
        $id = $request->input('id');
        $user_id = session('user_info')['id'];
        $vcourseMark = VcourseMark::find($id);

        if ($vcourseMark == null) {
            return response()->json(['code' => 1, 'message' => '不存在该评论!']);
        }

        $likeRecord = LikeRecord::where('user_id', $user_id)->where('like_id', $id)->where('like_type', 2)->first();
        if ($likeRecord) {
            return response()->json(['code' => 1, 'message' => '请勿重复点赞!']);
        } else {
            $likeRecord = new LikeRecord;
            $likeRecord->user_id = $user_id;
            $likeRecord->like_id = $id;
            $likeRecord->like_type = 2;
            $vcourseMark->likes++;
            if ($likeRecord->save() && $vcourseMark->save()) {
                return response()->json(['code' => 2, 'message' => '点赞成功!']);
            } else {
                return response()->json(['code' => 1, 'message' => '点赞失败!']);
            }
        }
    }

    /** 好看提交笔记&作业 */
    public function add_mark(Request $request) {
        if ($request->isMethod('post')) {
            $customAttr = [
                'mark_content' => '作业&笔记',
            ];
            $this->validate($request, [
                'mark_content' => 'required|max:10000',
                    ], [], $customAttr);


            $vcourseMark = new VcourseMark();

            $vcourseMark->vcourse_id = $request->input('vcourse_id');
            $vcourseMark->mark_type = $request->input('mark_type');
            $scoretype = '';
            if ($vcourseMark->mark_type == '2') {
                $vcourseMark->visible = '1';
                $scoretype = '6';
            } else {
                $vcourseMark->visible = $request->input('visible');
                $scoretype = '7';
            }
            $vcourseMark->mark_content = $request->input('mark_content');
            $vcourseMark->parent_id = $request->input('parent_id', 0);
            $user_id = session('user_info')['id']; // 当前登录者
            $vcourseMark->user_id = $user_id;

            if ($vcourseMark->save()) {
                get_score($scoretype);
                $vcourseMarkInfo = VcourseMark::whereId($vcourseMark->id)->with('user')->first();
                $vcourseMarkInfo->user->profileIcon = @url($vcourseMarkInfo->user->profileIcon);

                if ($vcourseMark->parent_id > 0) {
                    Event::fire(new MarkReplay($request->input('vcourse_title', ''), $request->input('parent_openid', ''), $vcourseMarkInfo->user->nickname, $request->input('mark_content')));
                }

                return response()->json(['status' => true, 'vcourseMarkInfo' => $vcourseMarkInfo->toJson(),
                            'msg' => $vcourseMark->parent_id > 0 ? '回复成功' : '作业&笔记提交成功'
                ]);
            } else {
                return response()->json(['status' => false, 'msg' => $vcourseMark->parent_id > 0 ? '回复失败' : '作业&笔记提交失败']);
            }
        }
    }

    /** 好看增加观看次数 */
    public function add_view_cnt(Request $request) {
        if ($request->isMethod('post')) {
            $vcourse_id = $request->input('id');
            $vcourse = Vcourse::find($vcourse_id);
            if ($vcourse) {
                $vcourse->view_cnt += 1;
                if ($vcourse->save()) {
                    get_score(12);
                    return response()->json(['status' => true]);
                }
            }
        }
    }

    //好看推荐
    public function recommend_list(Request $request) {
        $builder = Vcourse::whereStatus('2')->with('agency')
                        ->whereNotNull('vcourse.video_tran')
                        ->whereNotNull('vcourse.video_free')
                        ->whereNotNull('vcourse.bucket')
                        ->whereRecommend('2')->orderBy('vcourse.sort', 'desc');
        $data = $builder->paginate(10);

        //ajax请求，返回json
        if ($request->ajax()) {
            return $data->toJson();
        }

        return $data;
    }

    /** 好看付费订单 */
    public function order_free(Request $request) {
        if ($request->isMethod('post')) {

            $user_info = session('user_info');
            $vcourse_id = $request->input('vcourse_id');
            //课程详情  
            $vcourseDetail = Vcourse::whereStatus('2')
                    ->with(['order' => function ($query) use ($user_info) {
                            $query->where('pay_type', '2');
                            $query->where('user_id', $user_info['id']);
                            $query->whereIn('order_type', ['2', '4']);
                        }])
                    ->whereNotNull('vcourse.video_tran')
                    ->whereNotNull('vcourse.video_free')
                    ->whereNotNull('vcourse.bucket')
                    ->whereId($vcourse_id)
                    ->first();

            if (!empty($user_info['id']) && !empty($vcourseDetail['id']) && count($vcourseDetail->order) == '0') {
                $order = new Order;
                $order->order_code = get_order_code('2');
                $order->user_id = $user_info['id'];
                $order->agency_id = @$vcourseDetail->agency_id;
                $order->pay_id = $vcourse_id;
                $order->pay_type = 2;
                $order->order_type = 4;
                $order->order_name = $vcourseDetail->title;
                $order->free_flg = 1;
                if ($order->save()) {
                    return response()->json(['status' => true, 'msg' => '参加课程成功!']);
                } else {
                    return response()->json(['status' => false, 'msg' => '参加课程失败!']);
                }
            } else {
                return response()->json(['status' => false, 'msg' => '参加课程失败']);
            }
        }
    }

    /** 好看免费订单 */
    public function order($id, Request $request) {
        $temp = $request->input('temp'); // 收货地址
        $coupon_user_id = $request->input('coupon_user_id'); // 用户优惠券id
        $coupon_id = $request->input('coupon_id'); // 优惠券id
        $coupon_type = $request->input('coupon_type'); // 优惠券类型
        $coupon_cutmoney = $request->input('coupon_cutmoney'); // 优惠券减免
        $coupon_discount = $request->input('coupon_discount'); // 优惠券折扣

        $is_point = $request->input('is_point'); // 积分开关
        $usable_point = $request->input('usable_point'); // 可用积分
        $usable_money = $request->input('usable_money'); // 积分可抵用现金
        $is_balance = $request->input('is_balance'); // 可用余额开关
        $usable_balance = $request->input('usable_balance'); // 可用余额
        $total_price = $request->input('total_price'); // 总计
        // 当前用户
        $user_id = session('user_info')['id'];
        $user = User::find($user_id);

        //课程详情  
        $vcourseDetail = Vcourse::whereStatus('2')
                ->with(['order' => function ($query) use ($user_id) {
                        $query->where('pay_type', '2');
                        $query->where('user_id', $user_id);
                        $query->whereIn('order_type', ['1', '2', '4']);
                    }])
                ->whereNotNull('vcourse.video_tran')
                ->whereNotNull('vcourse.video_free')
                ->whereNotNull('vcourse.bucket')
                ->whereId($id)
                ->first();
        if (!$vcourseDetail)
            abort(404, '课程查找失败！');
        if (count($vcourseDetail->order) > 0)
            abort(403, '课程订单未取消或已完成！');

        // 优惠券
        $coupon_name = '';
        if ($coupon_id) {
            $coupon = Coupon::find($coupon_id);
            if ($coupon == null) {
                echo "<script>alert('不存在该优惠券！');history.go(-1);</script>";
                exit;
            }
            $coupon_name = $coupon->name; // 优惠券的名称
            // 选完优惠券，可用余额与总价相应变化
            if ($coupon_type == 1) {
                $total_price = $total_price - $coupon_cutmoney;
                if ($total_price < 0) {
                    $total_price = 0;
                    $usable_point = 0;
                    $usable_money = 0;
                } else if ($total_price > 0 && $total_price < $user->current_balance) {
                    $usable_balance = $total_price;
                } else {
                    $usable_balance = $user->current_balance;
                }
            }
            if ($coupon_type == 2) {
                $total_price = $total_price * $coupon_discount / 100;
                if ($total_price < $user->current_balance) {
                    $usable_balance = $total_price;
                } else {
                    $usable_balance = $user->current_balance;
                }
            }

            if ($temp == 2) {
                $is_point = 0;
                $is_balance = 0;
            }
        }

        $couponusers_usable = $this->getCouponsUsable($user_id, $vcourseDetail);

        return view('vcourse.order', compact('vcourseDetail', 'coupon_name', 'user_id', 'user', 'coupon_user_id', 'coupon_id', 'coupon_type', 'coupon_cutmoney', 'coupon_discount', 'is_point', 'usable_point', 'usable_money', 'is_balance', 'usable_balance', 'total_price', 'couponusers_usable'));
    }

    // 优惠券
    public function coupon(Request $request, $id) {
        $id = intval($id);

        $coupon = $request->input('coupon');
        $is_point = $request->input('is_point');
        $usable_point = $request->input('usable_point');
        $usable_money = $request->input('usable_money');
        $is_balance = $request->input('is_balance');
        $usable_balance = $request->input('usable_balance');

        $vcourse = Vcourse::find($id);
        if ($vcourse == null) {
            echo "<script>alert('不存在该课程！');history.go(-1);</script>";
            exit;
        }

        $user_id = session('user_info')['id'];

        //计算总金额，判断是否满足 满减
        $total_price = $vcourse->price;

        // 可用优惠券
        $coupons_usable = $this->getCouponsUsable($user_id, $vcourse);
        $couponusers_usable = [];
        // 不可用优惠券
        $couponusers_unusable = [];
        if ($coupons_usable)
            foreach ($coupons_usable as $coupon) {
                if ($coupon->full_money > $total_price) {
                    $couponusers_unusable[] = $coupon;
                } else {
                    $couponusers_usable[] = $coupon;
                }
            }

        $coupon_use_scope = config('constants.coupon_use_scope');
        // dd($couponusers_usable);

        return view('vcourse.coupon', compact('couponusers_usable', 'couponusers_unusable', 'vcourse', 'user_id', 'is_point', 'usable_point', 'usable_money', 'is_balance', 'usable_balance', 'coupon_use_scope', 'total_price'));
    }

    /** 好看免费订单保存 */
    public function order_save(Request $request) {
        $user_info = User::where('id', session('user_info')['id'])->first()->toArray();
        //首次支付
        if ($request->isMethod('post')) {
            $id = $request->input('id');

            //课程详情
            $vcourseDetail = Vcourse::whereStatus('2')
                    ->with(['order' => function ($query) use ($user_info) {
                            $query->where('pay_type', '2');
                            $query->where('user_id', $user_info['id']);
                            $query->whereIn('order_type', ['1', '2', '4']);
                        }])
                    ->whereNotNull('vcourse.video_tran')
                    ->whereNotNull('vcourse.video_free')
                    ->whereNotNull('vcourse.bucket')
                    ->whereId($id)
                    ->first();
            if (!$vcourseDetail)
                return response()->json(['status' => false, 'msg' => '课程不存在！']);
            if (count($vcourseDetail->order) > 0)
                return response()->json(['status' => false, 'msg' => '课程订单未取消或已完成！']);
            if (!empty($user_info['id']) && !empty($vcourseDetail['id']) && count($vcourseDetail->order) == '0') {
                DB::beginTransaction();
                try {
                    $order = new Order;
                    $order->order_code = get_order_code('2');
                    $order->user_id = $user_info['id'];
                    $order->agency_id = @$vcourseDetail->agency_id;
                    $order->pay_id = $id;
                    $order->pay_type = 2;
                    $order->order_type = 1;
                    $order->order_name = $vcourseDetail->title;
                    $order->free_flg = 2;

                    $last_price = $vcourseDetail->price;
                    $order->total_price = $last_price; // 任何减免之前的价格
                    // 优惠券
                    $coupon_id = $request->input('coupon_id');
                    if ($coupon_id) {
                        $couponuser = CouponUser::where('coupon_id', $coupon_id)
                                ->where('user_id', $user_info['id'])
                                ->where('is_used', 2)
                                ->whereNull('used_at')
                                ->where('expire_at', '>', date('Y-m-d'))
                                ->first();
                        if ($couponuser) {
                            $coupon_info = Coupon::find($coupon_id);
                            // 代金券
                            if ($coupon_info->type == 1) {
                                $coupon_score = $coupon_info->cut_money;
                                $last_price -= $coupon_score;
                            }
                            // 折扣券
                            if ($coupon_info->type == 2) {
                                $coupon_score = $last_price * (1 - $coupon_info->discount / 100);
                                $last_price -= $coupon_score;
                            }
                            $order->coupon_user_id = $couponuser->id; // 优惠券id
                            $order->coupon_price = $coupon_score; // 优惠券减免的金额
                            // 相应的减少优惠券(coupon_user表更改优惠券的使用状态)
                            $couponuser->is_used = 1;
                            $couponuser->used_at = date('Y-m-d H:i:s');
                            $couponuser->save();
                        }

                        if ($last_price < 0) {
                            $last_price = 0;
                            goto zero_pay;
                        }
                    }

                    // 积分抵用
                    $is_point = $request->input('is_point'); // 积分抵用开关
                    $usable_point = $request->input('usable_point'); // 积分减免的积分
                    $usable_money = $request->input('usable_money');
                    ; // 积分减免的金额
                    $user = User::find($user_info['id']);

                    if ($is_point && $usable_point > 0 && ($order->total_price / 2) >= $usable_money && ($usable_money * 100) == $usable_point && $usable_point <= $user->score) {

                        $last_price -= $usable_money;

                        $order->point_price = $usable_money;

                        // 相应的减少积分（user 用户表积分减少，user_point 积分增减记录）
                        $user->score -= $usable_point;
                        $user->save();

                        $userpoint = new UserPoint;
                        $userpoint->user_id = $user_info['id'];
                        $userpoint->point_value = $usable_point;
                        $userpoint->source = 3;
                        $userpoint->move_way = 2;
                        $userpoint->save();

                        if ($last_price < 0) {
                            $last_price = 0;
                            goto zero_pay;
                        }
                    }


                    // 可用余额
                    $is_balance = $request->input('is_balance'); // 可用余额开关
                    $user = User::find($user_info['id']);
                    $usable_balance = $request->input('usable_balance'); // 可用余额
                    if ($is_balance && $usable_balance <= $user->current_balance && $usable_balance > 0) {
                        $last_price -= $usable_balance;
                        $order->balance_price = $usable_balance;

                        // 相应的减少余额(user表的当前余额减少,user_balance记录减少)
                        $user->current_balance -= $usable_balance;
                        $user->save();

                        $userbalance = new UserBalance;
                        $userbalance->user_id = $user_info['id'];
                        $userbalance->amount = $usable_balance;
                        $userbalance->operate_type = 2;
                        $userbalance->source = 5;
                        $userbalance->save();
                    }
                    if ($last_price < 0) {
                        DB::rollBack();
                        return response()->json(['status' => false, 'msg' => '订单金额异常']);
                    }
                    zero_pay:
                    //实际支付金额为零
                    if ($last_price == 0) {
                        $order->order_type = '2';
                        $order->pay_method = '3';
                    }
                    $order->price = $last_price;
                    $order->free_flg = 2;
                    $order->save();
                    //实际支付金额为零
                    if ($last_price == 0) {
                        //通知
                        Event::fire(new OrderPaid($order));
                        DB::commit();
                        return response()->json(['status' => true, 'msg' => '下单成功！', 'vcourse_id' => $id]);
                    }
                    DB::commit();
                    return response()->json(['status' => true, 'msg' => '下单成功！', 'order_id' => $order->id]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['status' => false, 'msg' => '下单失败！']);
                }
            } else {
                return response()->json(['status' => false, 'msg' => '下单失败！']);
            }
        } else {
            //未支付继续支付
            return response()->json(['status' => false, 'msg' => '下单失败！']);
        }
    }

    private function getCouponsUsable($user_id, $vcourse) {
        $coupons_usable = [];

        //初步符合要求的优惠券数量
        $couponusers_usable = CouponUser::leftJoin('coupon', 'coupon.id', '=', 'coupon_user.coupon_id')
                ->where('coupon_user.user_id', $user_id)
                ->where('coupon_user.is_used', 2)
                ->where('coupon_user.expire_at', '>=', date('Y-m-d'))
                ->get();
        foreach ($couponusers_usable as $key => $value) {
            // 将不满足适用范围的优惠券和没到使用时间的优惠券注销
            if ($value->available_period_type == 2 && $value->available_start_time > date('Y-m-d')) {
                continue;
            }
            if ($value->use_scope == 1 || $value->use_scope == 3) {
                $coupons_usable[] = $value;
                continue;
            }
            // 该课程的类型id
            $agency_id = $vcourse->agency_id;
            // 该课程的id
            // 好课课程类别数组或者课程ID数组
            $arr_scope_val = explode(',', $value->use_scope_val);

            if ($value->use_scope == 8 && in_array($agency_id, $arr_scope_val)) {
                $coupons_usable[] = $value;
                continue;
            }
            if ($value->use_scope == 9 && in_array($vcourse->id, $arr_scope_val)) {
                $coupons_usable[] = $value;
                continue;
            }
        }
        return $coupons_usable;
    }

}
