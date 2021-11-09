<template>
	<view class="box">
		<view class=" flex-center flex-j-center w-100 pt-20 pb-15">
			<view class="head-portrait" @click="modifyAvatar()">
				<image :src="userInfo.Avatar"></image>
				<view class="font-black lh-30 font-14 font-w-b">{{lang.userInfo1}}</view>
			</view>
		</view>
		<view class="w-100 bc-white mb-15">
			<view class="cont-list flex-center flex-j-between bt_line pl-25 pr-25" @click="app.showOpen('user/nickName')">
				<text class="one-row font-black font-14 font-w-b">{{lang.userInfo2}}</text>
				<view class="value flex-center nowrap font-12">{{userInfo.NickName}}<i class="iconfont icon-you ml-10"></i></view>
			</view>
			<view class="cont-list flex-center flex-j-between bt_line pl-25 pr-25" @click="app.showOpen('user/bindPhone')">
				<text class="one-row font-black font-14 font-w-b">{{lang.userInfo3}}</text>
				<view class="value flex-center nowrap font-12">{{userInfo.SubPhone}}<i class="iconfont icon-you ml-10"></i></view>
			</view>
			<view class="cont-list flex-center flex-j-between pl-25 pr-25" @click="userInfo.auth_status==0?app.showOpen('auth/auth'):userInfo.auth_status==2?app.showOpen('index/AuthDetails'):''">
				<text class="one-row font-black font-14 font-w-b">{{lang.userInfo4}}</text>
				<view class="value flex-center nowrap font-12">{{userInfo.auth_status==0?lang.userInfo5:userInfo.auth_status==1?lang.userInfo6:userInfo.auth_status==2?lang.userInfo7:lang.userInfo8}} <i class="iconfont icon-you ml-10"></i></view>
			</view>
		</view>
		<view class="w-100 bc-white mb-15">
			<view class="cont-list flex-center flex-j-between bt_line pl-25 pr-25" @click="app.showOpen('login/modifyLogin')">
				<text class="one-row font-black font-14 font-w-b">{{lang.userInfo9}}</text>
				<view class="value flex-center nowrap font-12">{{lang.userInfo10}}<i class="iconfont icon-you ml-10"></i></view>
			</view>
			<view class="cont-list flex-center flex-j-between pl-25 pr-25" @click="app.showOpen('login/modifyPay')" v-if="userInfo.IsSetPayPass==1">
				<text class="one-row font-black font-14 font-w-b">{{lang.userInfo11}}</text>
				<view class="value flex-center nowrap font-12">{{lang.userInfo10}}<i class="iconfont icon-you ml-10"></i></view>
			</view>
			<view class="cont-list flex-center flex-j-between pl-25 pr-25" @click="app.showOpen('login/setPay')" v-else>
				<text class="one-row font-black font-14 font-w-b">{{lang.userInfo11}}</text>
				<view class="value flex-center nowrap font-12">{{lang.userInfo12}}<i class="iconfont icon-you ml-10"></i></view>
			</view>
		</view>
		<view class="w-100 bc-white">
			<view class="cont-list flex-center flex-j-between bt_line pl-25 pr-25" @click="app.showOpen('user/alipay')">
				<text class="one-row font-black font-14 font-w-b">{{lang.userInfo13}}</text>
				<view class="value flex-center nowrap font-12">
					<block v-if="userInfo.IsBindAlipay==1">
						<text>去修改</text>
						<i class="iconfont icon-you ml-10"></i>
					</block>
					<block v-else>
						<text style="color: #FA6400;">{{lang.userInfo15}}</text>
						<i class="iconfont icon-you ml-10"></i>
					</block>
				</view>
			</view>
			<view class="cont-list flex-center flex-j-between bt_line pl-25 pr-25" @click="app.showOpen('user/address')">
				<text class="one-row font-black font-14 font-w-b">{{lang.userInfo17}}</text>
				<view class="value flex-center nowrap font-12">
					<block v-if="userInfo.IsBindAddress==1">
						<text>去修改</text>
						<i class="iconfont icon-you ml-10"></i>
					</block>
					<block v-else>
						<text style="color: #FA6400;">{{lang.userInfo15}}</text>
						<i class="iconfont icon-you ml-10"></i>
					</block>
				</view>
			</view>
			<!-- <view class="cont-list flex-center flex-j-between pl-25 pr-25" @click="choiceLanguage()">
				<text class="one-row font-black font-14 font-w-b">语言</text>
				<view class="value flex-center nowrap font-12">
					<text v-text="name">中文</text>
					<i class="iconfont icon-you ml-10"></i>
				</view>
			</view> -->
		</view>
		<view class="w-100 pl-15 pr-15 pt-35 pb-35">
			<button class="btn" @click="quitLogin()">{{lang.userInfo18}}</button>
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
				token:"",
				name:"中文",
			}
		},
		onLoad() {
			var self=this;
			var userLang = uni.getStorageSync("userLang");
			if(!userLang){
				const sys = uni.getSystemInfoSync();
				userLang = sys.language;
			};
			if(userLang.substring(0,2) == 'zh'){
				self.name="中文";
			}else{
				self.name="English";
			};
			uni.setNavigationBarTitle({
				title:self.lang.userInfo32
			});
		},
		computed:{
			...mapState(['userInfo','qiniu','lang']),
		},
		methods: {
			...mapMutations(["setUserInfo","logout","setLanguage"]),
			choiceLanguage:function(){
				let self=this;
				uni.showActionSheet({
					title:'语言切换',
					itemList: ['中文', 'English'],
					success: (e) => {
						if(e.tapIndex==0){//中文
							self.name="中文";
							self.setLanguage('zh');
						}else{//English
							self.name="English";
							self.setLanguage('en');
						};
						uni.setNavigationBarTitle({
							title:self.lang.userInfo32
						});
					}
				})
			},
			modifyAvatar:function(){
				var self=this;
				upload.upload3(self.qiniu,function(res){
					self.uploadAvatar(res.key);
				},function(res){
					self.app._toast(res);
				});
				return;
				uni.chooseImage({
					success: (chooseImageRes) => {
						const tempFilePaths = chooseImageRes.tempFilePaths;
						uni.uploadFile({
							url: "https://up-z"+self.qiniu.region+".qiniup.com",
							filePath: tempFilePaths[0],
							name: 'file',
							formData: {
								'token': self.qiniu.token,
							},
							success: (e) => {
								if (e.statusCode == 200) {
									let data = JSON.parse(e.data);
									self.uploadAvatar(data.key);
								}else{
									self.app._toast( self.lang.userInfo19 );
								};
							},
							fail: (err) => {
								self.app._toast(self.lang.userInfo19);
							}
						});
					}
				});
			},
			uploadAvatar:function(e){
				var self=this;
				let url=config.api + "/member-modify-avatar";
				let send={
					Avatar:e,
				};
				uni.showLoading({title: self.lang.userInfo20});
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
							self.app._toastIcon(self.lang.userInfo21);
						};
					},
					fail: (res) => {
						self.app._toastIcon(self.lang.userInfo19);
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						setTimeout(function(){
							uni.hideLoading();
						},2000);
					}
				});
			},
			quitLogin:function(){
				var self=this;
				self.logout();
				uni.reLaunch({
					url:'/pages/login/login'
				});
			}
		}
	}
</script>

<style>
	page{background-color: #F7F7F7;}
	.cont-list{width: 100%;height: 50px;}
	.cont-list .value{color: rgba(51,51,51,0.6);}
	.cont-list .value .iconfont{color: rgba(51,51,51,0.6);font-size: 18px;opacity: 0.5;}
	.head-portrait image{width: 48px;height: 48px;display: block;border-radius: 50%;}
	button.btn{background: linear-gradient(bottom,#FA6C00,#FFB500);line-height: 44px;height: 44px;color: #FFFFFF;font-size: 14px;}
</style>
