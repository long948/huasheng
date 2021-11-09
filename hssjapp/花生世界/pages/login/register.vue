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
					<input type="number" :placeholder="lang.rg1" v-model="mobile" maxlength="11" />
				</view>
				<view class="contInput flex-center">
					<image mode="widthFix" src="../../static/img/eca5af10099d156eedbf1c92cffa733.png"></image>
					<text class="split-line"></text>
					<input type="password" :placeholder="lang.rg2" v-model="pass" maxlength="20" />
				</view>
				<view class="contInput flex-center">
					<image mode="widthFix" src="../../static/img/a50ae33f8439ac9db1b66a52c1e6e13.png"></image>
					<text class="split-line"></text>
					<input type="password" :placeholder="lang.rg3" v-model="pass1" maxlength="20" />
				</view>
				<!-- <view class="contInput flex-center">
					<image mode="widthFix" src="../../static/img/eca5af10099d156eedbf1c92cffa733.png"></image>
					<text class="split-line"></text>
					<input type="password" placeholder="请设置交易密码" v-model="pay" maxlength="20" />
				</view>
				<view class="contInput flex-center">
					<image mode="widthFix" src="../../static/img/a50ae33f8439ac9db1b66a52c1e6e13.png"></image>
					<text class="split-line"></text>
					<input type="password" placeholder="请再次确认交易密码" v-model="pay1" maxlength="20" />
				</view> -->
				<view class="contInput flex-center pr-10">
					<input type="text" :placeholder="lang.rg4" v-model="code" maxlength="6" />
					<!-- <button class="get-code-btn" :disabled="!code_status" v-if="!CodeSW" @click="sendCode()">{{lang.rg5}}</button>
					<button class="get-code-btn" disabled v-else>{{second}}S{{lang.rg6}}</button> -->
					<button class="get-code-btn" :disabled="!code_status" v-if="code_type==1" @click="sendCode()">{{lang.rg5}}</button>
					<button class="get-code-btn" disabled v-else-if="code_type==2">发送中</button>
					<button class="get-code-btn" disabled v-else-if="code_type==3">{{second}}S{{lang.rg6}}</button>
				</view>
				<view class="contInput flex-center">
					<input type="text" :placeholder="lang.rg7" v-model="invite" maxlength="10" />
				</view>
				<view class="treaty flex-center mt-20">
					<view class="flex-center font-12 font-plain">
						<text class="icon mr-5" :class="{'active':read}"></text>
						<text @click="read=!read">{{lang.rg8}}</text>
						<text class="font-main" @click="app.showOpen('user/explain?name=user_agreement')">{{lang.rg9}}</text>
					</view>
				</view>
			</view>
			<view class="btn-cont pl-45 pr-45 pt-50">
				<button class="btn" :disabled="!read||!register_status" @click="go_register()">{{lang.rg10}}</button>
			</view>
			<view class="text-center pt-20 font-plain">{{lang.rg11}}<text class="font-main" @click="go_login()">{{lang.rg12}}</text></view>
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
				pay:"",
				pay1:"",
				code:"",
				invite:"",
				second:60,//倒计时
				CodeSW:false,//验证码开关
				read:false,
				register_status:true,
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
			...mapMutations(["login","logout","setUserInfo","setQiniu"]),
			go_register:function(){
				var self=this;
				let send={
					mobile:self.mobile,
					pass:self.pass,
					pass1:self.pass1,
					// pay:self.pay,
					// pay1:self.pay1,
					code:self.code,
					invite:self.invite,
					ClientId:"0",
					// ClientId:plus.push.getClientInfo().clientid,
				};
				self.register_status=false;
				user.register(send,function(res){
					// console.log(send);
					if(res.data.status==1){
						uni.setStorageSync("showRule",true);
						self.login(res.data.data);
						self.setQiniu();
						self.setUserInfo();
						self.app._toastIcon(self.lang.rg13);
						setTimeout(function(){
							uni.reLaunch({
								url:'/pages/index/index'
							});
						},300);
					}else{
						console.log(res)
						self.app._toast(res.data.message);
					}
					self.register_status=true;
				},function(status,msg){
					self.app._toast(msg);
					self.register_status=true;
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
						sms.registerSMS(send, function(res) {
							if(res.data.status==1){
								self.app._toastIcon("发送成功");
								self.movetime();
								self.code_type=3;
							}else{
								self.code_type=1;
								self.code_status=true;
								self.app._toast(res.data.message);
							};
						}, function(status, msg) {
							self.app._toast(msg);
							self.code_status=true;
							self.code_type=1;
						});
					};
				});
			},
			go_login:function(){
				uni.redirectTo({
				    url: '/pages/login/login'
				});
			}
		}
	}
</script>

<style>
	@import url("@/common/css/login.css");
	.logo-cont{
		padding-top: 66px;
		padding-bottom: 10px;
	}
	.logo{
		width: 85px;
		height: 121px;
	}
</style>
