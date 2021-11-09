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
					<input type="text" :placeholder="lang.lg1" v-model="mobile" maxlength="11" />
				</view>
				<view class="contInput flex-center">
					<image mode="widthFix" src="../../static/img/eca5af10099d156eedbf1c92cffa733.png"></image>
					<text class="split-line"></text>
					<input type="password" :placeholder="lang.lg2" v-model="pass" maxlength="20" />
				</view>
				<view class="contInput flex-center pr-10">
					<input type="text" :placeholder="lang.lg3" v-model="code" maxlength="6" />
					<view class="one-row code-url" @click="getcode()">
						<image :src="picurl"></image>
					</view>
				</view>
				<view class="text-right pt-10"><text class="font-main" @click="app.showOpen('login/forgetLogin')" v-text="lang.lg4">忘记密码</text></view>
			</view>
			<view class="btn-cont pl-45 pr-45 pt-50">
				<button class="btn" :disabled="!login_status" @click="go_login()" v-text="lang.lg5">登录</button>
			</view>
			<view class="text-center pt-20"><text class="font-main" @click="go_register()" v-text="lang.lg6">前往注册</text></view>
			<!-- <view class="pay-login flex-center flex-j-center" @click="app._toast('暂不支持')">
				<view class="cont flex-center">
					<image src="../../static/img/2cff4e3d8360b3d709fcb7a5405c9d6.png"></image>
					<view class="text-center text">支付宝账户快捷登录</view>
				</view>
			</view> -->
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
				mobile:"",//13212391332 18581207080 13900000004
				pass:"", //aa111111
				login_status:true,
				code:"",
				picurl:"",
				rdNum:"",
			}
		},
		onLoad() {
			var self=this;
			// uni.setStorageSync("userLang",'zh');
			self.logout();
			self.getcode();
		},
		computed:{
			...mapState(['lang']),
		},
		methods: {
			...mapMutations(["login","logout","setUserInfo","setQiniu"]),
			go_register:function(){
				uni.redirectTo({
				    url: '/pages/login/register'
				});
			},
			go_login:function(){
				var self=this;
				let send={
					mobile:self.mobile,
					pass:self.pass,
					// ClientId:plus.push.getClientInfo().clientid,
					ClientId:"1",
					code:self.code,
					rand:self.rdNum,
				};
				// console.log(JSON.stringify(send));
				self.login_status=false;
				user.login(send,function(res){
					if(res.data.status==1){
						uni.setStorageSync("showRule",true);
						self.login(res.data.data);
						self.setQiniu();
						self.setUserInfo();
						self.app._toastIcon(self.lang.lg7);
						setTimeout(function(){
							uni.reLaunch({
								url:'/pages/index/index'
							});
						},300);
					}else if(res.data.status == 100000){
						var buttons = [self.lang.lg8];
						let msg=res.data.message?res.data.message:self.lang.lg9
						plus.nativeUI.confirm(msg, function(ev) {
							
						}, "", buttons);
					}else{
						console.log(res)
						self.code="";
						self.getcode();
						self.app._toast(res.data.message);
					}
					self.login_status=true;
				},function(status,msg){
					self.app._toast(msg);
					self.login_status=true;
				});
			},
			randomNum:function(n){
				var num='';
				for(var i=0;i<n;i++){  
				    num+=Math.floor(Math.random()*10); 
				};
				return num;  
			},
			getcode:function(){
				var self=this;
				// let num= Math.floor(Math.random() * (100 - 0));
				self.rdNum=self.randomNum(8);
				let url=config.api+'/captcha?rand='+self.rdNum;
				self.picurl=url;
			},
		}
	}
</script>

<style>
	@import url("@/common/css/login.css");
	.code-url{width: 150px;}
	.code-url image{width: 100%;height: 25px;max-height: 25px;display: block;margin: 0px;}
</style>
