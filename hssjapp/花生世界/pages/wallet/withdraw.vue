<template>
	<view class="box pt-20 pb-20 pl-15 pr-15">
		<view class="w-100 br-4 bc-white pt-16 pb-16 pl-12 pr-12 mb-12 flex-center flex-j-between" @click="popSW=true">
			<text class="font-16 font-w-b font-c-3" v-text="coin.EnName"></text>
			<view class="flex-center font-c-6 font-12">切换币种<i class="iconfont icon-icon-test9 ml-5"></i></view>
		</view>
		<view class="content pl-20 pr-20 br-4">
			<view class="cont-list">
				<view>
					<text class="font-white font-14 op-8">{{lang.wt1}}</text>
				</view>
				<view class="contInput flex-center flex-j-between br-4 bc-white pl-10 pr-10">
					<input type="text" :placeholder="lang.wt2" v-model="address" />
					<view class="one-row" @click="get_address()">
						<image src="../../static/img/803af873701ece0e3cd7edaf0316613.png"></image>
					</view>
				</view>
			</view>
			<view class="cont-list">
				<view class="flex-center flex-j-between">
					<text class="font-white font-14 op-8 one-row">{{lang.wt3}}</text>
					<view class="font-white font-12 op-7 nowrap">{{coin.EnName}} {{lang.wt4}}：{{coin.user.Money}}</view>
				</view>
				<view class="contInput flex-center flex-j-between br-4 bc-white pl-10 pr-10">
					<input type="text" :placeholder="lang.wt5" v-model="num" />
					<view class="one-row font-12 font-main" @click="num=coin.user.Money">{{lang.wt6}}</view>
				</view>
			</view>
			<view class="cont-list">
				<view class="flex-center flex-j-between">
					<text class="font-white font-14 op-8 one-row">{{lang.wt7}}</text>
					<view class="font-white font-12 op-7 nowrap">{{lang.wt8}}： {{app._accMul(fl,100)}}%</view>
				</view>
				<view class="contInput flex-center flex-j-between br-4 bc-white pl-10 pr-10">
					<!-- <input type="text" :value="app._accMul(num,change)" /> -->
					<input type="text" v-model="fee" disabled="" />
					<view class="one-row op-5 font-12 font-grey" v-text="coin.EnName">PT</view>
				</view>
			</view>
			<view class="cont-list">
				<view>
					<text class="font-white font-14 op-8">{{lang.wt9}}</text>
				</view>
				<view class="contInput flex-center flex-j-between br-4 bc-white pl-10 pr-10">
					<input type="password" :placeholder="lang.wt10" v-model="pass" />
					<text class="one-row font-12 font-main" @click="app.showOpen('login/forgetPay')">{{lang.wt11}}</text>
				</view>
			</view>
			<view class="cont-list">
				<view>
					<text class="font-white font-14 op-8">{{lang.wt12}}</text>
				</view>
				<view class="contInput flex-center flex-j-between br-4 bc-white pl-10 pr-10">
					<input type="text" :placeholder="lang.wt13" v-model="remark" />
				</view>
			</view>
			<view class="cont-list">
				<view>
					<text class="font-white font-14 op-8">验证码</text>
				</view>
				<view class="contInput flex-center flex-j-between br-4 bc-white pl-10 pr-10">
					<input type="text" placeholder="请输入验证码" v-model="code" />
					<text class="one-row font-12 font-main" v-if="code_type==1" @click="sendCode()">发送验证码</text>
					<text class="one-row font-12 font-main" v-else-if="code_type==2">发送中</text>
					<text class="one-row font-12 font-main" v-else-if="code_type==3">{{second}}S后获取</text>
				</view>
			</view>
			<view class="btn-cont flex-center flex-j-center pt-40 pb-25">
				<button class="btn" :disabled="!transfer_status" @click="transfer()">{{lang.wt14}}</button>
			</view>
			<view class="pt-20 pb-20">
				<view class="font-white pb-10">{{lang.wt15}}</view>
				<!-- <view class="font-white font-12 newlines lh-25">{{lang.wt16}}</view>
				<view class="font-white font-12 newlines lh-25">{{lang.wt17}}</view> -->
				<view class="font-white font-12 newlines lh-25" v-html="coin.WithDrawInfo"></view>
			</view>
		</view>
		<!-- 弹窗 -->
		<view class="winPopup flex-end" v-if="popSW" @click="popSW=false">
			<view class="w-100 pl-16 pr-16 pb-15" style="background-color: #EDEAEC;" @click.stop="">
				<view class="w-100 pt-16 pb-16 flex-center flex-j-between">
					<text class="font-c-3 font-12">选择币种</text>
					<i class="iconfont icon-icon-test6" @click="popSW=false"></i>
				</view>
				<view class="w-100 pl-12 pr-12 bc-white br-4" style="max-height: 60vh;overflow-y: scroll;">
					<view class="w-100 bt-line text-center pt-15 pb-15 font-c-3 font-16 font-w-b" v-for="(item,index) in coinlist" :key="index" @click="coin=item;popSW=false;">
						{{item.EnName}}
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	import Captcha from "@/common/js/TCaptcha.js";
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		data() {
			return {
				address:"",
				num:"",
				remark:"",
				change:"",
				pass:"",
				second:60,//倒计时
				CodeSW:false,//验证码开关
				code:"",
				transfer_status:true,
				code_status:true,
				code_type:1,//1-未发送，2发送完成，3发送中
				coin:{
					EnName:"--",
					WithDrawInfo:"--",
					user:{
						Money:"-"
					}
				},
				popSW:false,
			}
		},
		onLoad: async function(e) {
			var self=this;
			if(e.address){
				self.address=e.address;
			}
			self.initCode();
			self.getCharge();
			try{
				uni.showLoading();
				let coin=await self.getCoin();
				let info="";
				coin=coin.filter(function(e){
					if(e.EnName.toLocaleUpperCase()=="PT" && e.IsRecharge==1){
						info=e;
					}
					return e.IsWithDraw==1;
				});
				uni.hideLoading()
				self.coinlist=coin;
				console.log(coin);
				if(!info){
					info=coin[0];
				}
				self.coin=info;
			}catch(e){
				console.log(e);
			}	
		},
		onBackPress(){
			return Captcha.hide(); 
		},
		computed:{
			...mapState(['userInfo','assets','lang']),
			fee:function(){
				let self=this;
				if(self.coin.isPlatform==1){
					return self.app._accMul(self.change,self.num);
				}
				let f=self.app._accMul(self.coin.WithDrawFee,self.num);
				if(Number(self.coin.MinWithDrawFee)>Number(f)){
					return self.coin.MinWithDrawFee;
				}
				return f;
			},
			fl:function(){
				let self=this;
				if(self.coin.isPlatform==1){
					return self.change;
				}
				// let f=self.app._accMul(self.coin.WithDrawFee,self.num);
				// if(Number(self.coin.MinWithDrawFee)>Number(f)){
				// 	return self.coin.MinWithDrawFee;
				// }
				// return self.coin.WithDrawFee;
				return self.coin.WithDrawFee;
			}
		},
		methods: {
			...mapMutations(["setUserInfo","set_assets"]),
			getCoin:function(){
				return new Promise(function(resolve,reject){
					uni.request({
						url: config.api + "/coin-list",
						data: {},
						method: "get",
						header: {Authorization: config.getToken()},
						success: res => {
							// console.log(JSON.stringify(res));
							if (res.data.status == 1) {
								resolve(res.data.data);
							}else{
								self.app._toast(res.data.message);
							};
						},
						fail: (res) => {
							console.log(res);
							reject(res);
						},
						complete: (res) => {}
					});
				})
			},
			get_address:function(){
				var self=this;
				uni.scanCode({
				    success: function (res) {
				        self.address = res.result;
				    }
				});
			},
			transfer:function(){
				var self=this;
				if(self.address.trim().length==0){
					self.app._toast(self.lang.wt18);
					return;
				};
				if(isNaN(self.num) || self.num<0 || self.num.trim().length==0){
					self.app._toast(self.lang.wt19);
					return;
				};
				if(Number(self.num)>Number(self.coin.user.Money)){
					self.app._toast("余额不足");
					return;
				}
				if(self.pass.trim().length==0){
					self.app._toast(self.lang.wt20);
					return;
				};
				if(!self.code||self.code.trim().length==0){
					self.app._toast("请输入验证码");
					return;
				};
				let send={
					Id:self.coin.Id,
					Money:self.num,
					Address:self.address,
					remark:self.remark,
					PayPassword:self.pass,
					AuthCode:self.code,
				};
				console.log(JSON.stringify(send))
				console.log(config.getToken())
				self.transfer_status=false;
				let url = config.api + "/post.exchange";//平台用原来 isPlatform
				if(self.coin.isPlatform==1){
					url = config.api + "/post.exchange";
				}else{
					url = config.api + "/withdraw";
				}
				uni.request({
					url: url,
					data: send,
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						console.log(JSON.stringify(res));
						if (res.data.status == 1) {
							self.app._toastIcon(self.lang.wt21);
							self.set_assets();
							setTimeout(function(){
								self.app.goBack();
							},1000);
						}else{
							console.log(JSON.stringify(res));
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						setTimeout(function(){
							self.transfer_status=true;
						},500);
					}
				});
			},
			getCharge:function(){
				var self=this;
				uni.request({
					url: config.api + "/get.exchange.fee",
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						if (res.data.status == 1) {
							self.change=res.data.data;
						}else{
							console.log(JSON.stringify(res));
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {}
				});
			},
			sendCode:function() {//获取验证码
				var self=this;
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
						let url=config.api + "/sms-transfer";
						self.code_status=false;
						self.code_type=2;
						uni.request({
							url: url,
							data: {
								ticket:result.ticket,
								randstr:result.randstr,
							},
							method: "post",
							header: {Authorization: config.getToken()},
							success: res => {
								// console.log(JSON.stringify(res));
								if(res.data.status==1){
									self.app._toastIcon("发送成功");
									self.movetime();
									self.code_type=3;
								}else{
									self.code_type=1;
									self.code_status=true;
									self.app._toast(res.data.message);
								};
							},
							fail: (res) => {
								console.log(JSON.stringify(res));
								self.app._toast("发送失败");
								self.code_status=true;
								self.code_type=1;
							},
							complete: (res) => {
								setTimeout(function(){
									self.code_status=true;
								},300);
							}
						});
					};
				});
			},
		}
	}
</script>

<style>
	@import url("@/common/newStyle/base.css");
	@import url("@/common/newStyle/iconfont.css");
	@import url("@/common/newStyle/common.css");
	page{background-color: #F7F7F7;}
	.content{background: linear-gradient(bottom,#FA6C00,#FFB500);}
	.btn-cont{border-bottom: 1px solid rgba(255,255,255,0.2);}
	.btn-cont button.btn{width: 180px;}
	.cont-list{padding-top: 23px;}
	.contInput{width: 100%;height: 35px;margin-top: 10px;}
	.contInput input{width: 100%;height: 100%;font-size: 14px;color: #333333;}
	.contInput image{width: 18px;height: 18px;}
	.bt-line{
		border-bottom: 1px solid #F0F0F0;
	}
</style>
