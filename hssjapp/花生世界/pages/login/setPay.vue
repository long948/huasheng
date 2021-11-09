<template>
	<view class="box">
		<view class="w-100 bc-white pt-10">
			<view class="contInput bt_line">
				<input type="password" :placeholder="lang.sp1" v-model="pass" maxlength="20" />
			</view>
			<view class="contInput bt_line">
				<input type="password" :placeholder="lang.sp2" v-model="pass1" maxlength="20" />
			</view>
			<view class="contInput flex-center pr-20">
				<input type="text" :placeholder="lang.sp3" v-model="code" maxlength="6" />
				<view class="one-row">
					<!-- <button class="get-code-btn" :disabled="!code_status" v-if="!CodeSW" @click="sendCode()">{{lang.sp4}}</button>
					<button class="get-code-btn" disabled v-else>{{second}}S{{lang.fgp4}}</button> -->
					<button class="get-code-btn" :disabled="!code_status" v-if="code_type==1" @click="sendCode()">{{lang.fgl2}}</button>
					<button class="get-code-btn" disabled v-else-if="code_type==2">发送中</button>
					<button class="get-code-btn" disabled v-else-if="code_type==3">{{second}}S{{lang.fgl3}}</button>
				</view>
			</view>
		</view>
		<view class="btn-cont pl-15 pr-15 pt-50 pb-50">
			<button class="btn" :disabled="!modify_status" @click="modifyPass()">{{lang.sp5}}</button>
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
				pass:"",
				pass1:"",
				code:"",
				modify_status:true,
				second:60,//倒计时
				CodeSW:false,//验证码开关
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
			...mapState(['userInfo','qiniu','lang']),
		},
		methods: {
			...mapMutations(["setUserInfo"]),
			modifyPass:function(){
				var self=this;
				let send={
					pass:self.pass,
					pass1:self.pass1,
					code:self.code,
				};
				self.modify_status=false;
				user.setPay(send,function(res){
					if(res.data.status==1){
						let val=uni.getStorageSync("userInfo");
						val.IsSetPayPass=1;
						uni.setStorageSync("userInfo",val);
						self.setUserInfo();
						self.pass="";
						self.pass1="";
						self.code="";
						self.app._toastIcon(self.lang.sp6);
						setTimeout(function(){
							self.app.goBack();
						},1000);
					}else{
						self.app._toast(res.data.message);
					};
					self.modify_status=true;
				},function(status,msg){
					self.app._toast(msg);
					self.modify_status=true;
				});
			},
			sendCode:function() {//获取验证码
				var self=this;
				Captcha.show();
			},
			initCode:function(){
				var self=this;
				Captcha.init(function(result) {
					let send={
						// mobile:self.mobile
						ticket:result.ticket,
						randstr:result.randstr,
					};
					self.code_status=false;
					self.code_type=2;
					sms.setPaySMS(send, function(res) {
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
				});
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
		}
	}
</script>

<style>
	page{background-color: #F7F7F7;}
	.contInput{width: 100%;height: 50px;}
	.contInput input{width: 100%;height: 100%;color: #333333;font-size: 14px;padding: 0px 20px;}
	button.btn{background: linear-gradient(bottom,#FA6C00,#FFB500);}
</style>
