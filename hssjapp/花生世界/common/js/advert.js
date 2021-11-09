// var adsModule = uni.requireNativePlugin("SjUniPlugin-SjModule")  //     SSKJAbcUniPlugin-AbcModule
var adsModule = uni.requireNativePlugin("AdsModule") 
const splashUnitId = "7D5239D8D88EBF9B6D317912EDAC6439" // 开屏广告单元id
const insetUnitId = "1D273967F51868AF2C4E080D496D06D0" // 插屏图片广告单元id
const rewardVideoUnitId = "09A177D681D6FB81241C3DCE963DCB46" // 视频视频广告单元id
const fullVideoUnitId = "D879C3DED01D5CE319CD2751474BA8E4" // 全屏广告单元id


export default {
	gotoSplashPage() { // 开屏
		try{
			console.log("开屏");
			// adsModule.gotoSplashPage({'adUnitId': splashUnitId}, (res) => {
			// 	console.log("isEnded=" + res.isEnded + "key=" + res.key);
			// }, (err) => {
			// 	console.log(err.code +"-"+ err.errMsg)
			// });
			adsModule.gotoSplashPage();
		}catch(e){
			// console.log(e)
		}
	},
	gotoInsetAd() { // 插屏图片
		adsModule.gotoInsetAd({'adUnitId': insetUnitId}, (res) => {
			console.log("isEnded=" + res.isEnded + "key=" + res.key);
		}, (err) => {
			console.log(err.code +"-"+ err.errMsg)
		});
	},
	gotoFullVideo() { // 全屏视频
		adsModule.gotoFullVideo({'adUnitId': fullVideoUnitId}, (res) => {
			console.log("isEnded=" + res.isEnded + "key=" + res.key);
		}, (err) => {
			console.log(err.code +"-"+ err.errMsg)
		});
	},
	gotoRewardVideo(config) { // 激励视频
		// adsModule.gotoRewardVideo({'adUnitId': rewardVideoUnitId}, (res) => {
		// 	console.log("isEnded=" + res.isEnded + "key=" + res.key);
		// 	if(res.isEnded){
		// 		let url=config.api + "/user-sign";
		// 		let send={
		// 			key:res.key
		// 		};
		// 		console.log(url);
		// 		console.log(send);
		// 		uni.request({
		// 			url: url,
		// 			data: send,
		// 			method: "post",
		// 			header: {Authorization: config.getToken()},
		// 			success: res => {
		// 				console.log(res);
		// 				// console.log(JSON.stringify(res));
		// 				config.api_status(res);
		// 				if(res.data.status==1){
							
		// 				}else{
							
		// 				};
		// 			},
		// 			fail: (res) => {
		// 				console.log(res);
		// 			},
		// 			complete: (res) => {}
		// 		});
		// 	}
			
		// }, (err) => {
		// 	console.log(err.code +"-"+ err.errMsg)
		// });
		
		adsModule.gotoRewardVideo((res) => {
			if(res.isEnded){
				let url=config.api + "/user-sign";
				let send={
					key:res.key
				};
				console.log(url);
				console.log(send);
				uni.request({
					url: url,
					data: send,
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						console.log(res);
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							
						}else{
							
						};
					},
					fail: (res) => {
						console.log(res);
					},
					complete: (res) => {}
				});
			}
		}, (err) => {
			uni.showModal({
				title: "err" + err.code + err.errMsg
			})
		});
	},
	testAsyncFunc() {
		// 调用异步方法
		adsModule.testAsyncFunc({
			'name': 'unimp',
			'age': 1
		}, (res) => {
			console.log("a=" + res.code)
		});
	},
	testSyncFunc() {
		// 调用同步方法
		var ret = adsModule.testSyncFunc({
			'name': 'unimp',
			'age': 1
		});
		console.log("b=" + res.code)
	},
	gotoNativePage() {
		adsModule.gotoNativePage();
	}
}