<script>
	import update from "common/js/update.js"
	var _cp=function(text){
		var osName = plus.os.name;
		if(plus.os.name == "Android") {
			var Context = plus.android.importClass("android.content.Context");
			var main = plus.android.runtimeMainActivity();
			var clip = main.getSystemService(Context.CLIPBOARD_SERVICE);
			plus.android.invoke(clip, "setText", text);
		} else if(osName == "iOS") {
			var UIPasteboard = plus.ios.importClass("UIPasteboard");
			var generalPasteboard = UIPasteboard.generalPasteboard();
			// 设置/获取文本内容:
			generalPasteboard.setValueforPasteboardType(text, "public.utf8-plain-text");
		}
	}
	export default {
		onLaunch: function() {
			var args= plus.runtime.arguments;
			if(args){//处理args参数，如直达到某新页面等
				let val="฿" + args;
				_cp(val);
				console.log(args)
			}
			plus.globalEvent.addEventListener('newintent', (e)=>{  
			    
			});
			update.check();
		},
		onShow: function() {
			// console.log(plus.push.getClientInfo().clientid);
			// plus.push.addEventListener("receive", function(msg) {
			// 	plus.push.createMessage(msg.content, msg.payload, {title: msg.title});
			// }, false);
		},
		onHide: function() {

		}
	}
</script>

<style>
	@import url("common/css/base.css");
	@import url("common/css/iconfont.css");
	.font-main{color: #FFB500 !important;}
	.font-dark-main{color: #FA6C00 !important;}
	.bc-main{background-color: #FFB500 !important;}
	.font-black{color: #000000 !important;}
	.font-white{color: #FFFFFF !important;}
	.bc-black{background-color: #000000 !important;}
	.bc-white{background-color: #FFFFFF !important;}
	.font-green {
		color: #A8FA9E !important;
	}
	.font-dark-green{
		color: #519B08 !important;
		/* color: #FFB500; */
	}
	.font-red{
		color: #FF0000 !important;
	}
	.font-dark-red{
		color: #E02020 !important;
	}
	.font-plain{
		color: rgba(255,255,255,0.7) !important;
	}
	.font-dark-grey{
		color: rgba(60,60,60,0.8) !important;
	}
	.font-grey{
		color: #3C3C3C !important;
	}
	.font-cl-9{
		color: #999999 !important;
	}
	.font-cl-6{
		color: #666666 !important;
	}
	.font-cl-3{
		color: #333333 !important;
	}
	
	button.btn {
		background: url(static/img/809735afa529b3db4a3684a30726100.png) no-repeat;
		background-size: 100% 100%;
		line-height: 40px;
		height: 46px;
		width: 100%;
		color: #FFFFFF;
		font-size: 16px;
	}
	button[disabled]{
		background: url(static/img/024ca03cac8de0ef799adf154da0fd9.png) no-repeat;
		background-size: 100% 100%;
		background-color: transparent !important;
		color: #FFFFFF !important;
	}
	button.get-code-btn{
		/* background: url(static/img/bbcef290a7790a7be771a1d68c98dad.png) no-repeat;
		background-size: 100% 100%; */
		background: linear-gradient(bottom,#FA6C00,#FFB500);
		line-height: 23px;
		height: 23px;
		width: 75px;
		color: #FFFFFF;
		font-size: 12px;
		text-align: center;
		padding: 0px;
		margin: 0px;
		display: inline-block;
	}
	/* 等级 弹窗 */
	.grade-content{position: relative;}
	.grade-content .content{max-height: 80vh;overflow-y: scroll;}
	.grade-content .content .progress{width: 100%;height: 16px;border-radius: 8px;background-color: #FDEDC7;}
	.grade-content .content .progress .val{height: 100%;border-radius: 8px;background: linear-gradient(bottom,#FA6C00,#FFB500);display: inline-block;}
	.grade-content .content .form-cont{width: 100%;}
	.grade-content .content .form-cont .ct{width: 32%;height: 25px;line-height: 25px;border-radius: 2px;background-color: #F2F2F2;font-size: 12px;color: rgba(60,60,60,0.8);margin-bottom: 4px;}
	.grade-content .content .form-cont .ct.ct-title{background-color: #FFB500;color: rgba(255,255,255,0.8);}
	.grade-content .grade-popup-close{position: absolute;top: -30px;right: 0px;}
	.grade-content .grade-popup-close .iconfont{font-size: 25px;}
	.grade-content img{width: 100%;}
	
	/* 分享 */
	.share-content{width: 100%;height: 100vh;overflow: hidden;}
	.share-content image.bc{width: 100%;height: 100%;}
	.share-content .share-cont{width: 100%;position: absolute;left: 0px;bottom: 0px;}
	.share-content .share-cont button.btn{width: 183px;}
	.share-content .page-close{position: absolute;top: 50px;right: 20px;}
	.share-content .page-close .iconfont{font-size: 25px}
	
	
	
	/* 交易密码 */
	.payIn{width: 100%;height: 44px;border-bottom: 1px solid #FAFAFA;font-size: 14px;color: #333333;}
	
	img{width: 100%;}
	.base-no-list{
		width: 100%;height: 30vh;display: flex;align-items: flex-end;justify-content: center;
	}
</style>
