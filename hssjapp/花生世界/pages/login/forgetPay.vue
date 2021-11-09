<template>
	<view class="box">
		<view class="content">
			<view class="logo-cont text-center">
				<image class="logo" mode="widthFix" src="../../static/img/e3f181eacb59b9c2a7b47ad1070c5fa.png"></image>
			</view>
			<view class="form-cont">
				<view class="contInput flex-center">
					<image mode="widthFix" src="../../static/img/b5f9899600da1fea96ae6924a37edbe.png"></image>
					<text class="split-line"></text>
					<input type="text" :placeholder="lang.fgp1" v-model="mobile" maxlength="11" />
				</view>
				<view class="contInput flex-center pr-10">
					<input type="text" :placeholder="lang.fgp2" v-model="code" maxlength="6" />
					<!-- <button class="get-code-btn" :disabled="!code_status" v-if="!CodeSW" @click="sendCode()">{{lang.fgp3}}</button>
					<button class="get-code-btn" disabled v-else>{{second}}S{{lang.fgp4}}</button> -->
					
					<button class="get-code-btn" :disabled="!code_status" v-if="code_type==1" @click="sendCode()">{{lang.fgl2}}</button>
					<button class="get-code-btn" disabled v-else-if="code_type==2">发送中</button>
					<button class="get-code-btn" disabled v-else-if="code_type==3">{{second}}S{{lang.fgl3}}</button>
					
				</view>
				<view class="contInput flex-center">
					<image mode="widthFix" src="../../static/img/eca5af10099d156eedbf1c92cffa733.png"></image>
					<text class="split-line"></text>
					<input type="password" :placeholder="lang.fgp5" v-model="pass" maxlength="20" />
				</view>
				<view class="contInput flex-center">
					<image mode="widthFix" src="../../static/img/a50ae33f8439ac9db1b66a52c1e6e13.png"></image>
					<text class="split-line"></text>
					<input type="password" :placeholder="lang.fgp6" v-model="pass1" maxlength="20" />
				</view>
			</view>
			<view class="btn-cont pl-45 pr-45 pt-50">
				<button class="btn" @click="forgetLogin()" :disabled="!forget_status">{{lang.fgp7}}</button>
			</view>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	import user from "@/common/js/user.js"
	import sms from "@/common/js/sms.js"
	import Captcha from "@/common/js/TCaptcha.js";
	
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		data() {
			return {
				mobile:"",
				pass:"",
				pass1:"",
				code:"",
				second:60,//倒计时
				CodeSW:false,//验证码开关
				forget_status:true,
				code_status:true,
				code_type:1,//1-未发送，2发送完成，3发送中
			}
		},
		onLoad() {
			var self = this;
			self.initCode();
		},
		onBackPress(){
			return Captcha.hide(); 
		},
		computed:{
			...mapState(['lang']),
		},
		methods: {
			forgetLogin:function(){
				var self=this;
				let send={
					mobile:self.mobile,
					pass:self.pass,
					pass1:self.pass1,
					code:self.code,
				};
				self.forget_status=false;
				user.forgetPay(send,function(res){
					if(res.data.status==1){
						self.app._toastIcon(self.lang.fgp8);
						setTimeout(function(){
							self.app.goBack();
						},300);
					}else{
						self.app._toast(res.data.message);
					}
					self.forget_status=true;
				},function(status,msg){
					self.app._toast(msg);
					self.forget_status=true;
				});
			},
			sendCode:function() {//获取验证码
				var self=this;
				if(!(self.mobile) || self.mobile.length!=11){
					return self.app._toast("请输入正确的手机号！");
				};
				Captcha.show();
			},
			movetime: function() {//倒计时几秒发送验证码的函数
				var self = this;
				self.CodeSW = true;
				var interval = setInterval(function() {
					if(self.second != 0) {
						self.second--;
					} else {
						clearInterval(interval);
						self.CodeSW = false;
						self.second = 60;
						self.code_type=1;
						self.code_status=true;
					};
				}, 1000);
			},
			initCode:function(){
				var self=this;
				Captcha.init(function(result) {
					console.log(result);
					if (result.status == 1) {
						let send={
							mobile:self.mobile,
							ticket:result.ticket,
							randstr:result.randstr,
						};
						self.code_status=false;
						self.code_type=2;
						sms.forgetPaySMS(send, function(res) {
							if(res.data.status==1){
								self.app._toastIcon(self.lang.fgp9);
								self.movetime();
								self.code_type=3;
							}else{
								self.code_type=1;
								self.code_status=true;
								self.app._toast(res.data.message);
							};
							self.code_status=true;
						}, function(status, msg) {
							self.app._toast(msg);
							self.code_status=true;
							self.code_type=1;
						});
					};
				});
			},
		}
	}
</script>

<style>
	@import url("@/common/css/login.css");
	.logo-cont{
		padding-top: 56px;
		padding-bottom: 10px;
	}
	.logo{
		width: 130px;
		height: 150px;
	}
	
</style>
