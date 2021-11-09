<template>
	<view class="box">
		<view class="top-data">
			<view class="user-Info flex-center flex-j-between" v-if="Info.user">
				<view class="head-portrait" @click="userInfo.auth_status==2?app.showOpen('index/AuthDetails'):userInfo.auth_status==3?app.showOpen('index/AuthDetails'):''">
					<image :src="userInfo.Avatar"></image>
					<view class="status text-center" v-text="userInfo.auth_status==0? lang.buy1 :userInfo.auth_status==1?lang.buy2:userInfo.auth_status==2?lang.buy3:lang.buy4">已实名</view>
				</view>
				<view class="user-data">
					<view class="name flex-center nowrap pb-10">
						<text class="font-16 font-white" v-text="userInfo.NickName">用户名</text>
						<image v-if="Info.user.level" class="ml-5" mode="widthFix" :src="Info.user.level.url"></image>
					</view>
					<view class="status-Info flex-center nowrap">
						<view class="bt br-1 font-10">{{lang.buy5}}{{userInfo.Level}}</view>
						<block v-if="userInfo.IsFrozenCTC==1">
							<view class="bt br-1 font-10 ml-10">{{lang.buy6}}</view>
							<view @click="app.showOpen('trade/frozen')" class="bt br-1 font-10 ml-10 br-2" style="background: transparent;border: 1px solid #BEDE8F;">{{lang.buy7}}</view>
						</block>
					</view>
				</view>
			</view>
		</view>
		<view class="content pl-20 pr-20">
			<view class="w-100 bc-white br-4 form-cont mb-15">
				<view class="font-14 font-grey op-7 pt-15">{{lang.buy8}}</view>
				<view class="cheques flex-center pt-25 pb-25">
					<view class="w-33 flex-center font-14 font-grey" :class="{'active':alipay}" @click="alipay=!alipay">
						<text class="icon iconfont icon-iconfontcheck"></text>{{lang.buy9}}
					</view>
					<view class="w-33 flex-center font-14 font-grey" :class="{'active':isusdt}" @click="isusdt=!isusdt">
						<text class="icon iconfont icon-iconfontcheck"></text>USDT
					</view>
				</view>
			</view>
			<view class="w-100 bc-white br-4 form-cont">
				<view class="w-100 flex-center flex-j-between">
					<image src="../../static/img/7fdaaa5c17a2ac8b7ae2d93a0d54d75.png"></image>
					<view class="Input-cont">
						<view class="contInput flex-center pt-5 bt_line">
							<input type="text" :placeholder="lang.buy10" class="font-14" v-model="num" @input="change1" />
							<text class="one-row font-12 op-7 font-grey">PT</text>
						</view>
						<view class="contInput flex-center pt-5 bt_line">
							<input type="text" :placeholder="lang.buy11" class="font-14" v-model="num1" @input="change2" />
							<text class="one-row font-12 op-7 font-grey">CNY</text>
						</view>
					</view>
				</view>
				<!-- <view class="contInput flex-center flex-j-between pt-5 bt_line">
					<input type="text" placeholder="请输入最低成交额" class="font-14" v-model="min" />
					<text class="one-row font-12 op-7 font-grey">USDT</text>
				</view>
				<view class="contInput flex-center flex-j-between pt-5 bt_line">
					<input type="text" placeholder="请输入最高成交额" class="font-14" v-model="max" />
					<text class="one-row font-12 op-7 font-grey">USDT</text>
				</view> -->
				<!-- <view class="pt-15">
					<view class="font-12 font-red lh-20">平台限额{{app._accMul(data.MinMoney,7)}}-{{app._accMul(data.MaxMoney,7)}} USDT</view>
				</view> -->
			</view>
			<view class="pt-35">
				<button class="btn" :disabled="!buy_status" @click="buy()">{{lang.buy12}}</button>
			</view>
			
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		data() {
			return {
				Info:"",
				data:{
					MinMoney:0,
					MaxMoney:0,
				},
				price:0,
				isusdt:false,
				alipay:false,
				wechat:false,
				num:"",
				num1:"",
				min:"",
				max:"",
				buy_status:true,
			}
		},
		onLoad(e) {
			var self=this;
			self.price=e.price;
			self.Info=uni.getStorageSync("levelInfo");
			self.getInfo();
			uni.setNavigationBarTitle({
				title:self.lang.trade4
			});
		},
		computed:{
			...mapState(['userInfo','assets','lang']),
		},
		methods: {
			buy:function(){
				var self=this;
				if(self.userInfo.IsFrozenCTC==1){
					setTimeout(function(){
						self.app.showOpen('trade/frozen');
					},500);
					return self.app._toast(self.lang.buy13);
				};
				if(!self.alipay&&!self.isusdt){
					return self.app._toast(self.lang.buy14);
				};
				if(isNaN(self.num)||self.num.length==0){
					return self.app._toast(self.lang.buy15);
				};
				// if(isNaN(self.min)||self.min.trim().length==0){
				// 	return self.app._toast("请输入最低成交额");
				// };
				// if(isNaN(self.max)||self.max.trim().length==0){
				// 	return self.app._toast("请输入最高成交额");
				// };
				let send={
					Number:self.num,
					// MinMoney:self.min,
					// MaxMoney:self.max,
					IsAddress:self.isusdt,
					IsWechat:self.wechat,
					IsAlipay:self.alipay,
				};
				self.buy_status=false;
				let url=config.api + "/add-sell-order";
				uni.request({
					url: url,
					data: send,
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							var page=self.app._prePage();
							page.$vm.initlist();
							self.app.closeMeOpen("trade/result?price="+self.price);
						}else{
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
			change1:function(e){
				var self=this;
				let v=e.detail.value;
				if(isNaN(v)){
					setTimeout(function(){
						self.num="";
					},300);
					return self.app._toast(self.lang.buy16);
				};
				self.num1=self.app._accMul(self.num,self.app._accMul(self.price,7));
			},
			change2:function(e){
				var self=this;
				let v=e.detail.value;
				if(isNaN(v)){
					setTimeout(function(){
						self.num1="";
					},300);
					return self.app._toast(self.lang.buy16);
				};
				self.num=self.app._accDiv(self.num1,self.app._accMul(self.price,7));
			},
			getInfo:function(){
				var self=this;
				let url=config.api + "/ctc-pay-method";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.data=res.data.data;
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {}
				});
			},
		}
	}
</script>

<style>
	page{background-color: #F7F7F7;}
	.top-data{width: 100%;background: url(../../static/img/2fcc5030bed4d81293053eada380843.png) no-repeat;background-size: 100% 100%;padding: 23px 25px;padding-bottom: 80px;}
	.top-data .set-up,.top-data .user-Info{position: relative;z-index: 1;}
	.top-data .head-portrait{position: relative;}
	.top-data .head-portrait image{width: 56px;height: 56px;display: inline-block;border-radius: 50%;}
	.top-data .head-portrait .status{background: linear-gradient(#F8E4BA,#F8D792,#FD9253);color: #A0340A;font-size: 10px;width: 100%;height: 15px;line-height: 15px;border-radius: 8px;position: absolute;bottom: 3px;left: 0px;}
	.top-data .user-data{width: calc(100% - 70px);}
	.top-data .user-data .name{width: 100%;}
	.top-data .user-data .name image{width: 26px;display: inline-block;}
	.top-data .user-data .bt{color: rgba(255,255,255,0.8);height: 18px;line-height: 18px;padding: 0px 8px;background-color: #A6CE73;}
	.top-data .user-data .grade{height: 18px;line-height: 18px;padding: 0px 8px;background: linear-gradient(bottom,#FA6C00,#FFB500);}
	.top-data .user-data .status-Info .name{display: inline-block;height: 18px;line-height: 18px;padding: 0px 8px;background: linear-gradient(#F8E4BA,#F8D792,#FD9253);width: auto;}
	
	
	
	.content{width: 100%;position: relative;top: -50px;}
	.content .form-cont{width: 100%;box-shadow: 0px 2px 4px 2px #E8EAF0;padding: 15px 20px;}
	.content .form-cont image{width: 20px;height: 20px;display: block;}
	.content .form-cont .Input-cont{width: calc(100% - 30px);}
	.content .form-cont .contInput{height: 45px;width: 100%;}
	.content .form-cont .contInput input{width: 100%;height: 100%;color: #333333;}
	.content .form-cont .cheques{width: 100%;}
	.content .form-cont .cheques .icon{width: 15px;height: 15px;margin-right: 5px;border-radius: 50%;display: inline-flex;align-items: center;align-content: center;justify-content: center;border: 1px solid #979797;color: #FFFFFF;font-size: 12px;}
	.content .form-cont .cheques .active .icon{background: linear-gradient(bottom,#FA6C00,#FFB500);border: 0px;}
	.content button.btn{background: linear-gradient(bottom,#FA6C00,#FFB500);font-size: 14px;height: 44px;line-height: 44px;}

</style>
