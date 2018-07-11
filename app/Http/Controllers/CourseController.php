<?php
/**
 * 好课模块
 */
namespace App\Http\Controllers;

use App\Models\LikeRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, QrCode, Log, Event, Wechat;
use App\Models\Carousel;
use App\Models\Course;
use App\Models\CourseReportAdmin;
use App\Models\Agency;
use App\Models\CourseComment;
use App\Models\UserPoint;
use App\Models\HotSearch;
use App\Models\Order;
use App\Models\OrderCourse;
use App\Models\UserFavor;
use App\Models\User;
use App\Models\UserReceiptAddress;
use App\Models\Coupon;
use App\Models\CouponUser;
use App\Models\CouponRule;
use App\Models\UserBalance;
use App\Events\OrderPaid;
use EasyWeChat\Foundation\Application;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->admin_url = config('constants.admin_url');
    }

    public function index(Request $request)
    {
        // 轮播图
        $carousels = Carousel::where('use_type', 1)->orderBy('sort', 'desc')->get();
        foreach ($carousels as &$value) {
            $value->image_url = $this->admin_url . $value->image_url;
        }

        // 热门搜索
        $hotsearch = HotSearch::where('type', 2)->orderBy('sort', 'desc')->lists('title');

        // 好课列表
        $courses = $this->course_list($request);

        return view('course.index', ['carousels' => $carousels, 'courses' => $courses, 'hotsearch' => $hotsearch,'wx_js' => Wechat::js()]);
    }

    public function block_index(Request $request)
    {
        // 轮播图
        $carousels = Carousel::where('use_type', 1)->orderBy('sort', 'desc')->get();
        foreach ($carousels as &$value) {
            $value->image_url = $this->admin_url . $value->image_url;
        }

        // 热门搜索
        $hotsearch = HotSearch::where('type', 2)->orderBy('sort', 'desc')->lists('title');

        // 好课列表
        $courses = $this->course_list($request);

        return view('course.index', ['carousels' => $carousels, 'courses' => $courses, 'hotsearch' => $hotsearch,'block'=>true]);
    }

    //课程列表
    public function course_list(Request $request)
    {
        $builder = Course::with(['area', 'agency'])->where('status', 2);

        if (($search_key = $request->input('search_key'))) {
            $builder->where('title', 'like', '%' . $search_key . '%');
            $builder->orWhere('teacher_intr', 'like', '%' . $search_key . '%');
        }
        if (($type = $request->input('type'))) {
            $builder->where('type', $type);
        }
        if (($agency_id = $request->input('agency_id'))) {
            $builder->where('agency_id', $agency_id);
        }
        if (($city = $request->input('city'))) {
            $builder->where('city', $city);
        }

        $data = $builder->orderBy('recommend', 'desc')
            ->orderBy('sort', 'desc')
            ->paginate(10);

        foreach ($data as &$value) {
            $value->picture = $this->admin_url . $value->picture;
        }

        //ajax请求，返回json
        if ($request->ajax()) {
            return $data->toJson();
        }

        return $data;
    }

    /** 好课课程搜索 */
    public function search(Request $request)
    {
        // 好课列表
        $courses = $this->course_list($request);
        // 热门搜索
        $hot_search = HotSearch::where('type', 2)->orderBy('sort', 'desc')->lists('title');

        // 课程类别
        $agencyArr = Agency::lists('agency_name', 'id');
        // 收费类别
        $typeArr = config('constants.type_list');
        // 所有合伙人的城市
        $partnerCitys = User::select('area.area_id', 'area.area_name')
            ->leftjoin('area', 'area.area_id', '=', 'user.partner_city')
            ->where('user.role', 3)
            ->where('user.block', 1)
            ->whereNotNull('user.partner_city')
            ->get();
        $cityArr = array();
        foreach ($partnerCitys as &$value) {
            $cityArr[$value->area_id] = $value->area_name;
        }

        return view('course.search', compact('courses', 'hot_search', 'agencyArr', 'typeArr', 'cityArr'));
    }
    
    //静态链接
    public function staticlink(Request $request, $id)
    {
    	 $id = intval($id);
         $carousel = Carousel::find($id);
         $carousel->redirect_content = replace_content_image_url($carousel->redirect_content);
         return view('course.staticlink', [
            'carousel'=>$carousel
        ]);
    }

    // 课程详情
    public function detail(Request $request, $id)
    {
        $id = intval($id);
        $course = Course::where('status', 2)->find($id);
        if ($course == null) {
            echo "<script>alert('不存在该课程！');history.go(-1);</script>";
            exit;
        }

        // 课程图片 图片路径前面添加上后台路径
        $course->picture = $this->admin_url . $course->picture;
        // 课程安排 替换图片地址为后台完整地址
        $course->course_arrange = replace_content_image_url($course->course_arrange);

        $courseNew = new Course();
        $similarCourses = $courseNew->where('agency_id',$course->agency_id)->select('id')->get();

        $courseIds = array();
        if(!empty($similarCourses->toArray())){
            foreach($similarCourses as $courseItem ){
                $courseIds[] = $courseItem->id;
            }
        }

        // 评论
        $course_comments = CourseComment::with(['user'])->whereIn('course_id', $courseIds)->orderBy('created_at','desc')->get();

        // 推荐课程
        $recommend_courses = Course::where('recommend', 2)->where('status', 2)->get();
        foreach ($recommend_courses as &$value) {
            $value->picture = $this->admin_url . $value->picture;
        }

        $session_mobile = '';
        if (isset(session('user_info')['mobile'])) {
            $session_mobile = session('user_info')['mobile'];
        }

        $order = Order::where('user_id', session('user_info')['id'])->where('pay_type', 1)->where('pay_id', $id)->where('order_type',1)->first();
        $orderPaid = Order::where('user_id', session('user_info')['id'])->where('pay_type', 1)->where('pay_id', $id)->whereIn('order_type',['2','3'])->first();

        $userfavor='';
        $share_flg = '0';
        $subscribe= 0;
        $user_id='';
        //课程分享(被分享人)
        $share_user = $request->input('share_user');
        if (is_wechat()) {
            $userfavor = UserFavor::where('user_id', session('user_info')['id'])->where('favor_id', $id)->where('favor_type', 1)->first();
            //是否关注过公众号
            $wechat = new Application(config('wechat'));
            $userService = $wechat->user;
            $userWechat = $userService->get(session('user_info')['openid']);
            $subscribe = $userWechat->subscribe;

            //课程分享(分享人)
            if ($request->input('share') == '1') {
                $share_flg = '1';
            }
            
            if ($share_user) {
                $user_share_info = User::find($share_user);
                if ($user_share_info && $share_user != session('user_info')['id']) {
                    $couponUser = CouponUser::where('user_id', session('user_info')['id'])->
                    where('come_from', '3')->where('come_from_id', $id)->first();
                    if ($couponUser) {
                        //被分享过
                        $share_user = '';
                    }
                }
            }
            if ($share_user == session('user_info')['id']) {
                $share_user = '';
            }
            $user_id = session('user_info')['id'];
        }

        $requestUri = $request->getRequestUri();
        return view('course.detail', [
            'course' => $course,
            'course_comments' => $course_comments,
            'recommend_courses' => $recommend_courses,
            'session_mobile' => $session_mobile,
            'order' => $order,
            'orderPaid' => $orderPaid,
            'userfavor' => $userfavor,
            'requestUri' => $requestUri,
            'subscribe' => $subscribe,
            'share_flg' => $share_flg,
            'wx_js' => Wechat::js(),
            'share_user' => $share_user,
            'user_id' => $user_id,
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     *
     * 课程评论点赞
     */
    public function commentLike($id)
    {
        $course = Course::find($id);
        if ($course == null)
            return response()->json(['code' => 1, 'message' => '课程查询失败！']);
        $userId = user_info('id');
        $comment = CourseComment::find(request('comment_id'));
        if ($comment == null) {
            return response()->json(['code' => 1, 'message' => '课程评论查询失败!']);
        }
        $likeRecord = LikeRecord::where('user_id', $userId)->where('like_id', $comment->id)->where('like_type', 1)->first();
        if ($likeRecord) {
            return response()->json(['code' => 2, 'message' => '请勿重复点赞!']);
        } else {
            $likeRecord = new LikeRecord;
            $likeRecord->user_id = $userId;
            $likeRecord->like_id = $comment->id;
            $likeRecord->like_type = 1;
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

    // 课程分享获取优惠券
    public function get_coupon(Request $request)
    {
        $user_id = session('user_info')['id'];
        $course_id = $request->input('id');
        $share_user_id = $request->input('user_id');
        if ($user_id && $course_id) {
            $couponRule = CouponRule::where('rule_id', '3')->first();
            $coupon_ids = explode(',', $couponRule->coupon_id);
            $couponUser = CouponUser::where('user_id', session('user_info')['id'])
                ->where('come_from', '3')
                ->where('come_from_id', $course_id)->first();
            if (!$couponUser) {
                foreach ($coupon_ids as $coupon_id) {
                    $this->_send_coupon($coupon_id, $user_id, '3', $course_id);
                    if ($share_user_id > 0) {
                        $this->_send_coupon($coupon_id, $share_user_id, '3', $course_id);
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

    // 课程收藏与取消收藏
    public function collection(Request $request)
    {
        $id = $request->input('id');
        $course = Course::where('status', 2)->find($id);

        if ($course == null) {
            return response()->json(['code' => 1, 'message' => '不存在该课程!']);
        }

        $userfavor = UserFavor::where('user_id', session('user_info')['id'])->where('favor_id', $id)->where('favor_type', 1)->first();
        if ($userfavor) {
            if ($userfavor->delete()) {
                return response()->json(['code' => 0, 'message' => '取消收藏成功!']);
            } else {
                return response()->json(['code' => 1, 'message' => '取消收藏失败!']);
            }
        } else {
            $userfavor = new UserFavor;
            $userfavor->user_id = session('user_info')['id'];
            $userfavor->favor_id = $id;
            $userfavor->favor_type = 1;
            if ($userfavor->save()) {
                return response()->json(['code' => 2, 'message' => '收藏成功!']);
            } else {
                return response()->json(['code' => 1, 'message' => '收藏失败!']);
            }
        }
    }

    // 课程评论  页面
    public function comment($id)
    {
        $id = intval($id);
        $course = Course::where('status', 2)->find($id);
        if ($course == null) {
            echo "<script>alert('不存在该课程！');history.go(-1);</script>";
            exit;
        }
        
        //指导师课程并且当前用户是指导师身份
        if($course->is_tutor_course == 1 && user_info()['role']  == 2){
             return view('course.comment', ['course' => $course]);
        }
        
        $order = Order::where('user_id', session('user_info')['id'])->where('pay_type', 1)->where('pay_id', $id)->whereIn('order_type', ['2', '4'])->first();
        if ($order == null) {
            echo "<script>alert('参加过该课程，方可进行评论！');history.go(-1);</script>";
            exit;
        }
        return view('course.comment', ['course' => $course]);
    }

    // 课程评论 添加
    public function do_comment(Request $request)
    {
        $id = $request->input('id');
        $course = Course::where('status', 2)->find($id);
        if ($course == null) {
            return response()->json(['code' => 1, 'message' => '不存在该课程！']);
        }

        $coursecomment = new CourseComment;
        $coursecomment->course_id = $id;
        $coursecomment->user_id = session('user_info')['id'];
        $coursecomment->content = $request->input('content');


        if ($coursecomment->save()) {
            get_score(5);
            return response()->json(['code' => 0, 'message' => '评论成功!']);
        } else {
            return response()->json(['code' => 1, 'message' => '评论失败!']);
        }
    }

    // 参加课程 免费
    public function join_free(Request $request)
    {
        $id = $request->input('id');
        $course = Course::where('status', 2)->find($id);
        if ($course == null) {
            return response()->json(['code' => 1, 'message' => '不存在该课程！']);
        }
        if(intval($course->participate_num)>=intval($course->allow_num)){
        	return response()->json(['code' => 1, 'message' => '参与人数已经达到上限，请参加其他课程！']);
        }
        $user = User::find(session('user_info')['id']);
        if (!$user) {
            return response()->json(['code' => 1, 'message' => '参加课程失败!']);
        }
        DB::beginTransaction();
        try {

            $order = new Order;
            $order->order_code = get_order_code('1');
            $order->user_id = session('user_info')['id'];
            $order->agency_id = $course->agency_id;
            $order->pay_id = $id;
            $order->pay_type = 1;
            $order->order_type = 2;
            $order->order_name = $course->title;
            $order->free_flg = 1;
            $order->save();

            $ordercourse = new OrderCourse;
            $ordercourse->order_id = $order->id;
            $ordercourse->user_city = $user->city;
            $ordercourse->report_flg = 1;
            $ordercourse->save();

            $course->participate_num += 1;
            $course->save();

            //通知&发送二维码
            Event::fire(new OrderPaid($order));
            DB::commit();
            return response()->json(['code' => 0, 'message' => '参加课程成功!']);
        } catch (\EasyWeChat\Core\Exceptions\HttpException $e) {
            DB::rollBack();
            return response()->json(['code' => 1, 'message' => '无法推送报道二维码,请在公众号上点击自定义菜单进行操作!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('course', [$e]);
            return response()->json(['code' => 1, 'message' => '参加课程失败!']);
        }

    }

    // 参加课程 收费  页面
    public function join_charge(Request $request, $id)
    {
        $coupon_user_id = $request->input('coupon_user_id');// 用户优惠券id
        $coupon_id = $request->input('coupon_id');// 优惠券id
        $coupon_type = $request->input('coupon_type');// 优惠券类型
        $coupon_cutmoney = $request->input('coupon_cutmoney');// 优惠券减免
        $coupon_discount = $request->input('coupon_discount');// 优惠券折扣

        $package_flg = $request->input('package_flg');// 单人或者家庭套餐
        $package_prices = $request->input('package_prices');// 价格
        $number = $request->input('number');// 购买数量
        $is_point = $request->input('is_point');// 积分开关
        $usable_point = $request->input('usable_point');// 可用积分
        $usable_money = $request->input('usable_money');// 积分可抵用现金
        $is_balance = $request->input('is_balance');// 可用余额开关
        $usable_balance = $request->input('usable_balance');// 可用余额
        $total_price = $request->input('total_price');// 总计


        $id = intval($id);
        $course = Course::where('status', 2)->find($id);
        if ($course == null) {
            echo "<script>alert('不存在该课程！');history.go(-1);</script>";
            exit;
        }

        $course->picture = $this->admin_url . $course->picture;

        // 当前用户
        $user_id = session('user_info')['id'];
        $user = User::find($user_id);

        // 优惠券
        $coupon_name = '';
        if ($coupon_id) {
            $coupon = Coupon::find($coupon_id);
            if ($coupon == null) {
                echo "<script>alert('不存在该优惠券！');history.go(-1);</script>";
                exit;
            }
            $coupon_name = $coupon->name;// 优惠券的名称
            // 选完优惠券，可用余额与总价相应变化
            if ($coupon_type == 1) {
                $total_price = $total_price - $coupon_cutmoney;
                if ($total_price < $user->current_balance) {
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
        }
        $couponusers_usable = $this->getCouponsUsable($user_id, $course);

        return view('course.join_charge', compact('course', 'coupon_name', 'user_id', 'user', 'coupon_user_id', 'coupon_id', 'coupon_type', 'coupon_cutmoney', 'coupon_discount', 'package_flg', 'package_prices', 'number', 'is_point', 'usable_point', 'usable_money', 'is_balance', 'usable_balance', 'total_price', 'couponusers_usable'));
    }

    // 参加课程 收费
    public function do_join_charge(Request $request)
    {
        $this->validate($request, [
            'number' => 'required|min:1|integer',
        ], [], [
            'number' => '购买数量',
        ]);
        $id = $request->input('id');
        $course = Course::with(['agency'])->where('status', 2)->find($id);
        if ($course == null) {
            echo "<script>alert('不存在该课程！');history.go(-1);</script>";
            exit;
        }
    	if(intval($course->participate_num)>=intval($course->allow_num)){
            echo "<script>alert('参与人数已经达到上限，请参加其他课程！');history.go(-1);</script>";
            exit;
        }

        // 禁止反复购买
        // $has_order = Order::where('user_id', session('user_info')['id'])->where('pay_type', 1)->where('pay_id', $id)->whereIn('order_type', ['1', '2', '4'])->first();
        // if ($has_order) {
        //     echo "<script>alert('您已经参加该课程！');history.go(-1);</script>";
        //     exit;
        // }

        $user_id = session('user_info')['id'];
        DB::beginTransaction();
        try {
            // 添加订单（主表 order表）
            $order = new Order;
            $order->order_code = get_order_code('1');
            $order->user_id = $user_id;
            $order->agency_id = $course->agency_id;
            $order->pay_id = $id;
            $order->pay_type = 1;
            $order->order_type = 1;
            $order->order_name = $course->title;

            //套餐价格
            if ($request->input('package_flg') == '2') {
                $last_price = $course->package_price;
            } else {
                $last_price = $course->price;
            }

            $order->each_price = $last_price;

            // 购买数量
            $number = $request->input('number');
            if ($number >= 1) {
                $last_price = $last_price * $number;
            }
            $order->quantity = $number;
            $order->total_price = $last_price;// 任何减免之前的价格


            // 优惠券
            $coupon_id = $request->input('coupon_id');
            if ($coupon_id) {
                $couponuser = CouponUser::where('coupon_id', $coupon_id)
                    ->where('user_id', $user_id)
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
                    else if ($coupon_info->type == 2) {
                        $coupon_score = $last_price * (1 - $coupon_info->discount / 100);
                        $last_price -= $coupon_score;
                    }
                    $order->coupon_user_id = $couponuser->id;// 优惠券id
                    $order->coupon_price = $coupon_score;// 优惠券减免的金额

                    // 相应的减少优惠券(coupon_user表更改优惠券的使用状态)
                    $couponuser->is_used = 1;
                    $couponuser->used_at = date('Y-m-d H:i:s');
                    $couponuser->save();
                }

            }

            // 积分抵用
            $is_point = $request->input('is_point');// 积分抵用开关
            $usable_point = $request->input('usable_point');// 积分减免的积分
            $usable_money = $request->input('usable_money');;// 积分减免的金额
            $user = User::find($user_id);

            if ($is_point && $usable_point > 0 && ($order->total_price / 2) >= $usable_money && ($usable_money * 100) == $usable_point && $usable_point <= $user->score) {

                $last_price -= $usable_money;

                $order->point_price = $usable_money;

                // 相应的减少积分（user 用户表积分减少，user_point 积分增减记录）
                $user->score -= $usable_point;
                $user->save();

                $userpoint = new UserPoint;
                $userpoint->user_id = $user_id;
                $userpoint->point_value = $usable_point;
                $userpoint->source = 3;
                $userpoint->move_way = 2;
                $userpoint->save();
            }


            // 可用余额
            $is_balance = $request->input('is_balance');// 可用余额开关
            $user = User::find($user_id);
            $usable_balance = $request->input('usable_balance');// 可用余额
            if ($is_balance && $usable_balance <= $user->current_balance && $usable_balance > 0) {
                $last_price -= $usable_balance;
                $order->balance_price = $usable_balance;

                // 相应的减少余额(user表的当前余额减少,user_balance记录减少)
                $user->current_balance -= $usable_balance;
                $user->save();

                $userbalance = new UserBalance;
                $userbalance->user_id = $user_id;
                $userbalance->amount = $usable_balance;
                $userbalance->operate_type = 2;
                $userbalance->source = 5;
                $userbalance->save();
            }
            if ($last_price < 0) {
                echo "<script>alert('订单金额异常！');history.go(-1);</script>";
                exit;
            }
            //实际支付金额为零
            if ($last_price == 0) {
                $order->order_type = '2';
                $order->pay_method = '3';
            }
            $order->price = $last_price;
            $order->free_flg = 2;
            $order->save();

            // 添加订单（子表 order_course表
            //获取收货地址
            $orderCourse = new OrderCourse;
            $orderCourse->order_id = $order->id;
            $orderCourse->package_flg = $request->input('package_flg');
            $orderCourse->user_city = $user->city;
            $orderCourse->report_flg = 1;
            $orderCourse->save();

            // 课程参加人数加1
            $course->participate_num += 1;
            $course->save();

            //实际支付金额为零
            if ($last_price == 0) {
                //通知
                Event::fire(new OrderPaid($order));
                DB::commit();
                return redirect(route('course.detail', ['id' => $id]));
            }
            DB::commit();
            return redirect(route('wechat.course_pay') . '?id=' . $order->id);
        } catch (\EasyWeChat\Core\Exceptions\HttpException $e) {
            DB::rollBack();
            abort(403, '无法推送报道二维码,请在公众号上点击自定义菜单进行操作!');
        } catch (\Exception $e) {
            DB::rollBack();
            abort(403, $e->getMessage());
        }

    }

    // 优惠券
    public function coupon(Request $request, $id)
    {
        $id = intval($id);

        $package_flg = $request->input('package_flg');
        $package_prices = $request->input('package_prices');
        $number = $request->input('number');
        $coupon = $request->input('coupon');
        $is_point = $request->input('is_point');
        $usable_point = $request->input('usable_point');
        $usable_money = $request->input('usable_money');
        $is_balance = $request->input('is_balance');
        $usable_balance = $request->input('usable_balance');

        $course = Course::find($id);
        if ($course == null) {
            echo "<script>alert('不存在该课程！');history.go(-1);</script>";
            exit;
        }

        $user_id = session('user_info')['id'];

        //计算总金额，判断是否满足 满减
        $total_price = $package_prices * $number;

        // 可用优惠券
        $coupons_usable = $this->getCouponsUsable($user_id, $course);
        $couponusers_usable = [];
        // 不可用优惠券
        $couponusers_unusable = [ ];
        if ($coupons_usable) foreach ($coupons_usable as $coupon) {
            if ($coupon->full_money > $total_price) {
                $couponusers_unusable[] = $coupon;
            }
            else {
                $couponusers_usable[] = $coupon;
            }
        }

        $coupon_use_scope = config('constants.coupon_use_scope');
        // dd($couponusers_usable);

        return view('course.coupon', compact('couponusers_usable', 'couponusers_unusable', 'course', 'user_id', 'package_flg', 'package_prices', 'number', 'is_point', 'usable_point', 'usable_money', 'is_balance', 'usable_balance', 'coupon_use_scope', 'total_price'));

    }


    // 微信支付
    public function wechat_pay($id)
    {
        $id = intval($id);
        $order = Order::find($id);
        if ($order == null) {
            echo "<script>alert('不存在该订单！');history.go(-1);</script>";
            exit;
        }

        return view('course.pay_way', ['order' => $order]);
    }

    // 线下支付
    public function line_pay($id)
    {
        $id = intval($id);
        $order = Order::where('user_id', session('user_info')['id'])->where('pay_type', 1)->where('order_type', '1')->find($id);
        if ($order == null) {
            echo "<script>alert('不存在该订单！');history.go(-1);</script>";
            exit;
        }

        return view('course.line_pay', ['order' => $order]);
    }

    // 线下支付
    public function line_pay_static()
    {
        return view('course.line_pay_static');
    }

    // 线下支付
    public function do_line_pay(Request $request)
    {
        if ($request->isMethod('post')) {
            $user_info = session('user_info');
            $id = $request->input('id');
            //订单详情  
            $order = Order::where('user_id', session('user_info')['id'])->where('pay_type', 1)->where('order_type', '1')->find($id);

            if (!empty($user_info['id']) && $order) {
                $order->pay_method = 2;
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

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 显示线下核销的二维码
     */
    public function qrcode($id)
    {
        $order = Order::where('user_id', session('user_info')['id'])->find($id);
        if ($order == null)
            abort(404, '课程订单查询失败');

        //生成二维码
        $qrcodeImage = public_path('uploads/qrcodes/qrcode_' . $order->order_code . '.png');
        if (!file_exists($qrcodeImage)) {
            $url = route('course.course_report', ['id' => $order->id]);
            if (!file_exists(public_path('uploads/qrcodes')))
                mkdir(public_path('uploads/qrcodes'));
            QrCode::format('png')->size(200)->merge('/public/images/hrwq.jpg', .15)->generate($url, $qrcodeImage);
        }
        $qrcodeUrl = url('uploads/qrcodes/qrcode_' . $order->order_code . '.png');
        return view('course.qrcode', ['order' => $order, 'qrcodeUrl' => $qrcodeUrl]);
    }

    // 线下报道
    public function course_report($id)
    {
        $id = intval($id);
        $order = Order::where('pay_type', '1')
            ->whereIn('order_type', ['2', '3', '4'])
            ->with('course.area', 'order_course', 'user')
            ->find($id);
        if ($order->order_type == '3') {
            abort(403, '该订单已取消!');
        }
        if (!$order) {
            abort(403, '没有符合要求的订单');
        }
        $orderCourse = OrderCourse::where('order_id', $id)->first();
        if ($order->order_type == '4') {
            abort(403, '该订单已签到，请不要重复核销');
        }

        $openid = session('wechat_user.openid');
        $reportAdmin = CourseReportAdmin::whereOpenid($openid)->whereCourseId($order->pay_id)->first();
        if ($reportAdmin) {
            //验证管理员通过
            DB::beginTransaction();
            $order->order_type = '4';
            $orderCourse->report_flg = '2';
            $orderCourse->report_time = date('Y-m-d H:i:s');
            $orderCourse->report_admin = $openid;
            if ($order->save() && $orderCourse->save()) {
                $user_id = $order->user_id;
                get_score(14,0,$user_id);
                //好友首次购买爱中管教根基课 推荐人获得红包
                $user_info = User::find($user_id);
                $couponRule = CouponRule::where('rule_id', '1')->first();
                if ($user_info->invite_id > 0 && $order->course->agency_id == $couponRule->agency_id) {
                    $order_cnt = Order::where('pay_type', '1')->where('user_id',$user_id)
                                 ->with('course.area')
                                 ->whereIn('order_type', ['2', '4'])
                                 ->whereHas('course', function ($query ) {
                                    $query->where('agency_id',1);
                                 })->count();
                    if ($order_cnt=='1') {
                        //首次购买成功
                        $bouns = $couponRule->bouns;
                        if ($bouns>0) {
                            // 相应的减少余额(user表的当前余额减少,user_balance记录减少)
                            $user_invite = User::find($user_info->invite_id);
                            if ($user_invite) {
                                $user_invite->increment('current_balance', $bouns);  //总收益 & 余额 ++
                                $user_invite->increment('balance', $bouns);

                                $userbalance = new UserBalance;
                                $userbalance->user_id = $user_info->invite_id;
                                $userbalance->amount = $bouns;
                                $userbalance->operate_type = 1;
                                $userbalance->source = 1;
                                $userbalance->remark = "邀请会员:".$user_info->nickname;
                                $userbalance->save();
                            }
                        }
                    }
                }
                DB::commit();
                return view('course.course_report', ['order' => $order, 'orderCourse' => $orderCourse]);
            } else {
                DB::rollBack();
                abort(403, '课程报到失败');
            }
        } else {
            return view('course.course_report_login', ['order' => $order]);
        }
    }

    // 激活课程报到权限
    public function course_report_login(Request $request)
    {
        if ($request->isMethod('post')) {
            $openid = session('wechat_user.openid');
            $id = $request->input('order_id');
            $verify_password = $request->input('verify_password');
            //订单详情  
            $order = Order::where('pay_type', 1)->where('order_type', '2')->find($id);
            $courseInfo = Course::where('verify_password', $verify_password)->find($order->pay_id);
            if (!$verify_password) {
                return response()->json(['status' => false, 'msg' => '验证密码不能为空!']);
            }
            if (!$courseInfo) {
                return response()->json(['status' => false, 'msg' => '验证密码错误,权限激活失败!']);
            }
            $courseReportAdmin = new CourseReportAdmin;
            $courseReportAdmin->openid = $openid;
            $courseReportAdmin->course_id = $order->pay_id;
            if ($courseReportAdmin->save()) {
                return response()->json(['status' => true, 'msg' => '权限激活成功!']);
            } else {
                return response()->json(['status' => false, 'msg' => '权限激活失败']);
            }
        }
    }

    private function _send_coupon($coupon_id, $user_id, $come_from, $come_from_id)
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
            $couponUser->come_from_id = $come_from_id;
            $couponUser->save();
        }
    }

    private function getCouponsUsable($user_id, $course)
    {
        $coupons_usable = [ ];

        //初步符合要求的优惠券数量
        $couponusers_usable = CouponUser::leftJoin('coupon', 'coupon.id', '=', 'coupon_user.coupon_id')
            ->where('coupon_user.user_id', $user_id)
            ->where('coupon_user.is_used', 2)
            ->where('coupon_user.expire_at', '>=', date('Y-m-d'))
            ->get();
        // 将不满足适用范围的优惠券和没到使用时间的优惠券注销
        foreach ($couponusers_usable as $key => $value) {
            if ($value->available_period_type == 2 && $value->available_start_time > date('Y-m-d')) {
                continue;
            }
            if ($value->use_scope == 1 || $value->use_scope == 2) {
                $coupons_usable[] = $value;
                continue;
            }
            // 该课程的类型id
            $agency_id = $course->agency_id;
            // 该课程的id

            // 好课课程类别数组或者课程ID数组
            $arr_scope_val = explode(',', $value->use_scope_val);

            if ($value->use_scope == 6 && in_array($agency_id, $arr_scope_val)) {
                $coupons_usable[] = $value;
                continue;
            }
            if ($value->use_scope == 7 && in_array($course->id, $arr_scope_val)) {
                $coupons_usable[] = $value;
                continue;
            }
        }
        return $coupons_usable;
    }
}
