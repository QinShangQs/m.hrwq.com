<?php
/**
 * 1 => '服务协议',
 * 2 => '关于我们',
 * 3 => '积分规则',
 * 4 => '成长值介绍',
 * 5 => '会员等级介绍',
 * 6 => '和会员介绍',
 * 7 => '帮助中心',
 * 8 => '收益介绍'
 */
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Article;

class ArticleController extends Controller
{
    /**
     * @param $id  类型id   见上述描述
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request,$id)
    {
        $article = Article::whereType($id)->first();
        $article->content = replace_content_image_url($article->content);
        $session_mobile = '';
        if (isset(session('user_info')['mobile'])) {
            $session_mobile = session('user_info')['mobile'];
        }
        $requestUri = $request->getRequestUri();
        //是否已生成订单
        $order = Order::where('user_id',session('user_info')['id'])->where('pay_type',6)->where('order_type',1)->first();
        
        // 当前用户
        $user_id = session('user_info')['id'];
        $user = User::find($user_id);
        
        return view('article_common', ['article' => $article, 'session_mobile' => $session_mobile, 'requestUri' => $requestUri,'order'=>$order,'user'=>$user]);
    }

    public function helpcenter($type)
    {
        $data = Article::where('type', $type)->get();

        return view('helpcenter.index', ['data' => $data, 'type_id' => $type]);
    }

    public function helpcenterdetail($id)
    {
        $data = Article::where('id', $id)->first();
        $data->content = replace_content_image_url($data->content);

        return view('helpcenter.detail', ['data' => $data, 'id' => $id]);
    }

}
