<?php
/**
 * 和会员业务
 */
namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Models\OrderVip;
use App\Models\UserReceiptAddress;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Vip;
use App\Models\User;
use App\Models\Config;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\CouponUser;
use App\Models\UserBalance;
use App\Models\UserPoint;
use App\Models\UserPointVip;
use DB,Event,Wechat;

class VipController extends Controller
{
    public function index(Request $request)
    {
        $user_id = user_info('id');
        //是否已生成订单
        $order = Order::where('user_id',$user_id)->where('pay_type',6)->where('order_type',1)->first();
        $session_mobile = '';
        if (isset(session('user_info')['mobile'])) {
            $session_mobile = session('user_info')['mobile'];
        }
        $requestUri = $request->getRequestUri();
        return view('vip.index',['data'=>user_info(),'order'=>$order, 'session_mobile' => $session_mobile, 'requestUri' => $requestUri,'wx_js' => Wechat::js()]);
    }

    /**
     * 开通会员-购买
     */
    public function buy(Request $request)
    {
        //$this->_vip_abort();

        $temp = $request->input('temp');//优惠券点击
        $address_id = $request->input('address_id');// 收货地址
        $coupon_user_id = $request->input('coupon_user_id');// 用户优惠券id
        $coupon_id = $request->input('coupon_id');// 优惠券id
        $coupon_type = $request->input('coupon_type');// 优惠券类型
        $coupon_cutmoney = $request->input('coupon_cutmoney');// 优惠券减免
        $coupon_discount = $request->input('coupon_discount');// 优惠券折扣

        $is_point = $request->input('is_point');// 积分开关
        $usable_point = $request->input('usable_point');// 可用积分
        $usable_money = $request->input('usable_money');// 积分可抵用现金
        $is_balance = $request->input('is_balance');// 可用余额开关
        $usable_balance = $request->input('usable_balance');// 可用余额
        $total_price = $request->input('total_price') ? : Config::first()->pluck('vip_price');

        // 当前用户
        $user_id = session('user_info')['id'];
        $user = User::find($user_id);

        // 获取默认收货地址
        $user_receipt_address = UserReceiptAddress::where('user_id', $user_id)->where('default', 2)->first();
        // 收货地址
        if ($address_id) {
            $user_receipt_address = UserReceiptAddress::find($address_id);
        }

        // 优惠券
        $coupon_name ='';
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
                if ($total_price<$user->current_balance) {
                    $usable_balance = $total_price;
                }else{
                    $usable_balance = $user->current_balance;
                }
            }
            if ($coupon_type == 2) {
                $total_price = $total_price*$coupon_discount/100;
                if ($total_price<$user->current_balance) {
                    $usable_balance = $total_price;
                }else{
                    $usable_balance = $user->current_balance;
                }
            }

            if ($temp == 2) {
                $is_point = 0;
                $is_balance = 0;
            }
        }
        return view('vip.buy',compact('user','vip_price','usable_balance','coupon_name','coupon_user_id','is_point','usable_point','usable_money','is_balance','coupon_id','coupon_type','coupon_cutmoney','coupon_discount','total_price', 'user_receipt_address'));
    }

    /**
     * 开通会员-购买-提交  生成订单
     */
    public function create_order(Request $request)
    {
    	$user_id = user_info('id');
    	//是否已生成订单
    	$order = Order::where('user_id',$user_id)->where('pay_type',6)->where('order_type',1)->first();

    	if (!$order) {
    		DB::beginTransaction();
    		try {
    			// 添加订单（主表 order表）
    			$order = new Order;
    			$order->order_code = get_order_code(6);
    			$order->user_id = $user_id;
    			$order->pay_type = 6;//1好课 2好看 3壹家壹 4好问提问 5好问偷听 6和会员
    			$order->order_type = 1;
    			$order->order_name = '一年期和会员';
    			$order->free_flg = 2;
    			$last_price = $order->total_price =  Config::first()->pluck('vip_price');// 任何减免之前的价格
                        
                        // 优惠券
                        $coupon_id = $request->input('coupon_id');
                        if ($coupon_id) {
                            $couponuser = CouponUser::where('coupon_id',$coupon_id)
                                ->where('user_id',$user_id )
                                ->where('is_used',2)
                                ->whereNull('used_at')
                                ->where('expire_at','>',date('Y-m-d'))
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

                        if ($is_point&&$usable_point>0&&($order->total_price/2)>=$usable_money&&($usable_money*100)==$usable_point&&$usable_point<=$user->score) {

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
                        if ($is_balance&&$usable_balance<=$user->current_balance&&$usable_balance>0) {
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
    				$order->pay_time = date('Y-m-d H:i:s');
    			}
    			$order->price = $last_price;
    			$order->free_flg = 2;
    			$order->save();
    
    			//添加 vip 订单附加信息 （order_vip表
    			$orderVip = new OrderVip();
    			$orderVip->order_id = $order->id;
    			$orderVip->consignee = '';
    			$orderVip->consignee_tel = '';
    			$orderVip->consignee_address = '';
    			$orderVip->delivery_flg = 1;
    			$orderVip->save();
    			//实际支付金额为零
    			if ($last_price == 0) {
    				//vip flag
    				Event::fire(new OrderPaid($order));
    				User::find($user_id)->update(['vip_flg'=>2]);
    				DB::commit();
    				return redirect(route('user'));
    			}
    			DB::commit();
    			return redirect(route('wechat.vip_pay'));
    		}catch (\Exception $e) {
    			DB::rollBack();
    			abort(403,$e->getMessage());
    		}
    	}
        else{
    		return redirect(route('wechat.vip_pay'));
    	}
    }

    // 更换收货地址  页面
    public function receipt_address(Request $request)
    {
        $coupon_id = $request->input('coupon_id');
        $coupon_type = $request->input('coupon_type');
        $coupon_cutmoney = $request->input('coupon_cutmoney');
        $coupon_discount = $request->input('coupon_discount');

        $is_point = $request->input('is_point');
        $usable_point = $request->input('usable_point');
        $usable_money = $request->input('usable_money');
        $is_balance = $request->input('is_balance');
        $usable_balance = $request->input('usable_balance');
        $total_price = $request->input('total_price');

        $user_id = session('user_info')['id'];

        $user_receipt_address = $this->address_list();

        return view('vip.receipt_address', [
            'user_id' => $user_id,
            'user_receipt_address' => $user_receipt_address,
            'coupon_id' => $coupon_id,
            'coupon_type' => $coupon_type,
            'coupon_cutmoney' => $coupon_cutmoney,
            'coupon_discount' => $coupon_discount,
            'is_point' => $is_point,
            'usable_point' => $usable_point,
            'usable_money' => $usable_money,
            'is_balance' => $is_balance,
            'usable_balance' => $usable_balance,
            'total_price' => $total_price
        ]);
    }


    // 新增收货地址
    public function add_address(Request $request)
    {
        // 判断是否有收货地址
        $user_id = session('user_info')['id'];
        $has_receipt_address = UserReceiptAddress::where('user_id', $user_id)->first();

        $user_receipt_address = new UserReceiptAddress;
        $user_receipt_address->user_id = $user_id;
        $user_receipt_address->name = $request->input('name');
        $user_receipt_address->phone = $request->input('phone');
        $user_receipt_address->region = $request->input('region');
        $user_receipt_address->address = $request->input('address');
        // 默认第一个添加的收货地址是默认地址
        if ($has_receipt_address) {
            $user_receipt_address->default = 1;
        } else {
            $user_receipt_address->default = 2;
        }
        if ($user_receipt_address->save()) {
            $address = $this->address_list();
            return response()->json(['code' => 0, 'message' => '添加收货地址成功!', 'address' => $address]);
        } else {
            return response()->json(['code' => 1, 'message' => '添加收货地址失败!']);
        }

    }

    // 编辑收货地址
    public function edit_address(Request $request)
    {
        // 判断是否有该收货地址
        $user_id = session('user_info')['id'];
        $id = $request->input('id');
        $user_receipt_address = UserReceiptAddress::where('user_id', $user_id)->find($id);
        if ($user_receipt_address == null) {
            return response()->json(['code' => 1, 'message' => '不存在该收货地址!']);
        }

        $user_receipt_address->name = $request->input('name');
        $user_receipt_address->phone = $request->input('phone');
        $user_receipt_address->region = $request->input('region');
        $user_receipt_address->address = $request->input('address');

        if ($user_receipt_address->save()) {
            $address = $this->address_list();
            return response()->json(['code' => 0, 'message' => '添加收货地址成功!', 'address' => $address]);
        } else {
            return response()->json(['code' => 1, 'message' => '添加收货地址失败!']);
        }

    }

    /** 设为默认收货地址 */
    public function set_default(Request $request)
    {
        $user_id = session('user_info')['id'];
        $id = $request->input('id');
        $user_receipt_address = UserReceiptAddress::where('user_id', $user_id)->find($id);
        if ($user_receipt_address == null) {
            return response()->json(['code' => 1, 'message' => '不存在该收货地址!']);
        }

        // 1、将该地址设置为默认
        $user_receipt_address->default = 2;
        // 2、除这个之外的地址设置成不默认
        $update = DB::table('user_receipt_address')->where('user_id', $user_receipt_address->user_id)->whereNotIn('id', [$id])->update(['default' => 1]);

        if ($user_receipt_address->save() && $update) {
            $address = $this->address_list();
            return response()->json(['code' => 0, 'message' => '设置默认地址成功!', 'address' => $address]);
        } else {
            return response()->json(['code' => 1, 'message' => '设置默认地址失败!']);
        }
    }


    /** 删除收货地址 */
    public function delete(Request $request)
    {
        $user_id = session('user_info')['id'];
        $id = $request->input('id');
        $user_receipt_address = UserReceiptAddress::where('user_id', $user_id)->find($id);
        if ($user_receipt_address == null) {
            return response()->json(['code' => 1, 'message' => '不存在该收货地址!']);
        }

        if ($user_receipt_address->default == 2) {
            return response()->json(['code' => 1, 'message' => '默认收货地址不可删除!']);
        }

        if ($user_receipt_address->delete()) {
            $address = $this->address_list();
            return response()->json(['code' => 0, 'message' => '删除成功!', 'address' => $address]);
        } else {
            return response()->json(['code' => 1, 'message' => '删除失败!']);
        }

    }

    //收货地址列表
    public function address_list()
    {
        $user_id = session('user_info')['id'];
        $user_receipt_address = UserReceiptAddress::select('id', 'name', 'phone', 'region', 'address', 'default')->where('user_id', $user_id)->get();
        foreach ($user_receipt_address as &$value) {
            if ($value['default'] == 2) {
                $value['default'] = true;
            } else {
                $value['default'] = false;
            }
        }
        $data = json_encode($user_receipt_address);
        return $data;
    }

    // 优惠券
    public function coupon(Request $request)
    {
        $address_id = $request->input('address_id');
        $is_point = $request->input('is_point');
        $usable_point = $request->input('usable_point');
        $usable_money = $request->input('usable_money');
        $is_balance = $request->input('is_balance');
        $usable_balance = $request->input('usable_balance');

        $total_price = Config::first()->pluck('vip_price');

        $user_id = session('user_info')['id'];
        // 可用优惠券
        $builder = CouponUser::select('coupon_user.id','coupon_user.coupon_id','coupon_user.created_at','coupon_user.expire_at','coupon.name','coupon.type','coupon.full_money','coupon.cut_money',
            'coupon.discount','coupon.use_scope','coupon.use_scope_val','coupon.available_period_type','coupon.available_start_time','coupon.available_end_time')
            ->leftJoin('coupon','coupon.id','=','coupon_user.coupon_id')
            ->where('coupon_user.user_id',$user_id)
            ->where('coupon_user.is_used',2)
            ->where('coupon_user.expire_at','>=',date('Y-m-d'));
        $builder_b = clone $builder;
        $builder_c = clone $builder;

        $couponusers_usable = $builder_b->where('coupon.full_money','<=',$total_price)->get();

        // 将不满足适用范围的优惠券和没到使用时间的优惠券注销
        foreach ($couponusers_usable as $key => $value) {
            if ($value->use_scope != 1) {
                unset($couponusers_usable[$key]);
            }
//            if ($value->available_period_type == 2 && $value->available_start_time < date('Y-m-d')) {
//                unset($couponusers_usable[$key]);
//            }
        }

        // 不可用优惠券
        $couponusers_unusable = $builder_c->where('coupon.full_money','>',$total_price)->get();
        // 将不满足适用范围的优惠券和没到使用时间的优惠券注销
        foreach ($couponusers_unusable as $key => $value) {
            if ($value->available_period_type == 2 && $value->available_start_time < date('Y-m-d')) {
                unset($couponusers_unusable[$key]);
            }
        }

        $coupon_use_scope = config('constants.coupon_use_scope');
        return view('vip.coupon',compact('couponusers_usable','couponusers_unusable','user_id', 'address_id', 'is_point','usable_point','usable_money','is_balance','usable_balance','coupon_use_scope','total_price'));
    }




    /**
     * 激活会员卡
     */
    public function active()
    {
        //$this->_vip_abort();
        return view('vip.active');
    }

    /**
     * 激活会员卡-提交
     */
    public function active_store(Request $request)
    {
        $code = trim($request->input('code'));
        if(!$code) {
            return response()->json(['code' => 4, 'message' => '卡号不能为空!']);
        }

        $data = Vip::where('code',$code)->first();

        if(!$data) {
            return response()->json(['code' => 1, 'message' => '无效的卡号!']);
        } elseif ($data->is_activated == 2) {
            return response()->json(['code' => 2, 'message' => '此卡号已被激活!']);
        } else {
            $update = [];
            $update['is_activated']  = 2;
            $update['activated_vip'] = user_info('id');

            $user = User::find(user_info('id'));
            $days = 365;
            $left_days = get_new_vip_left_day($user->vip_left_day, $days);
            UserPointVip::add($user->id, $days, 2);
 
            $user_update = [];
            $user_update['vip_flg'] = 2;
            $user_update['vip_code'] = $code;
            $user_update['vip_left_day'] = $left_days;
            
            //长期和会员
            if(in_array($code, config('constants.forever_vip_code'))){
                $user_update['vip_forever'] = 2;
            }

            DB::beginTransaction();
            try {
                ///更新卡号状态
                $data->update($update);
                //更新vip标识
                $user->update($user_update);

                DB::commit();
                return response()->json(['code' => 0, 'message' => '激活成功!','mobile'=>$user['mobile']]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['code' => 3, 'message' => '激活失败!']);
            }
        }
    }


    /**
     * 我的和会员天数记录
     */
    public function records()
    {
    	$data = User::with([
    			'user_point_vip' => function ($query) {
    				$query->orderBy('id', 'desc');
    			}
    	])->find(session('user_info')['id']);
    
    	return view('vip.records', ['data' => $data]);
    }
    
    /**
     * 禁止和会员操作
     */
    private function _vip_abort()
    {
        if(user_info('vip_flg') == 2) {
            abort('403','当前已是和会员身份');
        }
    }
   

}
