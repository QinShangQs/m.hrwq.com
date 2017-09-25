@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_task_note">
                    <div class="mtn_title">
                        <div class="mtn_title_1 select" style="width:100%">作业</div>
                        <!-- <div class="mtn_title_2">笔记</div> -->
                    </div>
                    <div class="mtn_div">
                        <div class="mtn_div_1">
                            <ul class="mtn_div_1_list">
                                @if(count($notes))
                                    @foreach($notes as $item)
                                        <li>
                                            <div class="mtn_div_1_list_problem">问题：{{$item->vcourse->title}} - {{$item->vcourse->work}}</div>
                                            <div class="mtn_div_1_list_date">{{(string)$item->created_at}}</div>
                                            <div class="mtn_div_1_list_p">作业内容：{{$item->mark_content}}</div>
                                            <div class="mtn_div_1_list_delect" data-id="{{$item->id}}"><img
                                                        src="/images/my/mq_div_2_list_delete.png" alt=""/></div>
                                            <!--data-id为信息id-->
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                        <div class="mtn_div_2" style="display:none;">
                            <ul class="mtn_div_2_list">
                                @if(count($works))
                                    @foreach($works as $item)
                                        <li>
                                            <div class="mtn_div_1_list_problem">课程：{{$item->vcourse->title}}</div>
                                            <div class="mtn_div_1_list_date">{{(string)$item->created_at}}</div>
                                            <div class="mtn_div_1_list_p">笔记内容：{{$item->mark_content}}</div>
                                            <div class="mtn_div_1_list_delect" data-id="{{$item->id}}"><img
                                                        src="/images/my/mq_div_2_list_delete.png" alt=""/></div>
                                            <!--data-id为信息id-->
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function () {
            var lock=false;
            $(".mtn_div_1_list_delect").click(function () {//删除作业
                if (lock) return;
                lock = true;
                var _self = $(this);
                $.ajax({
                    url: '{{route('my.note.delete')}}',
                    type: 'post',
                    data: {"id": $(this).data('id')},
                    dataType: 'json',
                    success: function (res) {
                        if(res.code == 0){
                            _self.closest('li').remove();
                        } else {
                            Popup.init({
                                popHtml: res.message,
                                popFlash:{
                                    flashSwitch:true,
                                    flashTime:2000
                                }
                            });
                        }
                        lock = false;
                    }

                });
            });
            $(".mtn_div_2_list_delect").click(function () {//删除笔记
                if (lock=true) return;
                lock = true;
                var _self = $(this);
                $.ajax({
                    url: '{{route('my.note.delete')}}',
                    type: 'post',
                    data: {"id": $(this).data('id')},
                    dataType: 'json',
                    success: function (res) {
                        if(res.code == 0){
                            _self.closest('li').remove();
                        } else {
                            Popup.init({
                                popHtml: res.message,
                                popFlash:{
                                    flashSwitch:true,
                                    flashTime:2000
                                }
                            });
                        }
                        lock = false;
                    }

                });
            });
            $(".mtn_title>div").click(function () {
                if ($(this).attr("class") != "mtn_title_1 select" && $(this).attr("class") != "mtn_title_2 select") {
                    $(".mtn_title_1").attr("class", "mtn_title_1");
                    $(".mtn_title_2").attr("class", "mtn_title_2");
                    $(this).addClass("select");
                    if ($(this).attr("class") == "mtn_title_1 select") {
                        $(".mtn_div_2").hide();
                        $(".mtn_div_1").show();
                    } else {
                        $(".mtn_div_1").hide();
                        $(".mtn_div_2").show();
                    }
                }
            });
        });
    </script>
@endsection