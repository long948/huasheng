<template>
	<view class="box pt-20 pb-20 pl-15 pr-15">
		<view class="w-100 br-4 bc-white pt-16 pb-16 pl-12 pr-12 mb-12 flex-center flex-j-between" @click="popSW=true">
			<text class="font-16 font-w-b font-c-3" v-text="coin.EnName"></text>
			<view class="flex-center font-c-6 font-12">切换币种<i class="iconfont icon-icon-test9 ml-5"></i></view>
		</view>
		<view class="content pt-25 pb-25 pl-30 pr-30 br-4">
			<view class="flex-center flex-j-center">
				<!-- <tki-qrcode ref="qrcode" :val="address" :size="110" background="#fff" foreground="#000" pdground="#000" :onval="true" :loadMake="true"  :show="true" unit="px"></tki-qrcode> -->
				<!-- <uni-qrcode cid="qrcode2233" ref="qrcode2233" :size="110" :text="address" /> -->
				<canvas canvas-id="qrcode"  style="width: 200px;height: 200px;" />
				<!-- <uni-qrcode cid="qrcode2243" text="uQRCode" backgroundColor="#fff" size="110"  /> -->
			</view>
			<view class="text-center op-8 font-12 pt-10 pb-20">{{lang.wallet17}}</view>
			<view class="text-center font-14 newlines pb-20" v-text="address">0x157457fada36900c84af6219edf8af5c</view>
			<view class="btn-cont flex-center flex-j-center pb-20">
				<button class="btn" @click="app._copy(address)">{{lang.wallet18}}</button>
			</view>
			<view class="pt-20 pb-20">
				<view class="font-white pb-10">{{lang.wallet19}}</view>
				<!-- <view class="font-white font-12 newlines lh-25">{{lang.wallet20}}</view>
				<view class="font-white font-12 newlines lh-25">{{lang.wallet21}}</view> -->
				<view class="font-white font-12 newlines lh-25" v-html="coin.RechargeInfo"></view>
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
					<view class="w-100 bt-line text-center pt-15 pb-15 font-c-3 font-16 font-w-b" v-for="(item,index) in coinlist" :key="index" @click="getAddress(item)">
						{{item.EnName}}
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	// import tkiQrcode from "@/components/tki-qrcode/tki-qrcode.vue"
	import uQRCode from '@/common/js/uqrcode.js'
	import UniQrcode from '@/components/uni-qrcode/uni-qrcode'
	
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		components: {
			UniQrcode
		},
		data() {
			return {
				address:"",
				coinlist:[],
				coin:{
					EnName:"--",
					RechargeInfo:"--",
				},
				popSW:false,
			}
		},
		onLoad:async function() {
			var self=this;
			// self.getAddress();
			uni.setNavigationBarTitle({
				title:self.lang.wallet6
			});
			// this.getCoin();
			try{
				uni.showLoading();
				let coin=await self.getCoin();
				let info="";
				coin=coin.filter(function(e){
					if(e.EnName.toLocaleUpperCase()=="PT" && e.IsRecharge==1){
						info=e;
					}
					return e.IsRecharge==1;
				});
				console.log(coin);
				if(!info){
					info=coin[0];
				}
				console.log(info)
				self.coinlist=coin;
				self.getAddress(info);
			}catch(e){
				console.log(e);
			}
		},
		computed:{
			...mapState(['userInfo','assets','lang']),
		},
		methods: {
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
			getAddress:function(item){
				var self=this;
				self.coin=item;
				self.popSW=false;
				uni.showLoading();
				uni.request({
					url: config.api + "/recharge-address",
					data: {
						Id:item.Id
					},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						console.log(JSON.stringify(res));
						if (res.data.status == 1) {
							self.address=res.data.data;
							// var res = await uQRCode.make({
							// 	canvasId: 'batch',
							// 	text: self.address,
							// 	size: this.size
							// })
							// uni.showLoading({
							// 	title: '二维码生成中',
							// 	mask: true
							// })
							uQRCode.make({
								canvasId: 'qrcode',
								text: this.address,
								size: 200,
								margin: 5
							}).then(res => {
								// console.log(res)
							}).finally(() => {
								uni.hideLoading()
							})
							uni.hideLoading()
						}else{
							self.address="获取失败";
							uQRCode.make({
								canvasId: 'qrcode',
								text: self.address,
								size: 200,
								margin: 5
							}).then(res => {
								// console.log(res)
							}).finally(() => {
								uni.hideLoading()
							})
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						uni.hideLoading();
						console.log(JSON.stringify(res));
					},
					complete: (res) => {}
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
	.bt-line{
		border-bottom: 1px solid #F0F0F0;
	}
</style>
