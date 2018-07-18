
$(function () {
    function showloading() {
        $(".avd-loading").show();
    }

    function hideloading() {
        $(".avd-loading").hide();
    }

    var uploader = Qiniu.uploader({
        runtimes: 'html5,flash,html4',
        browse_button: 'pickfiles',
        container: 'container',
        drop_element: 'container',
        max_file_size: '60mb',
        flash_swf_url: 'js/plupload/Moxie.swf',
        dragdrop: true,
        chunk_size: '4mb',
        uptoken_url: $("#token_url").val(),
        domain: $("#domain_url").val(),
        auto_start: false,
        multi_selection: false,
        upload_domain: "http://up-z1.qiniup.com", //定义上传域名
        filters : {
            max_file_size : '60mb',
            prevent_duplicates : true,
            mime_types : [ {
                    title : "视频文件",
                    extensions : "flv,mpg,mpeg,avi,wmv,mov,asf,rm,rmvb,mkv,m4v,mp4"
		}, 
            ]
        },
        init: {
            'FilesAdded': function (up, files) {
                Popup.init({
                    popHtml: '确认要上传该视频吗？',
                    popOkButton: {
                        buttonDisplay: true,
                        buttonName: "确认",
                        buttonfunction: function () {
                            uploader.start();//启动上传
                            //显示效果
                            showloading();
                            plupload.each(files, function (file) {
                                var progress = new FileProgress(file, 'fsUploadProgress');
                                progress.setStatus("等待...");
                            });
                        }
                    },
                    popCancelButton:{
                        buttonDisplay:true,
                        buttonName:"取消",
                        buttonfunction:function(){}
                    }
                });
            },
            'BeforeUpload': function (up, file) {
                var progress = new FileProgress(file, 'fsUploadProgress');
                var chunk_size = plupload.parseSize(this.getOption('chunk_size'));
                if (up.runtime === 'html5' && chunk_size) {
                    progress.setChunkProgess(chunk_size);
                }
            },
            'UploadProgress': function (up, file) {
                var progress = new FileProgress(file, 'fsUploadProgress');
                var chunk_size = plupload.parseSize(this.getOption('chunk_size'));
                progress.setProgress(file.percent + "%", file.speed, chunk_size);
            },
            'UploadComplete': function () {
                hideloading();
            },
            'FileUploaded': function (up, file, info) {
                console.log('FileUploaded :' + info, file, up);
                //修改数据库
                var ji = $.parseJSON(info);
                var url = $("#domain_url").val() + "" + ji.key;
                var hash = ji.hash;
                changeVideo(url, hash);
            },
            'Error': function (up, err, errTip) {
                console.error(err);
                Popup.init({
                    popHtml: '<p>' + errTip + '</p>',
                    popFlash: {
                        flashSwitch: true,
                        flashTime: 2000,
                    }
                });
            },
            'Key': function (up, file) {
                //自己定义文件名
                var extarr = file['name'].split('.');
                var prename = "";
                var ext = "";
                if (extarr.length === 1) {
                    var arr = file['type'].split('/');
                    prename = extarr[0];
                    ext = (arr[arr.length - 1] == 'undefined') ? '' : arr[arr.length - 1];
                } else {
                    ext = '.' + extarr[extarr.length - 1]; //得到后缀
                    var index = file['name'].lastIndexOf('.');//得到最后一个点的坐标
                    prename = file['name'].substring(0, index);//得到最后一个点之前的字符串
                }

                var key = "avd/" + $("#uid").val() +  "/" + prename + ext;
                console.info('avd key is ' + key);
                return key;
            }
        }
    });

    uploader.bind('FileUploaded', function () {
        console.log('hello man,a file is uploaded');
    });
    
});
