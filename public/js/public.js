$(document).ready(function(){//公用脚本
	window.Popup={//手机页面弹窗功能插件
		init:function(value){//初始化各种条件
			value.popTitle=value.popTitle||"";
			value.popHtml=value.popHtml||"";
			value.popOkButton=value.popOkButton||{buttonDisplay:false,buttonfunction:function(){}};
			value.popCancelButton=value.popCancelButton||{buttonDisplay:false,buttonfunction:function(){}};
			value.popFlash=value.popFlash||{flashSwitch:false};
			this.conduct(value.popTitle,value.popHtml,value.popOkButton,value.popCancelButton,value.popFlash);
		},
		conduct:function(popTitle,popHtml,popOkButton,popCancelButton,popFlash){//开始执行
			var buttonHtml='';
			if(popOkButton.buttonDisplay||popCancelButton.buttonDisplay){
				buttonHtml='<div class="popupWindow_button">';
				if(popCancelButton.buttonDisplay){
					var buttonName=popCancelButton.buttonName||"取消";
					buttonHtml+='<div class="popupWindow_button_cancel">'+buttonName+'</div>';
				}
				if(popOkButton.buttonDisplay){
					var buttonName=popOkButton.buttonName||"确认";
					buttonHtml+='<div class="popupWindow_button_ok">'+buttonName+'</div>';
				}
				buttonHtml+='</div>';
			}
			var titleHtml='';
			if(popTitle!=""){
				titleHtml='<div class="popupWindow_title">'+popTitle+'</div>';
			}
			var html='<div class="popupWindow"  style="cursor:pointer;">\
				<div class="popupWindow_hp"></div>\
				<div class="popupWindow_frame">\
					'+titleHtml+'\
					<div class="popupWindow_text">'+popHtml+'</div>\
					'+buttonHtml+'\
				</div>\
			</div>';
			$("body").append(html);
			//进行开始按钮点击后的回调事件
			popOkButton.buttonfunction=popOkButton.buttonfunction||function(){};
			this.okButton(popOkButton.buttonfunction);
			//进行取消按钮点击后的回调事件
			popCancelButton.buttonfunction=popCancelButton.buttonfunction||function(){};
			this.cancelButton(popCancelButton.buttonfunction);
			if(popFlash.flashSwitch){
				popFlash.flashfunction=popFlash.flashfunction||function(){};
				setTimeout(function(){Popup.Close();popFlash.flashfunction();},popFlash.flashTime);
			}
		},
		okButton:function(Event){//点击确认按钮后
			$(".popupWindow_button_ok").on("click",function(){
				Popup.Close();
				Event();
			});
		},
		cancelButton:function(Event){//点击取消按钮后
			$(".popupWindow_button_cancel").on("click",function(){
				Popup.Close();
				Event();
			});
		},
		Close:function(){//关闭弹窗
			$(".popupWindow").fadeOut(500,function(){
				$(".popupWindow").remove();
			});
		}
	}

	$('body').on('click', '.popupWindow', function(event) {
		event.preventDefault();
		var drag = $(".popupWindow_frame"),
            dragel = $(".popupWindow_frame")[0],
            target = event.target;
        if (dragel !== target && !$.contains(dragel, target)) {
            Popup.Close();
        }
		/* Act on the event */
	});


	window.xalert = function(message, title, callback) {
		setTimeout(function() {
			Popup.init({
				popTitle: title || null,
				popHtml:'<p>'+ message +'</p>',
				popOkButton:{
					buttonDisplay:true,
					buttonName:"好",
					buttonfunction: callback || function(){}
				},
				popCancelButton:null,
				popFlash:{
					flashSwitch:false
				}
			});
		}, 600);
	}
});

function browserOS(){
	var u = navigator.userAgent, app = navigator.appVersion;
	var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1;
	var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); 
	if(isAndroid) return 'android';
	if(isiOS) return 'ios';
	return 'pc';
}
