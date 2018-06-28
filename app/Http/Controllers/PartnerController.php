<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Area;
use App\Models\Article;
use App\Models\UserPartnerApply;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PartnerController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 合伙人申请页
     */
    public function apply()
    {
        $article = Article::whereType(9)->first();
        $article->content = replace_content_image_url($article->content);

        return view('partner.apply', ['article' => $article]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 合伙人完善资料
     */
    public function complete()
    {
        $userInfo = user_info();

        if ($userInfo['role'] != 3)
            abort(403, '不是合伙人，无法完善合伙人资料！');
        $userPartnerApply = UserPartnerApply::where('user_id', $userInfo['id'])->where('progress', 1)->orderBy('id', 'desc')->first();
        if ($userPartnerApply != null)
            return view('partner.progress', ['userInfo' => $userInfo, 'userPartnerApply' => $userPartnerApply]);
        if ($userInfo['partner_city']) {
            $areaInfo = Area::where('area_id', $userInfo['partner_city'])->first();
            $userInfo['partner_city_parent'] = $areaInfo->area_parent_id;
        }
        return view('partner.complete', ['userInfo' => $userInfo]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * 保存合伙人资料
     */
    public function partnerSave()
    {
        $user = User::with('c_province', 'c_city', 'c_district')->find(session('user_info')['id']);
        if ($user->role != 3)
            return response()->json(['code' => 2, 'message' => '不是合伙人，无法完善合伙人资料！']);

        $this->validate(request(), [
            'realname' => 'required',
            'sex' => 'required',
            'email' => 'required',
            'address' => 'required',
            'province' => 'required',
            'city' => 'required',
        ], [], [
            'realname' => '真实姓名',
            'sex' => '性别',
            'email' => '邮箱',
            'address' => '通讯地址',
            'province' => '期望城市',
            'city' => '期望城市',
        ]);

        $userPartnerApply = new UserPartnerApply();
        $userPartnerApply->user_id = $user->id;
        $userPartnerApply->realname = request('realname');
        $userPartnerApply->sex = request('sex');
        $userPartnerApply->email = request('email');
        $userPartnerApply->address = request('address');
        $userPartnerApply->city = request('city');
        $userPartnerApply->progress = 1;
        if ($userPartnerApply->save())
            return response()->json(['code' => 0, 'message' => '合伙人资料提交成功！']);
        return response()->json(['code' => 1, 'message' => '合伙人资料提交失败！']);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * 合伙人城市检查
     */
    public function city_check()
    {
        return response()->json(['code' => 1]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 合伙人区域订单
     */
    public function orders()
    {
        $userId = session('user_info')['id'];
        $userInfo = User::where('role', '3')->where('partner_city', '>', '0')->find($userId);
        if (!$userInfo) {
            abort(403, '非合伙人无法查看合伙人区域订单!');
        }
        //将合伙人新订单 flag 置2
        $user = $userInfo;
        Order::where('partner_flg', 1)->where(function ($query) use ($user) {
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
        })->update(['partner_flg' => 2]);

        //查询出合伙人订单
        $orders = Order::with([
            'course', 'user', 'order_course'
        ])
//            ->whereHas('course', function ($query) use ($userInfo) {
//                $query->where('promoter', $userInfo->id)
//                    ->where('head_flg', 2);
//
//            })
//            ->orWhere(function ($query) use ($userInfo) {
//                $query->whereHas('course', function ($query) use ($userInfo) {
//                    $query->where('head_flg', 1)
//                        ->where('distribution_flg', 1);
//                })
//                ->whereHas('order_course', function ($query) use ($userInfo) {
//                    $query->where('user_city', $userInfo->partner_city);
//                 });
//            })

            ->whereHas('user', function($query) use ($userInfo){
                $query->where('lover_id', $userInfo->id);
            })
            ->whereIn('pay_type', ['1'])
            ->whereIn('order_type', ['1', '2', '4'])
            ->orderBy('id', 'desc')
            ->get();
        $order_type = config('constants.order_type');
        return view('partner.orders', ['orders' => $orders, 'order_type' => $order_type]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * 运营数据
     */
    public function operate()
    {
        $user = User::find(session('user_info')['id']);
        if($user==null)
            abort(403, '合伙人信息查询失败');

        //当前年份
        $cur_year = date('Y');
        //当前月份
        $cur_month = date('m');
        //累计注册用户
        $userAllCnt = User::where('city', $user->partner_city)->count();
        $yesterdayf = date("Y-m-d 00:00:00", strtotime("-1 day"));
        $yesterdayt = date("Y-m-d 23:59:59", strtotime("-1 day"));
        $userYesterdayCnt = User::whereBetween('created_at', [$yesterdayf, $yesterdayt])
            ->where('city', $user->partner_city)->count();
        //昨日新增用户
        return view('partner.operate', compact('userAllCnt', 'userYesterdayCnt', 'cur_year', 'cur_month'));
    }

    /**
     * 按年份统计用户
     *
     */
    public function user(Request $request)
    {
        $select_s_year = $request->input('select_s_year');
        $select_s_month = $request->input('select_s_month');

        //月份为0,按年份搜索
        if ($select_s_month == 0) {
            $this->_stat_user_by_year();
        }
        $this->_stat_user_by_month();
    }

    /**
     * 最近七天
     */
    public function day7()
    {
        $barData = $tick = $message = [];
        $day_arr = [];
        for ($i = 7; $i > 0; $i--) {
            $day_arr[] = date('Y-m-d', strtotime('-' . $i . ' day'));
        }

        $day_num_arr = $this->_stat_month_user_add($day_arr);

        foreach ($day_arr as $k => $day) {
            $barData[0]['data'][] = [$k + 1, isset($day_num_arr[$day]) ? $day_num_arr[$day] : 0];
            //todo 用户列表 链接待完善
            $barData[0]['url'] = '';
            $tick[$k] = [$k+1, substr($day, 5)];
        }

        $message['code'] = 0;
        $message['content'] = $barData;
        $message['tick'] = $tick;
        $message['days'] = $day_arr;
        die(json_encode($message, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 最近一年的数据（不包括当月）
     */
    private function _stat_user_by_year()
    {
        $barData = $tick = $message = [];
        $month_num_arr = $this->_stat_year_user_add();

        $startTime = Carbon::now()->subMonths(13);
        for ($i = 0; $i < 12; $i++) {
            $i_month = $startTime->addMonth()->format('Y-m');

            $barData[0]['data'][] = [$i + 1, isset($month_num_arr[$i_month]) ? $month_num_arr[$i_month] : 0];
            //todo 用户列表 链接待完善
            if ($i%3==0)
                $tick[] = [$i + 1, $i_month];
        }
        $barData[0]['url'] = '';

        $message['code'] = 0;
        $message['content'] = $barData;
        $message['tick'] = $tick;
        die(json_encode($message, JSON_UNESCAPED_UNICODE));
    }


    /**
     * 最近一个月统计用户
     *
     */
    private function _stat_user_by_month()
    {
        $barData = $tick = $message = [];

        $day_arr = [];
        for ($i = 30; $i > 0; $i--) {
            $day_arr[] = date('Y-m-d', strtotime('-' . $i . ' day'));
        }

        $day_num_arr = $this->_stat_month_user_add($day_arr);

        foreach ($day_arr as $k => $day) {
            $barData[0]['data'][] = [$k + 1, isset($day_num_arr[$day]) ? $day_num_arr[$day] : 0];
            //todo 用户列表 链接待完善
            $barData[$k]['url'] = '';
            if ($k%4==0)
                $tick[] = [$k + 1, substr($day, 5)];
        }

        $message['code'] = 0;
        $message['content'] = $barData;
        $message['tick'] = $tick;
        die(json_encode($message, JSON_UNESCAPED_UNICODE));
    }


    private function _stat_year_user_add()
    {
        $user = User::find(session('user_info')['id']);
        if($user==null)
            return [];

        $endTime = Carbon::now()->subMonth()->endOfMonth();
        $startTime = Carbon::now()->subMonth(13)->startOfMonth();

        $day_num_arr = [];
        $user_add = User::withTrashed()->whereBetween('created_at', [(string)$startTime, (string)$endTime])->where('city', $user->partner_city)->lists('created_at')->toArray();

        foreach ($user_add as $v) {
            $tmp = explode('-', $v);
            $day_key = $tmp[0] . '-' . $tmp[1];

            $day_num_arr[$day_key] = isset($day_num_arr[$day_key]) ? $day_num_arr[$day_key] + 1 : 1;
        }

        return $day_num_arr;
    }


    /**
     * 统计当月所有注册用户 数组分组  日期=》注册次数
     *
     * @param $day_arr
     * @return array
     */
    private function _stat_month_user_add($day_arr)
    {
        $user = User::find(session('user_info')['id']);
        if($user==null)
            return [];

        $day_num_arr = [];
        $month_user_add = User::withTrashed()->whereBetween('created_at', [$day_arr[0] . ' 00:00:00', end($day_arr) . ' 23:59:59'])->where('city', $user->partner_city)->lists('created_at')->toArray();

        foreach ($month_user_add as $v) {
            $day_key = explode(' ', $v)[0];
            $day_num_arr[$day_key] = isset($day_num_arr[$day_key]) ? $day_num_arr[$day_key] + 1 : 1;
        }

        return $day_num_arr;
    }
}
