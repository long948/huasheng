import Vue from 'vue'
import Vuex from 'vuex'
import base from "@/common/js/base.js"
import config from "@/common/js/config.js"
import coin from "@/common/js/coin.js"
Vue.use(Vuex)
try{
	var userLang = uni.getStorageSync("userLang");
	// console.log(uni.getSystemInfoSync());
	if(!userLang){
		const sys = uni.getSystemInfoSync();
		// console.log(sys)
		userLang = sys.language;
	};
	if(userLang.substring(0,2) == 'zh'){
		var lang = require('../language/zh.js');
	}else{
		var lang = require('../language/en.js');
	};
	// console.log(lang)
} catch(e) {
	console.log(e)
}

const store = new Vuex.Store({
    state: {
        hasLogin: false,
		userInfo:"",//用户信息
		qiniu:{
			token:"",
			domain:"",
			region:"",
		},
		assets:{
			power:"----",
			output:"----",
			total_assets:"----",
			total_assets_cny:"----",
		},
		RewardInfo:{
			is_giveAway:0,
		},
		lang : lang ,// 语言
		coinList:[],
    },
    mutations: {
        login(state,info) {
            state.hasLogin = true;
			var getCoin = async function(){
				try{
					let list=await coin.getlist();
					state.coinList=list;
				}catch(e){
					console.log(e);
					//TODO handle the exception
				}
			}
			getCoin();
			uni.setStorageSync("userToken",info);
        },
        logout(state) {
			state.hasLogin = false;
			state.userInfo = "";
			uni.removeStorageSync("userToken");
			uni.removeStorageSync("userInfo");
        },
		setUserInfo(state) {
			let val=uni.getStorageSync("userInfo");
			state.userInfo=val;
			let url=config.api + "/member-info";
			state.userInfo=uni.getStorageSync("userInfo")||"";
			uni.request({
				url: url,
				data: {},
				method: "get",
				header: {Authorization: config.getToken()},
				success: res => {
					// console.log(res)
					// console.log(JSON.stringify(res));
					// config.api_status(res);
					if(res.data.status==1){
						if(res.data.data.Avatar==""){
							res.data.data.Avatar='../../static/img/bacc1bf65f6c0e8ed8c29f22df3743b.png';
						};
						state.userInfo=res.data.data;
						// state.userInfo.IsFrozenCTC=1;//
						// console.log(state.userInfo);
						uni.setStorageSync("userInfo",state.userInfo);
					};
				},
				fail: (res) => {
					console.log(JSON.stringify(res));
				},
				complete: (res) => {}
			});
			
		},
		set_assets(state){
			let url=config.api + "/self";
			uni.request({
				url: url,
				data: {},
				method: "get",
				header: {Authorization: config.getToken()},
				success: res => {
					// console.log(JSON.stringify(res));
					// config.api_status(res);
					if(res.data.status==1){
						res.data.data.power=base._toFixed(res.data.data.power,4);
						res.data.data.output=base._toFixed(res.data.data.output,4);
						res.data.data.total_assets=base._toFixed(res.data.data.total_assets,4);
						res.data.data.total_assets_cny=base._toFixed(res.data.data.total_assets_cny,4);
						state.assets=res.data.data;
					}else{
						console.log(JSON.stringify(res));
					};
				},
				fail: (res) => {
					console.log(JSON.stringify(res));
				},
				complete: (res) => {}
			});
		},
		setQiniu(state) {
			let url=config.api + "/qiniu-upload";
			uni.request({
				url: url,
				data: {},
				method: "get",
				header: {Authorization: config.getToken()},
				success: res => {
					// console.log(JSON.stringify(res));
					// config.api_status(res);
					if(res.data.status==1){
						state.qiniu = res.data.data;
					};
				},
				fail: (res) => {
					console.log(JSON.stringify(res));
				},
				complete: (res) => {}
			});
		},
		getReward(state){
			let val=uni.getStorageSync("RewardInfo");
			state.RewardInfo=val;
			let url=config.api + "/user-auth-is-giveAway";
			uni.request({
				url: url,
				data: {},
				method: "get",
				header: {Authorization: config.getToken()},
				success: res => {
					// console.log(JSON.stringify(res));
					// config.api_status(res);
					if(res.data.status==1){
						state.RewardInfo=res.data.data;
						uni.setStorageSync("RewardInfo",res.data.data);
					};
				},
				fail: (res) => {
					console.log(JSON.stringify(res));
				},
				complete: (res) => {}
			});
		},
		setLanguage(state,info){
			uni.setStorageSync("userLang",info);
			if(info == 'zh'){
				state.lang = require('../language/zh.js');
			}else{
				state.lang = require('../language/en.js');
			};
		},
		
    }
})

export default store
