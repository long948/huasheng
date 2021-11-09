import Vue from 'vue'
import App from './App'
import $app from './common/js/base.js'
import store from './store'
import config from "@/common/js/config.js"

// import advert from "@/common/js/advert.js";

Vue.prototype.app = $app;
Vue.prototype.$store = store;
Vue.config.productionTip = false

import {
	mapState,
	mapMutations
} from 'vuex'
App.mpType = 'app'

const app = new Vue({
	store,
    ...App,
	data() {
		return {
			
		}
	},
	created() {
		var self=this;
		let userToken = uni.getStorageSync('userToken') || '';
		let userInfo = uni.getStorageSync('userInfo') || '';
		if(userToken){
			self.setQiniu();
			self.login(userToken);
			self.setUserInfo();
		}else{
			// uni.reLaunch({
			// 	url:'/pages/login/login'
			// });
		};
		// advert.gotoSplashPage(config);
	},
	mounted:function() {
		var self = this;
		self.getAPP();
	},
	methods: {
		...mapMutations(["login","logout","setUserInfo","setQiniu"]),
		getAPP:function(){
			let self=this;
			let url=config.api + "/get.app.info";
			uni.request({
				url: url,
				data: {},
				method: "get",
				header: {Authorization: config.getToken()},
				success: res => {
					if(res.data.status==1){
						let url=res.data.data;
						$app.shareURL=url.shareUrl+"?code=";
					}else{
						
					};
				},
				fail: (res) => {
					console.log(JSON.stringify(res));
				},
				complete: (res) => {
					self.loader=false;
				}
			});
		}
	},
})
app.$mount()
