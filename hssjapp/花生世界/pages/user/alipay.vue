<template>
	<view class="box">
		<view class="bc-white pl-15 pr-15">
			<input type="text" :placeholder="lang.userInfo27" v-model="account" disabled />
		</view>
		<view class="font-red font-12 pl-15 pr-15 pt-10">{{lang.userInfo28}}</view>
		<view class="pt-15 pb-15 pl-25 pr-25">
			<view class="pic" @click="uploadAvatar()">
				<image :src="auth" v-if="auth"></image>
				<view class="w-100" v-else>
					<view class="w-100 text-center"><i class="iconfont icon-jia"></i></view>
					<view class="w-100 font-12 font-cl-3 op-5 text-center pt-10">{{lang.userInfo29}}</view>
				</view>
			</view>
			<view class="btn-cont pt-50 pb-45">
				<button class="btn" :disabled="!auth_status" @click="go_auth()">{{lang.userInfo23}}</button>
			</view>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js"
	import upload from "@/common/js/upload.js"
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		data() {
			return {
				account:"",
				auth:"",
				key:"",
				auth_status:true,
			}
		},
		onLoad() {
			var self=this;
			if(self.userInfo.realName){
				self.account=self.userInfo.realName+":"+self.userInfo.Phone;
			}else{
				self.account=self.userInfo.Phone;
			}
			uni.setNavigationBarTitle({
				title:self.lang.buy9
			});
		},
		computed:{
			...mapState(['userInfo','qiniu','lang']),
		},
		methods: {
			...mapMutations(["setUserInfo"]),
			uploadAvatar:function(){
				var self=this;
				upload.upload3(self.qiniu,function(res){
					self.auth=res.url;
					self.key=res.key;
				},function(res){
					self.app._toast(res);
				});
			},
			go_auth:function(auth){
				var self=this;
				if(self.key.length==0){
					return self.app._toast(self.lang.userInfo30);
				};
				if(self.account.trim().length==0){
					return self.app._toast(self.lang.userInfo24);
				};
				let send={
					Account:self.account,
					QrCode:self.key,
				}
				self.auth_status=false;
				let url=config.api + "/bind-alipay";
				if(self.userInfo.IsBindAlipay==1){
					url=config.api + "/modify-alipay";
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
							val.IsBindAlipay=1;
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
	.pic{width: 100%;height: 200px;border-radius: 2px;background-color: #F1F0F0;margin-top: 15px;display: flex;align-items: center;justify-content: center;}
	.pic .iconfont{font-size: 50px;color: #FFFFFF;}
	.pic image{width: 100%;height: 100%;position: relative;z-index: 1;}
	.btn-cont{width: 100%;}
	.btn-cont button.btn{width: 100%;height: 40px;line-height: 40px;border-radius: 4px;color: #FFFFFF;font-size: 14px;background:linear-gradient(bottom,#FA6C00,#FFB500);}
</style>
