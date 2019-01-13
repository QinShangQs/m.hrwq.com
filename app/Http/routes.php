<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//音频转码完成异步通知  不可加入微信权限验证
Route::group(['prefix' => 'user'], function () {
    Route::post('av_thumb_mp3_notify', 'UserController@av_thumb_mp3_notify')->name('user.av_thumb_mp3_notify');
});

Route::group(['prefix' => 'wechat'], function () {
    Route::any('/auth', 'WechatController@auth')->name('wechat.auth');
    Route::any('/notify', 'WechatController@notify')->name('wechat.notify');
    Route::post('/status', 'WechatController@status')->name('wechat.status');
    Route::post('/question_listen_pay', 'WechatController@question_listen_pay')->name('wechat.question_listen_pay');
    Route::post('/question_ask_pay', 'WechatController@question_ask_pay')->name('wechat.question_ask_pay');
    Route::get('/menu', 'WechatController@menu')->name('wechat.menu');
    Route::get('/material', 'WechatController@material')->name('wechat.material');
    Route::get('/access_token', 'WechatController@access_token')->name('wechat.access_token');
});

Route::group(['prefix' => 'wechat', 'middleware' => ['FrontWechat', 'FrontAuth']], function () {
    Route::get('/question', 'WechatController@question')->name('wechat.question');
    Route::get('/question_ask_confirm/{id?}', 'WechatController@question_ask_confirm')->name('wechat.question_ask_confirm');

});

Route::group(['prefix' => 'wechat'], function () {
    Route::get('/qrcode', 'WechatController@qrcode')->name('wechat.qrcode');
});

Route::group(['prefix' => 'home'], function () {
    Route::get('/qav_token', 'HomeController@getQiniuQavToken')->name('home.qav.token');
    Route::get('/test', 'HomeController@test')->name('home.test');
});

Route::group(['prefix' => 'wechat', 'middleware' => ['FrontWechat']], function () {
    Route::get('/vcourse_pay', 'WechatController@vcourse_pay')->name('wechat.vcourse_pay');
    Route::get('/course_pay', 'WechatController@course_pay')->name('wechat.course_pay');
    Route::get('/vip_pay', 'WechatController@vip_pay')->name('wechat.vip_pay');
    Route::get('/opo-pay', 'WechatController@opoPay')->name('wechat.opo.pay');
    Route::get('/vip_listen', 'WechatController@vip_listen')->name('wechat.vip_listen');
});

//好课
Route::group([], function () {
    Route::get('/block_index', 'CourseController@block_index')->name('course.block_index');
});
Route::group(['middleware' => ['FrontWechat']], function () {
    Route::get('/', 'VcourseController@index')->name('vcourse');
});
Route::group(['prefix' => 'course', 'middleware' => ['FrontWechat']], function () {
	Route::get('/', 'CourseController@index')->name('course');
    Route::get('/search', 'CourseController@search')->name('course.search');
    Route::get('/detail/{id?}', 'CourseController@detail')->name('course.detail');
    Route::post('/course_list', 'CourseController@course_list')->name('course.course_list');
    Route::post('/collection', 'CourseController@collection')->name('course.collection');
    Route::post('/cancel_collection', 'CourseController@cancel_collection')->name('course.cancel_collection');
    Route::get('/comment/{id}', 'CourseController@comment')->name('course.comment');
    Route::post('/comment', 'CourseController@do_comment')->name('course.do_comment');
    Route::post('/join_free', 'CourseController@join_free')->name('course.join_free');
    Route::get('/join_charge/{id}', 'CourseController@join_charge')->name('course.join_charge');
    Route::post('/join_charge', 'CourseController@do_join_charge')->name('course.do_join_charge');

    Route::get('/coupon/{id}', 'CourseController@coupon')->name('course.coupon');
    Route::get('/wechat_pay/{id}', 'CourseController@wechat_pay')->name('course.wechat_pay');
    Route::get('/line_pay/{id}', 'CourseController@line_pay')->name('course.line_pay');
    Route::get('/line_pay_static', 'CourseController@line_pay_static')->name('course.line_pay_static');
    Route::post('/line_pay', 'CourseController@do_line_pay')->name('course.do_line_pay');
    Route::get('/course_report/{id}', 'CourseController@course_report')->name('course.course_report');
    Route::post('/course_report_login', 'CourseController@course_report_login')->name('course.course_report_login');
    Route::post('/get_coupon', 'CourseController@get_coupon')->name('course.get_coupon');

    Route::get('/qrcode/{id}', 'CourseController@qrcode')->name('course.qrcode');
    Route::get('/staticlink/{id}', 'CourseController@staticlink')->name('course.staticlink');

    /** 点赞 */
    Route::post('/comment_like/{id}', 'CourseController@commentLike')->name('course.comment.like');
});

//壹家壹
Route::group(['prefix' => 'opo', 'middleware' => ['FrontWechat']], function () {
    Route::get('/', 'OpoController@index')->name('opo');
    Route::post('/get_coupon', 'OpoController@get_coupon')->name('opo.get_coupon');
    Route::get('/report-share/{id}', 'OpoController@reportShares')->name('opo.report.shares');
});
Route::group(['prefix' => 'opo', 'middleware' => ['FrontWechat', 'FrontAuth']], function () {
    Route::get('/report-previews/{id}', 'OpoController@reportPreviews')->name('opo.report.previews');

    Route::get('/buy/{id}', 'OpoController@buy')->name('opo.buy');
    Route::get('/choose-coupon/{id}', 'OpoController@chooseCoupon')->name('opo.choose.coupon');
    Route::post('/order/{id}', 'OpoController@confirmOrder')->name('opo.confirm.order');

    Route::get('/offline-pay/{id}', 'OpoController@offlinePay')->name('opo.offline.pay');
    Route::post('/offline-pay/{id}', 'OpoController@confirmOfflinePay')->name('opo.confirm.offline.pay');

    Route::any('/comments', 'OpoController@comments')->name('opo.comments');
    Route::get('/comment/{id}', 'OpoController@comment')->name('opo.comment.create');
    Route::post('/comment/{id}', 'OpoController@saveComment')->name('opo.comment.store');
    Route::post('/comment/{id}/like', 'OpoController@like')->name('opo.comment.like');
});

//好看
Route::group(['prefix' => 'vcourse', 'middleware' => ['FrontWechat']], function () {
    Route::get('/', 'VcourseController@index')->name('vcourse');
    Route::get('/search', 'VcourseController@search')->name('vcourse.search');
    Route::get('/detail/{id?}/', 'VcourseController@detail')->name('vcourse.detail');
    Route::get('/add_favor', 'VcourseController@add_favor')->name('vcourse.add_favor');
    Route::get('/add_like', 'VcourseController@add_like')->name('vcourse.add_like');
    Route::post('/add_mark', 'VcourseController@add_mark')->name('vcourse.add_mark');
    Route::post('/add_view_cnt', 'VcourseController@add_view_cnt')->name('vcourse.add_view_cnt');
    Route::any('/more/{type}/', 'VcourseController@more')->name('vcourse.more');
    Route::post('/recommend_list', 'VcourseController@recommend_list')->name('vcourse.recommend_list');

});
Route::group(['prefix' => 'vcourse', 'middleware' => ['FrontWechat']], function () {
    Route::post('/order_free', 'VcourseController@order_free')->name('vcourse.order_free');
    Route::get('/order/{id}/', 'VcourseController@order')->name('vcourse.order');
    Route::get('/coupon/{id}/', 'VcourseController@coupon')->name('vcourse.coupon');
    Route::post('/order_save', 'VcourseController@order_save')->name('vcourse.order_save');
});


//好问
Route::group(['prefix' => 'question', 'middleware' => ['FrontWechat']], function () {
    Route::get('/', 'QuestionController@index')->name('question');
    Route::get('/teacher/{id?}', 'QuestionController@teacher')->name('question.teacher');
    Route::get('/talk/{id?}', 'QuestionController@talk')->name('question.talk');
    //for ajax
    Route::post('/teacher_list', 'QuestionController@teacher_list')->name('question.teacher_list');
    Route::post('/question_list', 'QuestionController@question_list')->name('question.question_list');
    Route::post('/talk_list', 'QuestionController@talk_list')->name('question.talk_list');

    //语音收听表记录
    Route::post('/question_listen', 'QuestionController@question_listen')->name('question.question_listen');
    //收听指导师
    Route::post('/teacher_favor', 'QuestionController@teacher_favor')->name('question.teacher_favor');

    Route::get('/talk_comment_like', 'QuestionController@talk_comment_like')->name('question.talk_comment_like');

    /** 点赞 */
    Route::post('/teacher_like', 'QuestionController@teacher_like')->name('question.teacher.like');
});

Route::group(['prefix' => 'question', 'middleware' => ['FrontWechat', 'FrontAuth']], function () {
    Route::get('/ask_talk', 'QuestionController@ask_talk')->name('question.ask_talk');
    Route::post('/ask_talk_store', 'QuestionController@ask_talk_store')->name('question.ask_talk_store');
    Route::get('/talk_comment/{id}', 'QuestionController@talk_comment')->name('question.talk_comment');
    Route::post('/talk_comment_store', 'QuestionController@talk_comment_store')->name('question.talk_comment_store');
    Route::get('/ask_question/{id}', 'QuestionController@ask_question')->name('question.ask_question');
    //提问保存
    Route::post('/ask_question_store', 'QuestionController@ask_question_store')->name('question.ask_question_store');
});

Route::group(['prefix' => 'user', 'middleware' => ['FrontWechat']], function () {
    Route::any('/login', 'UserController@login')->name('user.login');
    Route::post('/user/do_login', 'UserController@do_login')->name('user.do_login');
    Route::post('/user/get_code', 'UserController@get_code')->name('user.get_code');
});

//用户中心-我的
Route::group(['prefix' => 'user', 'middleware' => ['FrontWechat']], function () {
    Route::get('/', 'UserController@index')->name('user');
    //钱包
    Route::get('/wallet', 'UserController@wallet')->name('user.wallet');
    //收益
    Route::get('/balance', 'UserController@balance')->name('user.balance');
    //和贝
    Route::get('/score', 'UserController@score')->name('user.score');
    //优惠券
    Route::get('/coupon', 'UserController@coupon')->name('user.coupon');
    Route::post('/send_coupon', 'UserController@sendCoupon')->name('user.sendCoupon');
    //我的收听
    Route::get('/listen_teacher', 'UserController@listen_teacher')->name('user.listen_teacher');
    //我的提问
    Route::get('/question', 'UserController@question')->name('user.question');
    //设置
    Route::get('/setting', 'UserController@setting')->name('user.setting');

    Route::get('/my_card', 'UserController@my_card')->name('user.my_card');
    Route::get('/my_info', 'UserController@my_info')->name('user.my_info');
    Route::get('/talk', 'UserController@talk')->name('user.talk');

    
    //申请提现
    Route::get('/cash', 'UserController@cash')->name('user.cash');
    Route::post('/cash_store', 'UserController@cash_store')->name('user.cash_store');

    Route::get('/answer_voice/{id}', 'UserController@answer_voice')->name('user.answer_voice');
    Route::post('/upload_voice', 'UserController@upload_voice')->name('user.upload_voice');
    Route::post('/upload_voice_status', 'UserController@upload_voice_status')->name('user.upload_voice_status');

    /** 用户个人资料 */
    Route::get('/profile', 'ProfileController@index')->name('user.profile');
    Route::get('/profile/edit', 'ProfileController@edit')->name('user.profile.edit');
    Route::post('/profile', 'ProfileController@update')->name('user.profile.update');
});

Route::group(['prefix'=>'user', 'middleware'=>['FrontWechat']], function(){
    //我的作业&笔记
    Route::get('/note', 'MyController@note')->name('my.notes');
    Route::post('/note/delete', 'MyController@deleteNote')->name('my.note.delete');
    //我的课程（收藏）
    Route::get('/courses', 'MyController@courses')->name('my.courses');
    //我的地址
    Route::get('/addresses', 'MyController@addresses')->name('my.addresses');
    Route::post('/addresses/add', 'MyController@addAddress')->name('my.addresses.add');
    Route::post('/addresses/edit', 'MyController@editAddress')->name('my.addresses.edit');
    Route::post('/addresses/delete', 'MyController@deleteAddress')->name('my.addresses.delete');
    Route::post('/addresses/default', 'MyController@defaultAddress')->name('my.addresses.default');
    //我的订单
    Route::get('/orders', 'MyController@orders')->name('my.orders');
    Route::get('/orders/members/{team_id}', 'MyController@ordersMembers')->name('my.orders_members');
    Route::get('/order_cancel', 'MyController@orderCancel')->name('my.orders.cancel');
    //推荐有奖
    Route::get('/invite_user', 'MyController@invite_user')->name('my.invite_user');
    Route::get('/invited_user', 'MyController@invited_user')->name('my.invited_user');
    Route::post('/get_coupon', 'MyController@get_coupon')->name('my.get_coupon');
});

/** 指导师相关 */
Route::group(['prefix'=>'tutor', 'middleware'=>['FrontWechat']], function(){
    Route::get('/apply', 'TutorController@apply')->name('tutor.apply');
    Route::get('/complete', 'TutorController@complete')->name('tutor.complete');
    Route::post('/complete', 'TutorController@tutorSave')->name('tutor.save');
    Route::post('/upload', 'TutorController@upload')->name('tutor.upload');

    Route::get('/answers', 'TutorController@answers')->name('tutor.answers');
});

/** 合伙人相关 */
Route::group(['prefix'=>'partner', 'middleware'=>['FrontWechat']], function(){
    Route::get('/apply', 'PartnerController@apply')->name('partner.apply');
    Route::get('/complete', 'PartnerController@complete')->name('partner.complete');
    Route::post('/complete', 'PartnerController@partnerSave')->name('partner.save');
    Route::get('/operate', 'PartnerController@operate')->name('partner.operate');
    Route::get('/orders', 'PartnerController@orders')->name('partner.orders');
    Route::post('/user', 'PartnerController@user')->name('partner.user');
    Route::post('/day7', 'PartnerController@day7')->name('partner.day7');
    Route::post('/city_check', 'PartnerController@city_check')->name('partner.city_check');
    Route::get('/card', 'PartnerController@card')->name('partner.card');
    Route::get('/card/show/{uid}', 'PartnerController@cardShow')->name('partner.card.show');
    Route::get('/card/edit', 'PartnerController@cardEdit')->name('partner.cardEdit');
    Route::post('/card/update', 'PartnerController@cardUpdate')->name('partner.card.update');
    Route::post('/card/change/banner', 'PartnerController@cardChangeBanner')->name('partner.card.change_banner');
    Route::post('/card/create/img', 'PartnerController@cardCreateImg')->name('partner.card.create_img');
    Route::post('/card/remove/img', 'PartnerController@cardRemoveImg')->name('partner.card.remove_img');
    Route::post('/card/remove/video', 'PartnerController@cardRemoveVideo')->name('partner.card.remove_video');
    Route::post('/card/change/video', 'PartnerController@cardChangeVideo')->name('partner.card.change_video');
    Route::post('/card/build/love', 'PartnerController@buildLover')->name('partner.card.build_love');
});

/** 和会员业务 */
Route::group(['prefix'=>'vip','middleware' => ['FrontWechat']], function (){
    Route::get('/', 'VipController@index')->name('vip');
});

/** 和会员业务 */
Route::group(['prefix'=>'vip','middleware' => ['FrontWechat']], function (){
    Route::any('/buy', 'VipController@buy')->name('vip.buy');
    Route::post('/create_order', 'VipController@create_order')->name('vip.create_order');
    Route::get('/active', 'VipController@active')->name('vip.active');
    Route::post('/active_store', 'VipController@active_store')->name('vip.active_store');
    //收货地址相关
    Route::get('/receipt_address', 'VipController@receipt_address')->name('vip.receipt_address');
    Route::post('/add_address', 'VipController@add_address')->name('course.add_address');
    Route::post('/edit_address', 'VipController@edit_address')->name('course.edit_address');
    Route::post('/set_default', 'VipController@set_default')->name('course.set_default');
    Route::post('/delete', 'VipController@delete')->name('course.delete');
    //优惠券选择
    Route::get('/coupon', 'VipController@coupon')->name('vip.coupon');
    //和会员天数记录
    Route::get('/records', 'VipController@records')->name('vip.records');
});

//文章
Route::group(['prefix' => 'article', 'middleware' => ['FrontWechat']], function () {
    Route::get('/helpcenter/{type}', 'ArticleController@helpcenter')->name('article.helpcenter');
    Route::get('/helpcenterdetail/{id}', 'ArticleController@helpcenterdetail')->name('article.helpcenterdetail');
    Route::get('/{id}', 'ArticleController@show')->name('article');
});

//建议留言
Route::group(['prefix' => 'leaveword', 'middleware' => ['FrontAuth']], function () {
	Route::get('/index', 'LeaveWordController@index')->name('leaveword');
	Route::post('/create', 'LeaveWordController@create')->name('leaveword.create');
});

/** 用户分享的事件回调 */
Route::group(['prefix' => 'share', 'middleware' => ['FrontWechat']], function () {
    Route::post('/', 'ShareController@index')->name('share');
    Route::get('/love_angle', 'ShareController@loveAngle')->name('share.angle');
    Route::get('/hot/{id}', 'ShareController@hot')->name('share.hot');
    Route::get('/audio', 'ShareController@audio')->name('share.audio');
});
