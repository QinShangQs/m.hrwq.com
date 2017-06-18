<?php

namespace App\Http\Controllers;

use App\Models\LeaveWord;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
/**
 * 建议留言
 */
class LeaveWordController extends Controller {
	public function index() {
		return view ( 'leaveword.index' );
	}
	
	/**
	 * 添加留言
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function create() {
		$userId = session ( 'user_info' )['id'];
		$content = request ( 'content', '' );
		if (LeaveWord::add ( $userId, $content )) {
			return response ()->json ( [ 
					'code' => 0,
					'message' => '建议已成功提交\n感谢您对中国家庭教育事业的关注!'
			] );
		} else {
			return response ()->json ( [ 
					'code' => 1,
					'message' => '留言失败!' 
			] );
		}
	}
}