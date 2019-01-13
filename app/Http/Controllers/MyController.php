<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UserFavor;
use App\Models\UserReceiptAddress;
use App\Models\VcourseMark;
use App\Models\UserPoint;
use App\Models\Course;
use App\Models\UserBalance;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\CouponUser;
use App\Models\CouponRule;
use App\Models\OrderTeam;
use App\Http\Controllers\Controller;
use DB;
use Wechat;

class MyController extends Controller
{
    /**
     * 笔记
     */
    public function note()
    {
        $userId = session('user_info')['id'];
        $notes = VcourseMark::where('user_id', $userId)->where('mark_type', 1)->with('vcourse')->orderBy('id', 'desc')->get();
        $works = VcourseMark::where('user_id', $userId)->where('mark_type', 2)->with('vcourse')->orderBy('id', 'desc')->get();
        return view('my.note', ['notes' => $notes, 'works'=>$works]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * 删除笔记/作业
     */
    public function deleteNote()
    {
        $vcourseMark = VcourseMark::where('user_id', session('user_info')['id'])->find(intval(request('id')));
        if ($vcourseMark==null) {
            return response()->json(['code'=>1, 'message'=>'笔记/作业查找失败！']);
        }
        if($vcourseMark->delete())
            return response()->json(['code'=>0, 'message'=>'删除成功！']);
        return response()->json(['code'=>2, 'message'=>'删除失败！']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 我的课程&收藏
     */
    public function courses()
    {
        $userId = session('user_info')['id'];
        
        //收藏的课程
        $vcourses = UserFavor::where('user_id', $userId)
            ->where('favor_type', 2)
            ->with('vcourse');
        $favors = UserFavor::where('user_id', $userId)
            ->where('favor_type', 1)
            ->with('course')
            ->union($vcourses)
            ->orderBy('id', 'desc')->get();
            
        //参与的课程
//        $vcoursep = Vcourse::select('pay_type ', 'vcourse.status', 'vcourse.type', 'vcourse.id', 'vcourse.picture', 'vcourse.title', 'vcourse.price', 'vcourse.view_cnt',1,1)
//            ->leftJoin('order', 'order.pay_id', '=', 'vcourse.id')->where('user_id', $userId)
//            ->where('pay_type', 2)->whereIn('order_type', [2, 4]);
//        $participates = Course::select('pay_type ', 'course.status', 'course.type', 'course.id', 'course.picture', 'course.title', 'course.price', 1,'course.original_price' ,'course.participate_num' )
//            ->leftJoin('order', 'order.pay_id', '=', 'course.id')->where('order.user_id', $userId)
//            ->where('pay_type', 1)->whereIn('order_type', [2, 4])
//            ->union($vcoursep)->get();
        // $vcoursep = Order::distinct('pay_id')->where('user_id', $userId)
        //     ->where('pay_type', 2)->whereIn('order_type', [2, 4])
        //     ->with('vcourse');
            
        $participates = Order::groupBy('pay_type','pay_id')->where('user_id', $userId)
            ->whereIn('pay_type', ['1','2'])->whereIn('order_type', [2, 4])
            ->with('course','vcourse')
            ->orderBy('id', 'desc')->get();
        return view('my.courses', ['favors' => $favors, 'participates' => $participates]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 我的地址
     */
    public function addresses()
    {
        $addresses = $this->addressList()->toJson();
        return view('my.addresses', ['addresses' => $addresses]);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * 添加收货地址
     */
    public function addAddress()
    {
        // 判断是否有收货地址
        $userId = session('user_info')['id'];
        $hasAddress = UserReceiptAddress::where('user_id', $userId)->count();

        $newAddress = new UserReceiptAddress;
        $newAddress->user_id = $userId;
        $newAddress->name = request('name');
        $newAddress->phone = request('phone');
        $newAddress->region = request('region');
        $newAddress->address = request('address');
        // 默认第一个添加的收货地址是默认地址
        if ($hasAddress) {
            $newAddress->default = 1;
        } else {
            $newAddress->default = 2;
        }
        if ($newAddress->save()) {
            $addresses = $this->addressList();
            return response()->json(['code' => 0, 'message' => '添加收货地址成功!', 'data' => $addresses]);
        } else {
            return response()->json(['code' => 1, 'message' => '添加收货地址失败!']);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * 编辑收货地址
     */
    public function editAddress()
    {
        // 判断是否有该收货地址
        $userId = session('user_info')['id'];
        $id = request('id');
        $address = UserReceiptAddress::where('user_id', $userId)->where('id', $id)->first();
        if ($address == null) {
            return response()->json(['code' => 1, 'message' => '不存在该收货地址!']);
        }
        $address->name = request('name');
        $address->phone = request('phone');
        $address->region = request('region');
        $address->address = request('address');
        if ($address->save()) {
            $addresses = $this->addressList();
            return response()->json(['code' => 0, 'message' => '添加收货地址成功!', 'data' => $addresses]);
        } else {
            return response()->json(['code' => 2, 'message' => '添加收货地址失败!']);
        }

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * 删除地址
     */
    public function deleteAddress()
    {
        $userId = session('user_info')['id'];
        $address = UserReceiptAddress::where('user_id', $userId)->find(request('id'));
        if ($address == null) {
            return response()->json(['code' => 1, 'message' => '不存在该收货地址!']);
        }
        if ($address->default == 2) {
            return response()->json(['code' => 1, 'message' => '默认收货地址不可删除!']);
        }

        if ($address->delete()) {
            $addresses = $this->addressList();
            return response()->json(['code' => 0, 'message' => '删除成功!', 'data' => $addresses]);
        } else {
            return response()->json(['code' => 1, 'message' => '删除失败!']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * 设置默认地址
     */
    public function defaultAddress(Request $request)
    {
        $userId = session('user_info')['id'];
        $id = $request->input('id');
        $address = UserReceiptAddress::where('user_id', $userId)->find($request->input('id'));
        if ($address == null) {
            return response()->json(['code' => 1, 'message' => '不存在该收货地址!']);
        }

        DB::beginTransaction();
        try {

            UserReceiptAddress::where('user_id', $address->user_id)->update(['default'=>1]);
            $address->default = 2;
            $address->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['code' => 1, 'message' => '设置默认地址失败!']);
        }

        $addresses = $this->addressList();
        return response()->json(['code' => 0, 'message' => '设置默认地址成功!', 'data' => $addresses]);
    }

    /**
     * @return string
     *
     * 收货地址
     */
    private function addressList()
    {
        $addresses = UserReceiptAddress::where('user_id', session('user_info')['id'])->select(['id', 'name', 'phone', 'region', 'address', 'default'])->get();
        foreach ($addresses as $value) {
            if ($value['default'] == 2) {
                $value['default'] = true;
            } else {
                $value['default'] = false;
            }
        }
        return $addresses;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 我的订单
     */
    public function orders()
    {
        $userId = session('user_info')['id'];
        //将未看订单变成已看
        Order::where('user_id',$userId)->where('read_flg','1')->update(['read_flg' => 2]);
        $orders = Order::with('order_course', 'order_vip')->where('user_id',$userId)
                         ->whereIn('pay_type',['1','2','3','6'])
                         ->whereIn('order_type',['1','2','4'])
                         ->orderBy('id','desc')
                         ->get();
        $order_type = config('constants.order_type');
        return view('my.orders',['orders'=>$orders,'order_type'=>$order_type]);
    }

    /**
     * 组团成员
     * @param type $team_id
     */
    public function ordersMembers($team_id){
        return view('my.orders_members', ['team_members' => OrderTeam::findAllMembers($team_id)]);
    }
    
    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * 取消订单
     */
    public function orderCancel(Request $request)
    {
        $userId = session('user_info')['id'];
        $id = $request->input('id');
        $order = Order::where('user_id',$userId)
                         ->whereIn('pay_type',['1','2','3','6'])
                         ->whereIn('order_type',['1'])
                         ->find($id);
        if (!$order) {
            return response()->json(['code' => 1, 'message' => '取消订单失败']);
        }
        DB::beginTransaction();
        $order->order_type = 3;
        //退还积分
        if ($order->point_price>0) {
            $usable_point = $order->point_price*100;
            $user = User::find($userId);
            $user->score += $usable_point;
            $user->save();

            $userpoint = new UserPoint;
            $userpoint->user_id = $userId;
            $userpoint->point_value = $usable_point;
            $userpoint->source = 10;
            $userpoint->move_way = 1;
            $userpoint->save();
        }
        //退还余额
        if ($order->balance_price>0) {
            $user = User::find($userId);
            $user->current_balance += $order->balance_price;
            $user->save();

            $userbalance = new UserBalance;
            $userbalance->user_id = $userId;
            $userbalance->amount = $order->balance_price;
            $userbalance->operate_type = 1;
            $userbalance->source = 8;
            $userbalance->save();
        }
        //参与人数-1
        $course = Course::find($order->pay_id);
        if ($course) {
            $course->decrement('participate_num');
        }
        
        if ($order->save()) {
            DB::commit();
            return response()->json(['code' => 0, 'message' => '取消订单成功']);
        } else {
            DB::rollback();
            return response()->json(['code' => 1, 'message' => '取消订单失败']);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 推荐有奖
     */
    public function invite_user()
    {
        $userId = session('user_info')['id'];

        return view('my.invite_user', ['wx_js' => Wechat::js(),'user_id' => $userId]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 推荐有奖(接受方)
     */
    public function invited_user(Request $request)
    {
        if ($request->session()->get('user_info.mobile')) {
            return redirect('/');
        }
        if ($this->hasInvitedCoupon(session('user_info')['id'])) {
            return redirect()->route("user.login", [
                'invite_user'=>$request->input('invite_user')
            ]);
        }
        //推荐人
        $data['invite_user'] = $request->input('invite_user');
        return view('my.invited_user', $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * 被邀请注册的人获得红包
     */
    public function get_coupon(Request $request)
    {
        $user_id = session('user_info')['id'];
        $invite_user = $request->input('user_id');
        if ($invite_user) {
            if ($this->hasInvitedCoupon($user_id)) {
                return response()->json(['code' => 2, 'message' => '红包已领取!']);
            }

            $couponRule = CouponRule::where('rule_id', '1')->first();
            $coupon_ids = explode(',', $couponRule->coupon_id);
            foreach ($coupon_ids as $coupon_id) {
                $this->_send_coupon($coupon_id, $user_id, '1');
            }
            return response()->json(['code' => 0, 'message' => '领取红包成功!']);
        } else {
            return response()->json(['code' => 1, 'message' => '领取红包失败!']);
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


	private function hasInvitedCoupon($user_id)
	{
		return CouponUser::where('user_id', $user_id)->where('come_from',1)->count();
	}
}
