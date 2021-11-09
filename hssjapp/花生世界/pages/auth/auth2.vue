<template>
	<view class="box pl-25 pr-25 pt-15 pb-15">
		<view class="font-14 font-cl-3 pb-10">{{lang.at10}}	</view>
		<view class="font-14 font-cl-3 lh-25 op-7">{{lang.at11}}</view>
		<view class="font-14 font-cl-3 lh-25 op-7">{{lang.at12}}</view>
		<view class="w-100 pb-30">
			<view class="font-14 lh-25 op-7 font-red mt-5">{{lang.at13}}</view>
			<view class="pic mb-20">
				<video enable-play-gesture controls="true" poster="https://qny.zhangcai0710.club/wc.jpeg" src="/static/img/ac3625dffc4e453548bcc6c3fdfd71f7.mp4" @error="videoErrorCallback"></video>
			</view>
			<view class="pic" @click="uploadVideo()">
				<video :src="auth_s" enable-play-gesture v-if="auth_s" ></video>
				<view class="w-100" v-else>
					<view class="w-100 text-center"><image style="width: 52px;height: 36px;" src="../../static/img/0224b3220e5e67b58a7c12f52331839.png"></image> </view>
					<view class="w-100 font-12 font-cl-3 op-5 text-center pt-10">{{lang.at14}}</view>
				</view>
			</view>
		</view>
		<view class="btn-cont pt-10 pb-45">
			<button class="btn" :disabled="!auth_status" @click="go_auth()">{{lang.at15}}</button>
		</view>
		<view class="flex-center flex-j-center w-100 font-14 font-cl-3 hide">{{lang.at16}}<text class="font-dark-green" @click="app.closeMeOpen('auth/auth1?data='+JSON.stringify(data))">{{lang.at17}}</text></view>
		<view class="winPopup" v-if="loader">
			<loader type="loader2"></loader>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js"
	import upload from "@/common/js/upload.js"
	import loader from "@/components/loader/loader.vue";
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		components: {
			loader,
		},
		data() {
			return {
				data:"",
				auth_s:"",
				key_s:"",
				auth_status:true,
				loader:false,
			}
		},
		onLoad(e) {
			var self=this;
			self.data=JSON.parse(e.data);
		},
		computed:{
			...mapState(['userInfo','qiniu','lang']),
		},
		methods: {
			...mapMutations(["setUserInfo"]),
			videoErrorCallback(e){
				console.log(e)
			},
			uploadVideo:function(){
				var self = this;
				uni.chooseVideo({
					count: 1,
					maxDuration:6,
					// sourceType: ['camera', 'album'],
					sourceType:["camera"],
				    success: function(res) {
						self.loader=true;
						// console.log(res)
						const tempFilePaths = res.tempFilePath;
						uni.uploadFile({
							url: 'https://up-z'+self.qiniu.region+'.qiniup.com', 
							filePath: tempFilePaths,
							formData: {
								'token': self.qiniu.token,
							},
							name: 'file',
							success: (res) => {
								if(res.statusCode==200){
									res.data=JSON.parse(res.data);
									self.key_s=res.data.key;
									self.auth_s=tempFilePaths;
								};
								self.loader=false;
							}
						});
				    }
				});
			},
			go_auth:function(auth){
				var self=this;
				if(self.key_s.length==0){
					return self.app._toast(self.lang.at18);
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
							let val=uni.getStorageSync("userInfo");
							val.auth_status=1;
							uni.setStorageSync("userInfo",val);
							self.setUserInfo();
							self.app._toastIcon(self.lang.at19);
							setTimeout(function(){
								self.app.goBack(2);
							},2000);
						}else{
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						self.app._toast(self.lang.at20);
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
	.pic{width: 100%;height: 200px;border-radius: 4px;overflow: hidden;background-color: #F1F0F0;margin-top: 15px;display: flex;align-items: center;justify-content: center;}
	.pic .iconfont{font-size: 50px;color: #FFFFFF;}
	.pic video{width: 100%;height: 100%;position: relative;z-index: 1;}
	.btn-cont{width: 100%;}
	.btn-cont button.btn{width: 100%;height: 40px;line-height: 40px;border-radius: 4px;color: #FFFFFF;font-size: 14px;background:#FD9253;}
</style>
