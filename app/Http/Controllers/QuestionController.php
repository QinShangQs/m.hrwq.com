<?php
/**
 * 好问模块
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Question;
use App\Models\Talk;
use App\Models\HotSearch;
use App\Models\QuestionTag;
use App\Models\Tag;
use App\Models\TalkTag;
use App\Models\TalkComment;
use App\Models\Order;
use App\Models\UserFavor;
use App\Models\LikeRecord;
use App\Models\QuestionListener;
use App\Models\Carousel;

use DB,Wechat;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
    	//轮播图
    	$carouselList = Carousel::whereUseType('4')->orderBy('sort', 'desc')->get();
        //互助榜标签
        $talk_tags = TalkTag::with(['tag' => function ($query) {
            $query->withTrashed();
        }])->select('*', \DB::raw('COUNT(id) as num'))->groupBy('tag_id')->orderBy('num', 'desc')->limit(4)->get();

        //互助榜
        $talks = $this->talk_list($request);
        $wx_js = Wechat::js();

        return view('question.index', compact('carouselList', 'talks', 'talk_tags','wx_js'));
    }

    //指导师详情
    public function teacher(Request $request,$id)
    {
        $data = User::with(['question' => function ($query) {
            $query->where('answer_flg', 2);
        }, 'user_favor'])
            ->where('role', 2)->whereNotNull('tutor_price')->where('tutor_price','!=','0')
            ->find($id);

        if (!$data) {
            abort(403, '查找失败！');
        }

        //语音$audio_type  1一元旁听  2限时免费 3vip免费 4已支付
        if (session('wechat_user')) {
            $is_favor = UserFavor::where('user_id', user_info('id'))->where('favor_id', $id)->where('favor_type', 3)->first();
            foreach ($data->question as $item) {
                set_audio_state($item);
            }
        } else {
            $is_favor = '';
            $cur_time = time();
            foreach ($data->question as $item) {
                if ($item->free_flg == 1 && $cur_time >= strtotime($item->free_from) && $cur_time <= strtotime($item->free_end)) {
                    $item->audio_type = 2;
                    $item->audio_msg = '限时免费';
                } else {
                    $item->audio_type = 1;
                    $item->audio_msg = '1元旁听';
                }
            }
        }

        $session_mobile = '';
        if (isset(session('user_info')['mobile'])) {
            $session_mobile = session('user_info')['mobile'];
        }
        $requestUri = $request->getRequestUri();
        $wx_js = Wechat::js();
        return view('question.teacher_show', ['data' => $data, 'is_favor' => $is_favor, 'session_mobile' => $session_mobile, 'requestUri' => $requestUri, 'wx_js' => $wx_js]);
    }

    //收听/取消收听指导师
    public function teacher_favor(Request $request)
    {
        $tid = $request->input('tid');
        $uid = user_info('id');

        if ($uid == $tid) {
            abort(403, '不能对自己收听！');
        }


        if ($item = UserFavor::where('user_id', $uid)->where('favor_id', $tid)->where('favor_type', 3)->first()) {
            if ($item->delete()) {
                return response()->json(['code' => 0, 'message' => '取消成功!']);
            } else {
                return response()->json(['code' => 1, 'message' => '取消失败!']);
            }
        } else {
            $data = [];
            $data['user_id'] = $uid;
            $data['favor_id'] = $tid;
            $data['favor_type'] = 3;

            if (UserFavor::create($data)) {
                return response()->json(['code' => 2, 'message' => '收听成功!']);
            } else {
                return response()->json(['code' => 3, 'message' => '收听失败!']);
            }
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     *
     * 老师点赞
     */
    public function teacher_like()
    {
        $userId = user_info('id');
        $teacher = User::find(request('user_id'));
        if ($teacher == null) {
            return response()->json(['code' => 1, 'message' => '指导师查询失败!']);
        }
        //查询用户指导师点赞记录
        $likeRecord = LikeRecord::where('user_id', $userId)->where('like_id', $teacher->id)->where('like_type', 5)->first();
        if ($likeRecord) {
            return response()->json(['code' => 2, 'message' => '请勿重复点赞!']);
        } else {
            $likeRecord = new LikeRecord;
            $likeRecord->user_id = $userId;
            $likeRecord->like_id = $teacher->id;
            $likeRecord->like_type = 5;
            DB::beginTransaction();
            try {
                $likeRecord->save();
                $teacher->increment('likes', 1);
                DB::commit();
                return response()->json(['code' => 0, 'message' => '点赞成功!', 'data' => $teacher->likes]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['code' => 3, 'message' => '点赞失败!']);
            }
        }
    }
    //语音问题
    public function ask_question($uid)
    {
        if ($uid == user_info('id')) {
            abort(403, '不能对自己提问！');
        }

        $data = Tag::all();
        return view('question.ask_question', ['uid' => $uid, 'data' => $data]);
    }

    //语音问题-保存
    public function ask_question_store(Request $request)
    {
        if ($request->input('tutor_id') == user_info('id')) {
            abort(403, '不能对自己提问！');
        }

        $request->merge(array_map('trim', $request->all()));
        $this->validate($request, [
            'content' => 'required|min:20|max:100',
        ], [], [
            'content' => '问题描述'
        ]);

        $data = [];
        $data['user_id'] = user_info('id');
        $data['tutor_id'] = $request->input('tutor_id');
        $data['content'] = $request->input('content');
        //获取指导师价格
        $data['price'] = User::where('id', $data['tutor_id'])->pluck('tutor_price');

        try {
            $tmp = Question::create($data);
            $question_id = $tmp->id;
            $tags_id = $request->input('tag_ids');
            if ($tags_id) {
                $question_tag = [];
                $tags_arr = explode(',', $tags_id);
                foreach ($tags_arr as $k => $v) {
                    $question_tag[$k]['question_id'] = $question_id;
                    $question_tag[$k]['tag_id'] = $v;
                }
                QuestionTag::insert($question_tag);
            }
            return response()->json(['code' => 0, 'message' => '提问成功!', 'qid' => $question_id]);
        } catch (\Exception $e) {
            return response()->json(['code' => 3, 'message' => '提问失败!']);
        }
    }

    /**
     * 语音收听  1.收听记录表  2收听次数更新
     * !!指导师收听自己回答的问题，不记录任何数据
     * !!提问人收听 不对旁听次数 listener_nums 计数统计
     *
     */
    public function question_listen(Request $request)
    {
        //1一元旁听  2限时免费 3vip免费 4已支付
        $aid = $request->input('aid');
        $qid = $request->input('qid');
        $uid = user_info('id');

        if (QuestionListener::where('question_id', $qid)->where('user_id', $uid)->first()) {
            return response()->json(['code' => 1, 'message' => '已收听!']);
        }

        $data = [];
        $data['is_free'] = in_array($aid, [1, 4]) ? 2 : 1;
        $data['question_id'] = $qid;
        $data['user_id'] = $uid;

        $question = Question::find($qid);

        if ($question->tutor_id == $uid) {
            return response()->json(['code' => 2, 'message' => '指导师本人收听']);
        }

        if (QuestionListener::create($data)) {
            if ($question->user_id != $uid) {
                $question->increment('listener_nums', 1);
            }
            return response()->json(['code' => 0, 'message' => '收听成功!']);
        } else {
            return response()->json(['code' => 3, 'message' => '收听失败!']);
        }
    }

    /**
     * 互助榜详情
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function talk($id)
    {
        $talk = Talk::with([
            'comments' => function ($query) {
                $query->orderBy('id', 'desc');
            },
            'comments.like_record' => function ($query) {
                $query->where('like_type', '4');
                $query->where('user_id', user_info('id'));
            }
        ])->where('id', $id)->first();
        //浏览次数+1
        $talk->increment('view', 1);
        return view('question.talk_show', ['talk' => $talk]);
    }

    /**
     * 互助榜发帖
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ask_talk()
    {
        $data = Tag::all();
        return view('question.ask_talk', ['data' => $data]);
    }

    /**
     * 互助榜发帖-保存
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ask_talk_store(Request $request)
    {
        $request->merge(array_map('trim', $request->all()));
        $this->validate($request, [
            'title' => 'required|min:4|max:50',
            'content' => 'required|min:20',
        ], [], [
            'title' => '标题',
            'content' => '内容'
        ]);

        $data = [];
        $data['user_id'] = user_info('id');
        $data['title'] = $request->input('title');
        $data['content'] = $request->input('content');

        try {
            $tmp = Talk::create($data);
            $talk_id = $tmp->id;
            $tags_id = $request->input('tag_ids');
            if ($tags_id) {
                $question_tag = [];
                $tags_arr = explode(',', $tags_id);
                foreach ($tags_arr as $k => $v) {
                    $question_tag[$k]['talk_id'] = $talk_id;
                    $question_tag[$k]['tag_id'] = $v;
                }

                TalkTag::insert($question_tag);
            }
            get_score(4);
            return response()->json(['code' => 0, 'message' => '发布成功!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 1, 'message' => '发布失败!']);
        }
    }

    /**
     * 帖子评论
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function talk_comment($id)
    {
        return view('question.talk_comment', ['id' => $id]);
    }

    /**
     * 帖子评论-保存
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function talk_comment_store(Request $request)
    {
        $request->merge(array_map('trim', $request->all()));
        $this->validate($request, [
            'content' => 'required|min:20',
        ], [], [
            'content' => '评论内容'
        ]);

        $data = [];
        $data['r_user_id'] = user_info('id');
        $data['talk_id'] = $request->input('talk_id');
        $data['comment_c'] = $request->input('content');

        try {
            TalkComment::create($data);
            get_score(5);
            return response()->json(['code' => 0, 'message' => '评价成功!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 1, 'message' => '评价失败!']);
        }
    }

    /**
     * 互助榜点赞
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function talk_comment_like(Request $request)
    {
        $comment_id = $request->input('id');
        $user_id = user_info('id');

        $comment = TalkComment::find($comment_id);

        if ($comment == null) {
            return response()->json(['code' => 1, 'message' => '不存在该评论!']);
        }

        $likeRecord = LikeRecord::where('user_id', $user_id)->where('like_id', $comment_id)->where('like_type', 4)->first();

        if ($likeRecord) {
            return response()->json(['code' => 2, 'message' => '请勿重复点赞!']);
        } else {
            $likeRecord = new LikeRecord;
            $likeRecord->user_id = $user_id;
            $likeRecord->like_id = $comment_id;
            $likeRecord->like_type = 4;
            DB::beginTransaction();
            try {
                $likeRecord->save();
                $comment->increment('likes', 1);
                DB::commit();
                return response()->json(['code' => 0, 'message' => '点赞成功!']);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['code' => 3, 'message' => '点赞失败!']);
            }
        }
    }


    //智慧榜 前n个固定显示
    private function teacher_top_data($request)
    {
        $builder = User::select('realname', 'profileIcon','likes', 'id', 'question_amount', 'tutor_honor', 'nickname')
            ->with(['question' => function ($query) {
                $query->where('answer_flg', 2);
            }, 'user_favor','like_record' => function ($query) {
                $query->select("like_id")->where('like_type', 5)->where('user_id',session('user_info')['id'] );
            }])
            ->where('role', 2)->whereNotNull('tutor_price')->where('tutor_price','!=','0');

        if (($search_key = $request->input('search_key')) && $request->input('selected_tab') == 1) {
            $builder->where('realname', 'like', '%' . $search_key . '%');
        }
        return $builder->orderBy('sort', 'desc')->orderBy('id')
                ->limit(config('constants.teacher_list_top'))->get();
    }

    //智慧榜
    public function teacher_list(Request $request, $teachers_top_uid_arr = [])
    {
        //排除置顶指导师
        $teachers_top_uid_arr = $teachers_top_uid_arr ? $teachers_top_uid_arr : json_decode($request->input('teachers_top_uid_json'));

        $questionQuery = "select tutor_id, max(answer_date) as answer_date from question where answer_flg = 2 group by tutor_id";

        $builder = User::select('user.realname', 'user.profileIcon', 'user.id', 'user.question_amount', 'user.tutor_honor', 'user.nickname','likes')
            ->with(['question' => function ($query) {
                $query->where('answer_flg', 2);
            }, 'user_favor','like_record' => function ($query) {
                $query->select("like_id")->where('like_type', 5)->where('user_id',session('user_info')['id'] );
            }])->leftJoin(DB::raw("({$questionQuery}) as question"), function($join) {
                $join->on('user.id', '=', 'question.tutor_id');
            })
            ->where('user.role', 2)->whereNotNull('user.tutor_price')->where('tutor_price','!=','0');

        if ($teachers_top_uid_arr) {
            $builder->whereNotIn('user.id', $teachers_top_uid_arr);
        }

        if (($search_key = $request->input('search_key')) && $request->input('selected_tab') == 1) {
            $builder->where(function ($query) use ($search_key) {
                $query->where('user.realname', 'like', '%' . $search_key . '%')
                    ->orwhere('user.nickname', 'like', '%' . $search_key . '%');
                $query->orWhere(function ($query) use ($search_key) {
                    $query->whereHas('c_city', function ($query ) use ($search_key) {
                        $query->where('area_name', 'like', '%' . $search_key . '%');
                    });
                });
            });
        }

        $data = $builder->groupBy('user.id')
            ->orderBy('question.answer_date', 'desc')
            ->orderBy('user.sort', 'desc')
            ->orderBy('user.id')
            ->paginate();
        //ajax请求，返回json
        if ($request->ajax()) {
            return $data->toJson();
        }

        return $data;
    }

    //问题榜
    public function question_list(Request $request)
    {
        //默认流程 已回答肯定是 已支付过的
        $builder = Question::with('answer_user','order')->where('answer_flg', 2);

        //排除提问后未付款的问题
        // $builder->join('order', function ($join) {
        //     $join->on('question.id', '=', 'order.pay_id')
        //          ->where('order.pay_type', '=', 4)
        //          ->where('order.order_type', '=', 2);
        // });
        $builder->whereHas('order', function ($query) {
                $query->where('order.pay_type', '=', 4);
                $query->where('order.order_type', '=', 2);
        });
        if (($search_key = $request->input('search_key')) && $request->input('selected_tab') == 2) {
            $builder->where(function ($query) use ($search_key) {
                $query->where('content', 'like', '%' . $search_key . '%');

                // ask_user
                $query->orWhereHas('ask_user.c_city', function ($query) use ($search_key) {
                    $query->where(function($query) use($search_key) {
                        $query->where('realname', 'like', '%' . $search_key . '%')
                            ->orWhere('nickname', 'like', '%' . $search_key . '%')
                            ->orWhere('area_name', 'like', '%' . $search_key . '%');
                    });
                });
                // answer_user
                $query->orWhereHas('answer_user.c_city', function ($query) use ($search_key) {
                    $query->where(function($query) use($search_key) {
                        $query->where('realname', 'like', '%' . $search_key . '%')
                            ->orWhere('nickname', 'like', '%' . $search_key . '%')
                            ->orWhere('area_name', 'like', '%' . $search_key . '%');
                    });
                });
            });
        }

        if (($tag_id = $request->input('search_tag')) && $request->input('selected_tab') == 2) {
            $builder->with('tags')->whereHas('tags', function ($query) use ($tag_id) {
                $query->where('question_tag.tag_id', $tag_id);
            });
        }

        $builder->orderBy('question.free_flg', 'desc')->orderBy('sort', 'desc')->orderBy('answer_date', 'desc');

        $data = $builder->paginate();

        if (session('wechat_user')) {
            foreach ($data as $item) {
                set_audio_state($item);
            }
        } else {
            $cur_time = time();
            foreach ($data as $item) {
                if ($item->free_flg == 1 && $cur_time >= strtotime($item->free_from) && $cur_time <= strtotime($item->free_end)) {
                    $item->audio_type = 2;
                    $item->audio_msg = '限时免费';
                } else {
                    $item->audio_type = 1;
                    $item->audio_msg = '1元旁听';
                }
            }
        }

        //ajax请求，返回json
        if ($request->ajax()) {
            return $data->toJson();
        }

        return $data;
    }

    //互助榜
    public function talk_list(Request $request)
    {
        $builder = Talk::with('ask_user', 'ask_user.c_city', 'tags');

        if (($search_key = $request->input('search_key')) && $request->input('selected_tab') == 3) {
            $builder->where(function ($query) use ($search_key) {
                $query->where('title', 'like', '%' . $search_key . '%')
                    ->orWhere('content', 'like', '%' . $search_key . '%');

                // ask_user
                $query->orWhereHas('ask_user.c_city', function ($query) use ($search_key) {
                    $query->where(function($query) use($search_key) {
                        $query->where('realname', 'like', '%' . $search_key . '%')
                            ->orWhere('nickname', 'like', '%' . $search_key . '%')
                            ->orWhere('area_name', 'like', '%' . $search_key . '%');
                    });
                });
                // answer_user
                $query->orWhereHas('comments.answer_user', function ($query) use ($search_key) {
                    $query->where(function($query) use($search_key) {
                        $query->where('realname', 'like', '%' . $search_key . '%')
                            ->orWhere('nickname', 'like', '%' . $search_key . '%');
                    });
                })->orWhereHas('comments.answer_user.c_city', function ($query) use ($search_key) {
                    $query->where('area_name', 'like', '%' . $search_key . '%');
                });
            });
        }
        if (($tag_id = $request->input('search_tag')) && $request->input('selected_tab') == 3) {
            $builder->whereHas('tags', function ($query) use ($tag_id) {
                $query->where('talk_tag.tag_id', $tag_id);
            });
        }

        $builder->orderBy('sort', 'desc')->orderBy('id', 'desc');

        $data = $builder->paginate();

        //ajax请求，返回json
        if ($request->ajax()) {
            return $data->toJson();
        }

        return $data;
    }
}
