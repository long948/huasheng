<template>
	<view class="box pt-50 pb-15 pr-15 pl-15 flex-wrap flex-j-center flex-wrap">
		<view class="page-title flex-center pl-15 pr-15 font-18">
			<view @click="app.goBack()" class="flex-center font-18 font-white">
				<i class="iconfont icon-zuo font-20 mr-5"></i>{{lang.store2}}
			</view>
		</view>
		<!-- <view style="width: 100%;height: 300px;position: relative;z-index: 1;background-color: red;"></view> -->
		<view class="content1 pl-20 pr-20 mt-10">
			<view class="head-portrait flex-center flex-j-center">
				<view class="head">
					<image :src="data.icon"></image>
				</view>
			</view>
			<view class="flex-center flex-j-between pt-30">
				<view class="w-33 font-dark-grey text-center font-14" v-text="'名称'">{{lang.store3}}</view>
				<view class="w-33 font-dark-grey text-center font-14" v-text="'售价('+(type=='sapling'?data.payCoinName:'花生米')+')'">{{lang.store4}}</view>
				<view class="w-33 font-dark-grey text-center font-14" v-text="'等级'">{{lang.store5}}</view>
			</view>
			<view class="flex-center flex-j-between pt-20">
				<view class="w-33 font-dark-main text-center font-14 nowrap" v-text="data.nickname">大黄狗</view>
				<view class="w-33 font-dark-main text-center font-14 nowrap">{{app.toNonExponential(data.price)}}</view>
				<view class="w-33 font-dark-main text-center font-18 nowrap">Lv.{{data.level}}</view>
			</view>
			<view class="form-cont pt-15 pb-15 pl-15 pr-15 mt-25 flex-center flex-j-between" v-if="type=='sapling'">
				<view class="w-50">
					<view class="max-w-100 font-dark-grey font-11 nowrap">总收益：{{app.toNonExponential(details.total_profit)}}花生米</view>
					<view class="mt-10 max-w-100 font-dark-grey font-12 nowrap">日产量：  {{app.toNonExponential(details.yield)}}</view>
				</view>
				<view class="w-50">
					<view class="max-w-100 font-dark-grey font-11 nowrap pl-20 text-right flex-center flex-j-between"><text>亩数：</text>{{details.computing_power}}</view>
					<view class="mt-10 max-w-100 font-dark-grey font-12 nowrap pl-20 text-right flex-center flex-j-between"><text>生产周期： </text>{{details.cycle}}天</view>
				</view>
			</view>
			<view class="form-cont pt-15 pb-15 pl-25 pr-25 mt-25" v-else-if="data.id=='211171920811460612'">
				<view class="flex-center flex-j-between">
					<view class="w-50 font-dark-grey font-12 nowrap">超市权益：{{ app._toFixed(app._accMul(num,data.price),2) }}</view>
				</view>
			</view>
			<view class="form-cont pt-15 pb-15 pl-25 pr-25 mt-25" v-else>
				<view class="flex-center flex-j-between">
					<view class="w-50 font-dark-grey font-12 nowrap" v-if="type=='dog'">防守次数：{{details.max_defense_count}}</view>
					<view class="w-50 font-dark-grey font-12 nowrap" v-if="type=='mouse'">偷取次数：{{details.max_frequency}}</view>
				</view>
			</view>
			<view class="contInput mt-20" v-if="data.id=='211171920811460612'">
				<input class="font-12 font-grey" type="number" placeholder="请输入数量" v-model="num" />
			</view>
			<view class="contInput mt-20">
				<input class="font-12 font-grey" type="password" :placeholder="lang.store14" v-model="pass" />
				<view class="w-100 text-right pt-10"><text class="font-12 font-dark-main" @click="app.showOpen('login/forgetPay')">{{lang.i17}}</text></view>
			</view>
			<view class="btn-cont pt-25 pb-25 pl-30 pr-30 bt_line">
				<button class="btn" :disabled="!buy_status" @click="buy()">{{lang.store10}}</button>
				<view class="tips font-10 font-red text-center mt-10 hide">余额不足，继续加油吧！</view>
			</view>
			<view class="pt-20 pb-20">
				<view class="font-dark-main pb-10">{{lang.store11}}</view>
				<view class="font-dark-grey font-12 newlines lh-25" v-text="details.explanation">
					1. 用户拥有的大⻩看守总时⻓不得 超过20小时。
				</view>
			</view>
		</view>
		<view class="winPopup" v-if="authSW">
			<authResult :show="authSW" :authSW.sync="authSW"></authResult>
		</view>
		<view class="winPopup flex-center flex-j-center" v-if="loader">
			<loader type="loader2"></loader>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	import authResult from "@/components/authResult.vue";
	import loader from "@/components/loader/loader.vue";
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		components: {
			loader,
			authResult
		},
		data() {
			return {
				loader:false,
				data:"",
				type:"",
				details:{
					cycle:"-",
				},
				buy_status:true,
				pass:"",
				authSW:false,
				num:""
			}
		},
		onLoad(e) {
			var self=this;
			self.type=e.type;
			self.data=JSON.parse(e.data);
			self.getDetails();
		},
		computed:{
			...mapState(['userInfo','qiniu','lang']),
		},
		methods: {
			buy:function(){
				var self=this;
				if(self.userInfo.auth_status==1){
					self.authSW=true;
					return;
				};
				if(self.userInfo.auth_status==2){
					self.app.showOpen('index/AuthDetails');
					return;
				};
				if(self.userInfo.auth_status!=3){
					setTimeout(function(){
						self.app.showOpen('auth/auth');
					},500);
					return self.app._toast(self.lang.store12);
				};
				if(self.userInfo.IsSetPayPass!=1){
					setTimeout(function(){
						self.app.showOpen('login/setPay');
					},500);
					return self.app._toast(self.lang.store13);
				};
				if(self.pass.trim().length==0){
					return self.app._toast(self.lang.store14);
				};
				let send={
					type:self.type,
					id:self.data.id,
					PayPassword:self.pass,
				};
				if(self.data.id=="211171920811460612"){
					if(!self.num || isNaN(self.num) || self.num<=0 ){
						return self.app._toast("请输入正确的数量");
					}
					send.number=self.num;
				}
				console.log(send)
				self.buy_status=false;
				let url=config.api + "/store-buy";
				uni.request({
					url: url,
					data: send,
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.app._toastIcon(self.lang.store15);
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
							self.buy_status=true;
						},300);
					}
				});
			},
			getDetails:function(){
				var self=this;
				self.loader=true;
				let url=config.api + "/store-details";
				uni.request({
					url: url,
					data: {
						id:self.data.id,
						type:self.type
					},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.details=res.data.data;
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						self.loader=false;
					}
				});
			}
		}
	}
</script>

<style>
	page{
		height: 100vh;
		background: url(../../static/img/1e0cc92565daf015e510d11505f9289.png) no-repeat;
		background-size: 100% 100%;
	}
	page::after{
		position: absolute;
		top: 0px;
		left: 0px;
		right: 0px;
		bottom: 0px;
		content: "";
		background-color: rgba(4, 4, 4, 0.4);
	}
	.box {
		height: 100vh;
		width: 100%;
		overflow-y: scroll;
		position: relative;
		padding-bottom: 60px;
	}
	.box .page-title{
		width: 100%;
		position: absolute;
		left: 0px;
		top: 45px;
		z-index: 50;
	}
	.content1{
		width: 100%;
		height: auto;
		position: relative;
		z-index: 1;
		border-radius: 10px;
		background-color: #FFFFFF;
		margin-top: 50px !important;
	}
	.head-portrait{
		width: 100%;
		height: 60px;
	}
	.head-portrait .head{
		height: 120px;
		width: 120px;
		position: absolute;
		top: -60px;
		left: calc(50% - 60px);
		overflow: hidden;
	}
	.head-portrait .head image{
		width: 100%;
		height: 100%;
		display: block;
	}
	.form-cont{
		width: 100%;
		border-radius: 8px;
		border: 1px solid #FA6C00;
	}
	.contInput input{width: 100%;height: 44px;padding: 0px 20px;background-color: #F6F6F6;border-radius: 4px;}
</style>
