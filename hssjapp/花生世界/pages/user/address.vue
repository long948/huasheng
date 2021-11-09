<template>
	<view class="box">
		<view class="bc-white pl-15 pr-15">
			<input type="text" :placeholder="lang.userInfo22" v-model="account" />
		</view>
		<view class="pt-15 pb-15 pl-25 pr-25">
			<view class="btn-cont pt-50">
				<button class="btn" :disabled="!auth_status" @click="go_auth()">{{lang.userInfo23}}</button>
			</view>
			<view class="font-14 pt-20 pb-20 font-red">请绑定ERC20链类型的USDT地址!</view>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js"
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		data() {
			return {
				account:"",
				auth_status:true,
			}
		},
		onLoad() {
			var self=this;
			uni.setNavigationBarTitle({
				title:self.lang.trade35
			});
		},
		computed:{
			...mapState(['qiniu','userInfo','lang']),
		},
		methods: {
			...mapMutations(["setUserInfo"]),
			go_auth:function(auth){
				var self=this;
				if(self.account.trim().length==0){
					return self.app._toast(self.lang.userInfo24);
				};
				let send={
					Account:self.account
				};
				self.auth_status=false;
				let url=config.api + "/bind-adress";
				if(self.userInfo.IsBindAddress==1){
					url=config.api + "/modify-adress";
				};
				uni.request({
					url: url,
					data: send,
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							let val=uni.getStorageSync("userInfo");
							val.IsBindAddress=1;
							uni.setStorageSync("userInfo",val);
							self.setUserInfo();
							self.app._toastIcon(self.lang.userInfo25);
							setTimeout(function(){
								self.app.goBack();
							},2000);
						}else{
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						self.app._toast(self.lang.userInfo26);
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						self.auth_status=true;
					}
				});
			},
		}
	}
</script>

<style>
	page{background-color: #F7F7F7;}
	input{width: 100%;height: 50px;font-size: 14px;color: #333333;}
	.btn-cont{width: 100%;}
	.btn-cont button.btn{width: 100%;height: 40px;line-height: 40px;border-radius: 4px;color: #FFFFFF;font-size: 14px;background:linear-gradient(bottom,#FA6C00,#FFB500);}
</style>
