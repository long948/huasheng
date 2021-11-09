<template>
	<view class="box">
		<view class="w-100 bc-white pt-10">
			<view class="contInput bt_line">
				<input type="password" :placeholder="lang.mdp1" v-model="old" maxlength="20" />
			</view>
			<view class="contInput bt_line">
				<input type="password" :placeholder="lang.mdp2" v-model="pass" maxlength="20" />
			</view>
			<view class="contInput bt_line">
				<input type="password" :placeholder="lang.mdp3" v-model="pass1" maxlength="20" />
			</view>
			<view class="contInput flex-center pr-20">
				<input type="text" :placeholder="lang.sp3" v-model="code" maxlength="6" />
				<view class="one-row">
					<button class="get-code-btn" :disabled="!code_status" v-if="!CodeSW" @click="sendCode()">{{lang.sp4}}</button>
					<button class="get-code-btn" disabled v-else>{{second}}S{{lang.fgp4}}</button>
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
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		data() {
			return {
				old:"",
				pass:"",
				pass1:"",
				code:"",
				modify_status:true,
				second:60,//倒计时
				CodeSW:false,//验证码开关
				code_status:true,
			}
		},
		onLoad() {
			var self=this;
			uni.setNavigationBarTitle({
				title:self.lang.mdp4
			});
		},
		computed:{
			...mapState(['lang']),
		},
		methods: {
			modifyPass:function(){
				var self=this;
				let send={
					old:self.old,
					pass:self.pass,
					pass1:self.pass1,
					code:self.code,
				};
				self.modify_status=false;
				user.modifyPay(send,function(res){
					if(res.data.status==1){
						self.app._toastIcon(self.lang.mdl5);
					}else{
						self.app._toast(res.data.message);
					}
					self.old="";
					self.pass="";
					self.pass1="";
					self.code="";
					self.modify_status=true;
				},function(status,msg){
					self.app._toast(msg);
					self.modify_status=true;
					
				});
			},
			sendCode:function() {//获取验证码
				var self=this;
				let send={
					// mobile:self.mobile
				};
				self.code_status=false;
				sms.modifySMS(send, function(res) {
					if(res.data.status==1){
						self.app._toastIcon(self.lang.fgp9);
						self.movetime();
					}else{
						self.app._toast(res.data.message);
					};
					self.code_status=true;
				}, function(status, msg) {
					self.app._toast(msg);
					self.code_status=true;
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
