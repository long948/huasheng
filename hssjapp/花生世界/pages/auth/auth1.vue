<template>
	<view class="box pl-25 pr-25 pt-15 pb-15">
		<view class="font-14 font-cl-3 pb-10">手势认证</view>
		<!-- <view class="font-14 font-cl-3 lh-25 op-7">1.在白纸上写下“花生世界”及自己的姓名、当前日期；</view> -->
		<view class="font-14 font-cl-3 lh-25 op-7">1.手持身份证露脸拍照，上传；</view>
		<view class="w-100 flex-center flex-j-between pb-30">
			<view class="pic" @click="uploadAvatar()">
				<image :src="auth_s" v-if="auth_s"></image>
				<view class="w-100" v-else>
					<view class="w-100 text-center"><i class="iconfont icon-jia"></i></view>
					<view class="w-100 font-12 font-cl-3 op-5 text-center pt-10">手持照，手势为OK</view>
				</view>
			</view>
		</view>
		<view class="btn-cont pt-10 pb-45">
			<button class="btn" :disabled="!auth_status" @click="go_auth()">提交认证</button>
		</view>
		<!-- <view class="flex-center flex-j-center w-100 font-14 font-cl-3">手势认证有问题？切换到<text style="color: #FD9253;" @click="app.closeMeOpen('auth/auth2?data='+JSON.stringify(data))">视频认证</text></view> -->
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
				data:"",
				auth_s:"",
				key_s:"",
				auth_status:true,
			}
		},
		onLoad(e) {
			var self=this;
			self.data=JSON.parse(e.data);
		},
		computed:{
			...mapState(['userInfo','qiniu']),
		},
		methods: {
			...mapMutations(["setUserInfo"]),
			uploadAvatar:function(){
				var self=this;
				upload.upload2(self.qiniu,function(res){
					self.auth_s=res.url;
					self.key_s=res.key;
				},function(res){
					self.app._toast(res);
				});
			},
			go_auth:function(auth){
				var self=this;
				if(self.key_s.length==0){
					return self.app._toast("请上传手势照！");
				};
				let send=self.data;
				send.video=self.key_s;
				self.auth_status=false;
				let url=config.api + "/auth-member";
				uni.request({
					url: url,
					data: send,
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.setUserInfo();
							self.app._toastIcon("认证成功");
							setTimeout(function(){
								self.app.goBack(2);
							},2000);
						}else{
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						self.app._toast("认证失败");
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
	page{background-color: #FFFFFF;}
	.box{border-top: 10px solid #F7F7F7;}
	.pic{width: 100%;height: 200px;border-radius: 2px;background-color: #F1F0F0;margin-top: 15px;display: flex;align-items: center;justify-content: center;}
	.pic .iconfont{font-size: 50px;color: #FFFFFF;}
	.pic image{width: 100%;height: 100%;position: relative;z-index: 1;}
	.btn-cont{width: 100%;}
	.btn-cont button.btn{width: 100%;height: 40px;line-height: 40px;border-radius: 4px;color: #FFFFFF;font-size: 14px;background:linear-gradient(bottom,#FA6C00,#FFB500);}
</style>
