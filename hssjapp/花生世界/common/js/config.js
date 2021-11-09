// const api = "http://api-test.hssjapp.com";//测试
// const shareURL="http://h5-test.hssjapp.com"//测试
const api = "https://api.hssjapp.com";//正式
const shareURL="https://share-h5.hssjpnt.top"



if(api == "http://api-test.hssjapp.com"){
	console.log("测试环境!");
}

export default {
	getToken:function() {
		let Token=uni.getStorageSync("userToken");
		if(Token){
			return Token;
		}else{
			return " ";
		};
	},
	api_status:function(res){
		var self=this;
		// 1001-1004 登录过期
		if(res.data.status == 20003 || res.data.status == 20005 || res.data.status == 20003 || res.data.status == 20004 || res.data.status == 20001){
			uni.reLaunch({
				url:'/pages/login/login'
			});
		}else if(res.data.status == 100000){
			let p_num=uni.getStorageSync("pop")||1;
			var buttons = ["确定"];
			let msg=res.data.message?res.data.message:'系统正在维护中'
			if(p_num==1){
				uni.setStorageSync("pop",2);
				plus.nativeUI.confirm(msg, function(ev) {
					if(ev.index==0){
						uni.setStorageSync("pop",1);
						uni.reLaunch({
							url:'/pages/login/login'
						});
					};
				}, "", buttons);
			};
		};
	},
	api,
	shareURL,
};
