<template>
	<view class="box">
		<view class="w-100 pt-40 pb-18 top-content font-white">
			<view class="w-100 flex-center flex-j-between pl-10 pr-10">
				<i class="iconfont icon-icon-test8 font-30" @click="app.goBack()"></i>
				<text class="font-16 font-w-b">钱包</text>
				<i class="iconfont icon-icon-test8 font-30" style="opacity: 0;"></i>
			</view>
			<view class="w-100 pl-15 pr-15 pt-10">
				<view class="w-100 font-12">总账户资产折合(USTD)</view>
				<view class="w-100 font-21 font-w-b mt-8 pb-24">
					{{allusdt}}<text class="font-10">≈{{allcny}}cny</text>
				</view>
				<view class="w-100 flex-center flex-j-between">
					<view class="btn" @click="go_recharge()">收款</view>
					<view class="btn" @click="go_withdraw()">付款</view>
					<view class="btn" @click="app.showOpen('wallet/bill')">账单</view>
				</view>
			</view>
		</view>
		<view class="w-100" style="height: 12px;background-color: #EDEAEC;"></view>
		<view class="w-100 content">
			<view class="w-100 flex-center flex-j-between bb-line pl-16 pr-16 pt-15 pb-15">
				<view class="flex-center" @click="select=!select">
					<image class="select" src="../../static/img/select-active.png" v-if="select"></image>
					<image class="select" src="../../static/img/select.png" v-else></image>
					<text class="font-c-6 font-12 ml-12">隐藏小币种</text>
				</view>
				<view class="flex-center search-cont">
					<input type="text" v-model="key" placeholder="搜索" />
					<i class="iconfont icon-search"></i>
				</view>
			</view>
			<view class="w-100 bb-line pl-16 pb-4 pt-24 pb-24" v-for="(item,index) in data" :key="index" @click="goDetails(item)">
				<view class="flex-center flex-j-between pb-10 pr-10">
					<text class="font-18 font-w-b font-dark-main" v-text="item.EnName">USDT</text>
					<i class="iconfont icon-icon-test10 font-c-9 font-22 text-right"></i>
				</view>
				<view class="w-100 flex-center flex-j-between pr-16">
					<view class="w-33">
						<text class="font-12 font-c-6">可用</text>
						<view class="w-100 font-14 font-c-3 nowrap font-w-b" v-text="app.toNonExponential(item.Available)">0.00</view>
					</view>
					<view class="w-33 flex-center flex-j-center">
						<view style="max-width: 100%;">
							<text class="font-12 font-c-6">冻结</text>
							<view class="w-100 font-14 font-c-3 nowrap font-w-b" v-text="app.toNonExponential(item.Forzen)">0.00</view>
						</view>
					</view>
					<view class="w-33 text-right">
						<text class="font-12 font-c-6">折合(CNY)</text>
						<view class="w-100 font-14 font-c-3 nowrap font-w-b" v-text="item.cny">0.00</view>
					</view>
				</view>
			</view>
			<view class="w-100 flex-center flex-a-center flex-j-center flex-wrap font-c-9 h-50" v-if="data.length==0">
				<i class="iconfont icon-zanwu font-100"></i>
				<view class="w-100 text-center font-12 pt-20 pb-20">{{load?'加载中~':'无数据~'}}</view>
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
				list:[],
				load:true,
				select:false,
				key:"",
				allcny:"",
				allusdt:""
			}
		},
		onLoad() {
			uni.startPullDownRefresh();
		},
		onPullDownRefresh() {
			this.getData();
		},
		computed:{
			...mapState(['userInfo','assets','coinList']),
			data:function(){
				let self=this;
				let result=self.list.filter(function(e){
					if(self.key.trim().length!=0){
						return (self.select?(Number(e.Available)>0 && e.EnName.indexOf(self.key)!=-1):(e && e.EnName.indexOf(self.key)!=-1));
					}else{
						return (self.select?Number(e.Available)>0:e);
					}
				});
				return result;
			}
		},
		methods: {
			getData:function(){
				var self=this;
				// uni.showLoading();
				self.load=true;
				let url=config.api + "/get.wallet.info";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							let cnys=0;
							let usdts=0;
							res.data.data.forEach(function(e){
								if(e.EnName.toLocaleUpperCase()!="PT"){
									e.cny=self.app._accMul(e.PriceCny,(Number(e.Forzen)+Number(e.Available)));
								}else{
									e.cny=self.app._accMul(e.MoneyUSDT,7);
								}
								cnys=cnys+e.cny;
								e.cny=self.app._toFixed(e.cny,2);
								if(Number(e.cny)==0){
									e.cny="~";
								}
								// e.usd=self.app._accMul(self.getPrice(e),(Number(e.Forzen)+Number(e.Available)));
								usdts=usdts+Number(e.MoneyUSDT);
							})
							self.allcny=self.app._toFixed(cnys,2);
							self.allusdt=self.app._toFixed(usdts,2);
							self.list=res.data.data;
						}else{
							self.app._toast(res.data.message);
							console.log(JSON.stringify(res));
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						// uni.hideLoading();
						self.load=false;
						uni.stopPullDownRefresh();
					}
				});
			},
			getPrice:function(a){
				let self=this;
				let PriceCny=0;
				PriceCny=self.coinList.filter(function(e){
					return e.Id==a.Id;
				})[0].PriceCny;
				return PriceCny;
			},
			go_recharge:function(){
				uni.navigateTo({
					url:"recharge"
				})
			},
			go_withdraw:function(){
				uni.navigateTo({
					url:"withdraw"
				})
			},
			goDetails:function(item){
				let data;
				if(!item && this.list.length!=0){
					data=this.list[0];
				}else{
					data=item;
				}
				uni.navigateTo({
					url:"wallet-details?data="+JSON.stringify(data)
				})
			}
		}
	}
</script>

<style lang="less">
	@import url("@/common/newStyle/base.css");
	@import url("@/common/newStyle/iconfont.css");
	@import url("@/common/newStyle/common.css");
	page{
		background-color: #FFFEFF;
	}
	.top-content{
		background: url(../../static/img/NewUIwalletbackground.png) no-repeat;
		background-size: 100% 100%;
		.btn{
			line-height: 35px;text-align: center;border-radius: 2px;
			width: 30%;font-size: 14px;font-weight: bold;
			background-color: #FFC000;
		}
	}
	.content{
		.bb-line{
			border-bottom: 1px solid #F4F4F4;
		}
		image.select{
			width: 19px;height: 19px;
		}
		.search-cont{
			border: 1rpx solid #DFDFDF;border-radius: 11px;height: 23px;
			input{
				width: 80px;height: 100%;font-size: 10px;color: #333333;padding-left:15px;
			}
			.iconfont{
				color: #666666;font-size: 15px;width: 20px;text-align: center;
			}
		}
	}
</style>
