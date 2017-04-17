$(document).ready(function(){//历史搜索记录
    window.viewSearch={
        init:function(search_key){//更新搜索记录
            this.localStorageKey = 'view_history';
            var currentKwdKey = 'k_'+search_key,        //当前搜索值对应的键值
                currentKwdList = {},                    //创建json对象
                kwdStr = localStorage.getItem(this.localStorageKey);     //获取历史搜索内容（字符串）

            if(kwdStr !== null){
                var kwdList = JSON.parse(kwdStr);       //将历史搜索内容转化为对象
            }

            currentKwdList[currentKwdKey] = search_key; //将当前输入关键字动态加入新创建json对象中

            if(kwdList == undefined){   //如果不存在历史搜索内容，直接将当前搜索内容转化为字符串
                var kwdTxt = JSON.stringify(currentKwdList);
            }else{
                //合并对象（当前搜索内 和 历史 搜索内容）
                var kwdTxtObj = this.mergeToRepeat(currentKwdList,kwdList);
                //转化为序列化json字符串格式
                kwdTxt = JSON.stringify(kwdTxtObj);
            }
            localStorage.setItem(this.localStorageKey, kwdTxt);  //存入localStorage
        },
        has:function(search_key){//更新搜索记录
            this.localStorageKey = 'view_history';
            var currentKwdKey = 'k_'+search_key,        //当前搜索值对应的键值
                currentKwdList = {},                    //创建json对象
                kwdStr = localStorage.getItem(this.localStorageKey);     //获取历史搜索内容（字符串）

            if(kwdStr !== null){
                var kwdList = JSON.parse(kwdStr);       //将历史搜索内容转化为对象
                if (kwdList[currentKwdKey]!=undefined) {
                    return true;
                }
            } else {
                return false;
            }
            return false;
        },
        empty:function(){//情况搜索记录
            localStorage.removeItem(this.localStorageKey); //删除h_kwd_list这个键值的里面所有的值
        },

        mergeToRepeat:function(json1,json2){    //遍历两个对象合成一个并将两个对象中重复的键值的值去掉前一个
            var resJson={};
            for(var i in json1){

                resJson[i]=json1[i];
            }
            for (var i in json2) {
                resJson[i]=json2[i];
            };
            return resJson;
       }

    }

});