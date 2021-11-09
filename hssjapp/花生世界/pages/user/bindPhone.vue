<template>
	<view class="box">
		<view class="w-100 bc-white pt-10">
			<view class="contInput flex-center bt_line pr-20">
				<input type="text" v-model="oldcode" :placeholder="lang.bdph1" maxlength="6" />
				<text class="one-row font-main font-12" :disabled="!code_status1" v-if="!CodeSW1" @click="sendCode1()">{{lang.bdph2}}</text>
				<text class="one-row font-main font-12" disabled v-else>{{second1}}S{{lang.bdph3}}</text>
			</view>
			<view class="contInput bt_line">
				<input type="text" v-model="mobile" :placeholder="lang.bdph12" maxlength="11" />
			</view>
			<view class="contInput flex-center bt_line pr-20">
				<input type="text" v-model="code" :placeholder="lang.bdph10" maxlength="6" />
				<text class="one-row font-main font-12" :disabled="!code_status2" v-if="!CodeSW2" @click="sendCode2()">{{lang.bdph2}}</text>
				<text class="one-row font-main font-12" disabled v-else>{{second2}}S{{lang.bdph3}}</text>
			</view>
		</view>
		<view class="btn-cont pl-15 pr-15 pt-50 pb-50">
			<button class="btn" :disabled="!modify_status" @click="modifyPhone()">{{lang.bdph4}}</button>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	import user from "@/common/js/user.js"
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		data() {
			return {
				oldcode:"",
				second1:60,//倒计时
				CodeSW1:false,//验证码开关
				code_status1:true,
				mobile:"",
				code:"",
				second2:60,//倒计时
				CodeSW2:false,//验证码开关
				code_status2:true,
				modify_status:true,
			}
		},
		onLoad() {
			var self=this;
			uni.setNavigationBarTitle({
				title:self.lang.userInfo33
			});
		},
		computed:{
			...mapState(['userInfo','qiniu','lang']),
		},
		methods: {
			...mapMutations(["setUserInfo"]),
			sendCode1:function() {//获取验证码
				var self=this;
				let url=config.api + "/sms-unbind-code";
				self.code_status1=false;
				uni.request({
					url: url,
					data: {},
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						if(res.data.status==1){
							self.app._toastIcon(self.lang.bdph5);
							self.movetime1();
						}else{
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
						self.app._toast(self.lang.bdph6);
					},
					complete: (res) => {
						setTimeout(function(){
							self.code_status1=true;
						},300);
					}
				});
			},
			movetime1: function() {//倒计时几秒发送验证码的函数
				var self = this;
				self.CodeSW1 = true;
				var interval1 = setInterval(function() {
					if(self.second1 != 0) {
						self.second1--;
					} else {
						clearInterval(interval1);
						self.CodeSW1 = false;
						self.second1 = 60;
					};
				}, 1000);
			},
			sendCode2:function() {//获取验证码
				var self=this;
				if(self.mobile.trim().length!=11){
					return self.app._toast(self.lang.bdph7);
				};
				let send={
					Phone:self.mobile
				};
				let url=config.api + "/sms-bind-code";
				self.code_status2=false;
				uni.request({
					url: url,
					data: send,
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						if(res.data.status==1){
							self.app._toastIcon(self.lang.bdph5);
							self.movetime2();
						}else{
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
						self.app._toast(self.lang.bdph6);
					},
					complete: (res) => {
						setTimeout(function(){
							self.code_status2=true;
						},300);
					}
				});
			},
			movetime2: function() {//倒计时几秒发送验证码的函数
				var self = this;
				self.CodeSW2 = true;
				var interval2 = setInterval(function() {
					if(self.second2 != 0) {
						self.second2;
					} else {
						clearInterval(interval2);
						self.CodeSW2 = false;
						self.second2 = 60;
					};
				}, 1000);
			},
			modifyPhone:function(){
				var self=this;
				if(self.mobile.length!=11){
					return self.app._toast(self.lang.bdph8);
				};
				if(self.oldcode.trim().length==0){
					return self.app._toast(self.lang.bdph9);
				};
				if(self.code.trim().length==0){
					return self.app._toast(self.lang.bdph10);
				};
				let send={
					OldAuthCode:self.oldcode,
					NewPhone:self.mobile,
					NewAuthCode:self.code,
				};
				self.modify_status=false;
				let url=config.api + "/post.modifyphone";
				uni.request({
					url: url,
					data: send,
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.app._toastIcon(self.lang.bdph11);
							self.setUserInfo();
							setTimeout(function(){
								self.app.goBack();
							},1000);
						}else{
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						setTimeout(function(){
							self.modify_status=true;
						},300);
					}
				});
			},
		}
	}
</script>

<style>
	page{background-color: #F7F7F7;}
	.contInput{width: 100%;height: 50px;}
	.contInput input{width: 100%;height: 100%;color: #333333;font-size: 14px;padding: 0px 20px;}
	button.btn{background: linear-gradient(bottom,#FA6C00,#FFB500);}
</style>
