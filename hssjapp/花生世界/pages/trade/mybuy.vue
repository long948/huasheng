<template>
	<view class="box">
		<view class="w-100 pt-15">
			<view class="cont-list bc-white pt-20 pb-20 pl-20 pr-20 mb-10" v-for="(item,index) in list" :key="index"> 
				<view class="flex-center flex-j-between">
					<view class="w-30 font-12 title">{{lang.buy28}}</view>
					<view class="w-30 font-12 title">{{lang.buy29}}（PT）</view>
					<view class="w-30 font-12 title">{{lang.buy30}}（USDT）</view>
				</view>
				<view class="flex-center flex-j-between pt-5">
					<view class="w-30 font-12 value nowrap" v-text="getcreat(item.AddTime)">18:15 10/25</view>
					<view class="w-30 font-12 value nowrap" v-text="app._toFixed(item.Number,4)">100.00000000</view>
					<view class="w-30 font-12 value nowrap" v-text="app._toFixed(item.Price_USDT,4)">711.00</view>
				</view>
				<!-- <view class="font-12 pt-20 title pb-5">当前已完成{{ app._accMul(item.Rate,100)+'%'}}（已收到{{app._toFixed(item.Complete_USDT,4)}}USDT，还差{{app._toFixed(item.Surplus_USDT,4)}}USDT）</view>
				<view class="progress flex-center"><view class="value" :style="{'width': app._accMul(item.Rate,100)+'%'}"></view></view> -->
				<view class="flex-center flex-j-between pt-15">
					<text class="font-12 font-grey" v-text="item.State==0?lang.buy31:item.State==1?lang.buy32:lang.buy33"></text>
					<view class="flex-center">
						<!-- <button class="btn" @click="app.showOpen('trade/buyOrder?data='+JSON.stringify(item))">查看</button> -->
						<button class="btn" :disabled="isStop(item)" @click="stopOrder(item)">{{lang.buy34}}</button>
					</view>
				</view>
			</view>
			<view class="base-no-list w-100 pt-30 pb-30 mt-50 text-center" v-if="list.length==0">
				<image mode="widthFix" style="height: 60x;width: 60px !important;" src="../../static/img/9504b34d513f880dbd287c5f72ac000.png"></image>
			</view>
			<uni-load-more :status="loadingType"></uni-load-more>
		</view>
	</view>
</template>
<!-- 0正常，1终止，2完成 -->
<script>
	import config from "@/common/js/config.js";
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		components: {
			uniLoadMore
		},
		data() {
			return {
				page:1,
				count:20,
				list:[],
				loadingType: 'more',
			}
		},
		onLoad() {
			var self=this;
			uni.setNavigationBarTitle({
				title:self.lang.trade5
			});
			self.getList();
		},
		onReachBottom(){
			var self = this;
			self.getList();
		},
		computed:{
			...mapState(['userInfo','assets','lang']),
		},
		methods: {
			isStop:function(item){
				var self=this;
				if(item.State==1||item.State==2||item.Complete_USDT!=0){
					return true;
				}else if(item.State==0&&item.Complete_USDT==0){
					return false;
				};
			},
			stopOrder:function(item){
				var self=this;
				uni.showModal({
					content: self.lang.buy35,
					confirmText: self.lang.buy36,
					cancelText: self.lang.buy37,
					success: function (e) {
						if(e.confirm){
							let send={
								Id:item.Id,
							};
							uni.showLoading({title: self.lang.buy38});
							let url=config.api + "/ctc-order-stop";
							uni.request({
								url: url,
								data: send,
								method: "post",
								header: {Authorization: config.getToken()},
								success: res => {
									// console.log(JSON.stringify(res));
									config.api_status(res);
									if(res.data.status==1){
										self.app._toastIcon(self.lang.buy39);
										setTimeout(function(){
											self.initlist();
										},2500);
									}else{
										self.app._toast(res.data.message);
									};
								},
								fail: (res) => {
									console.log(JSON.stringify(res));
								},
								complete: (res) => {
									setTimeout(function(){
										uni.hideLoading();
									},2000);
								}
							});
						};
					}
				})
			},
			initlist:function(){
				var self=this;
				self.page=1;
				self.list=[];
				self.loadingType="more";
				self.getList();
			},
			getList:function(){
				var self=this;
				if(self.loadingType === 'nomore'){
					return;
				}else{
					self.loadingType = 'loading';
				};
				let send={
					page:self.page,
					count:self.count,
				};
				let url=config.api + "/ctc-my-list";
				uni.request({
					url: url,
					data: send,
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							for(var i=0;i<res.data.data.list.length;i++){
								var item=res.data.data.list[i];
								self.list.push(item);
							};
							if(res.data.data.list.length<self.count){
								self.loadingType = 'nomore';
							}else{
								self.loadingType = 'more';
							};
							self.page++;
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {}
				});
			},
			getcreat:function(inputTime){
				var date = new Date(parseInt(inputTime * 1000));
				var y = date.getFullYear();
				var m = date.getMonth() + 1;
				m = m < 10 ? ('0' + m) : m;
				var d = date.getDate();
				d = d < 10 ? ('0' + d) : d;
				var h = date.getHours();
				h = h < 10 ? ('0' + h) : h;
				var minute = date.getMinutes();
				var second = date.getSeconds();
				minute = minute < 10 ? ('0' + minute) : minute;
				second = second < 10 ? ('0' + second) : second;
				var str=h + ':' + minute + ' '+ y + '-' + m;
				return str;
				// return y + '-' + m + '-' + d +' ' + h + ':' + minute + ':' + second;
			}
		}
	}
</script>

<style>
	page{background-color: #F7F7F7;}
	.cont-list .title{color: #999999;}
	.cont-list .value{color: #333333;}
	.cont-list .progress{width: 100%;height: 10px;background-color: #D8D8D8;border-radius: 5px;padding: 0px;}
	.cont-list .progress .value{height: 100%;display: inline-block;background: linear-gradient(bottom,#FA6C00,#FFB500);border-radius: 5px;}
	button.btn{height: 32px;line-height: 32px;width: 70px;border-radius: 2px;background: linear-gradient(bottom,#FA6C00,#FFB500);font-size: 14px;color: #FFFFFF;margin-left: 12px;}
	button.btn[disabled]{background:#999999 !important;}
</style>
