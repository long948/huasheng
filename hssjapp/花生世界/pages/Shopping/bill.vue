<template>
	<view class="box">
		<view class="w-100 bc-white pl-16 pr-16 pt-5">
			<view class="search-cont">
				<input type="text" placeholder="请输入商品名称搜索订单" v-model="search" @confirm="pageRefresh()"  />
			</view>
			<view class="w-100 flex-center flex-j-between">
				<view class="nav-item" :class="{'active':nav==0}" @click="nav=0;pageRefresh();">全部</view>
				<view class="nav-item" :class="{'active':nav==1}" @click="nav=1;pageRefresh();">待付款</view>
				<view class="nav-item" :class="{'active':nav==2}" @click="nav=2;pageRefresh();">待成团</view>
				<view class="nav-item" :class="{'active':nav==3}" @click="nav=3;pageRefresh();">已完成</view>
				<view class="nav-item" :class="{'active':nav==4}" @click="nav=4;pageRefresh();">已取消</view>
			</view>
		</view>
		<view class="w-100 pt-12 pb-12 pl-16 pr-16">
			<view class="w-100 bc-white br-4 pl-12 pr-12 pb-15 mb-12 cont-list" v-for="(item,index) in list" :key="index" @click="app.showOpen('Shopping/order-details?data='+JSON.stringify(item))">
				<view class="w-100 flex-center flex-j-between pt-13 pb-12">
					<!-- 0全部 1待付款  2待成团 3已完成 4 已取消 -->
					<!-- <view class="flex-center nowrap">
						<image class="status mr-8" src="../../static/img/b1d1b3297c737adbd7a604d322885cc.png" v-if="item.status==1 || item.status==2"></image>
						<image class="status mr-8" src="../../static/img/13c0d368b42f685a1a3682160b63259.png" v-else-if="item.status==4"></image>
						<image class="status mr-8" src="../../static/img/5c18f82064ca192e2b5348a107c50b7.png" v-else-if="item.status==3 && !item.is_lock_draw"></image>
						<image class="status mr-8" src="../../static/img/f613cbd5966ee300b6e84bc39ed5749.png" v-else-if="item.status==3 && item.is_lock_draw"></image>
						<text class="font-12 font-c-3 nowrap" v-text="item.found.info">超时支付取消</text>
					</view> -->
					<!-- 待付款|待成团 -->
					<view class="flex-center nowrap" v-if="item.status==1 || item.status==2">
						<image class="status mr-8" src="../../static/img/b1d1b3297c737adbd7a604d322885cc.png"></image>
						<text class="font-12 font-c-3 nowrap" v-if="item.status==1">距订单关闭还有{{getBtTime(item)}}</text>
						<text class="font-12 font-c-3 nowrap" v-if="item.status==2">
							<block v-if="item.ctInfo">
								结算中
							</block>
							<block v-else>
								距成团截止还有{{getBtTime(item)}}
							</block>
						</text>
					</view>
					<!-- 已完成 -->
					<view class="flex-center nowrap" v-else-if="item.status==3 && !item.is_lock_draw">
						<image class="status mr-8" src="../../static/img/5c18f82064ca192e2b5348a107c50b7.png"></image>
						<text class="font-12 font-c-3 nowrap">很遗憾，您未中签</text>
					</view>
					<view class="flex-center nowrap" v-else-if="item.status==3 && item.is_lock_draw">
						<image class="status mr-8" src="../../static/img/f613cbd5966ee300b6e84bc39ed5749.png"></image>
						<text class="font-12 font-c-3 nowrap">恭喜中签！</text>
					</view>
					<!-- 已取消 -->
					<view class="flex-center nowrap" v-else-if="item.status==4">
						<image class="status mr-8" src="../../static/img/13c0d368b42f685a1a3682160b63259.png"></image>
						<text class="font-12 font-c-3 nowrap">订单已取消</text>
						<!-- <text class="font-12 font-c-3 nowrap">支付超时，订单已取消</text> -->
						<!-- <text class="font-12 font-c-3 nowrap">未能成团，订单已取消</text> -->
					</view>
					
					<view class="one-row status font-12" v-if="item.status==1">待付款</view>
					<view class="one-row status font-12" v-else-if="item.status==2">
						{{item.ctInfo?'结算中':'待成团'}}
					</view>
					<view class="one-row status font-12" v-else-if="item.status==3">已完成</view>
					<view class="one-row status font-12" v-else-if="item.status==4">已取消</view>
				</view>
				<view class="w-100 flex-row flex-j-between cont-data">
					<image class="br-4" :src="item.good.original_img"></image>
					<view class="cont">
						<view class="w-100 font-15 font-c-3 newlines lh-22 show-row">
							<view class="btn one-row">
								{{item.active.needer}}中{{item.active.stock_limit}}
							</view>
							{{item.good.good_name}}
						</view>
						<view class="w-100 flex-center flex-j-between mt-20">
							<view class="nowrap font-10 max-w-50 font-dark-main" v-if="item.status==3 && item.is_lock_draw">恭喜获得{{item.active.luck_amount}} {{item.active.luckCoinName}}</view>
							<view class="nowrap font-10 max-w-50 font-dark-main" v-else>未中可获得{{Number(item.active.return_amount)+Number(item.active.team_price)}} {{item.active.payCoinName}}</view>
							<view class="nowrap font-10 max-w-50 font-dark-main">
								<!-- <text class="font-14 font-c-9">需付:</text> -->
								<text class="font-14 font-dark-main" v-text="item.active.team_price">100</text> 
								<text class="font-10 font-c-3" v-text="item.active.payCoinName">斤花生</text>
							</view>
						</view>
					</view>
				</view>
			</view>
		</view>
		<uni-load-more :status="loadingType"></uni-load-more>
		<view class="winPopup flex-center flex-j-center" v-if="loader">
			<loader type="loader2"></loader>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	import loader from "@/components/loader/loader.vue";
	export default {
		components: {
			loader,
		},
		data() {
			return {
				loader:false,
				page: 1,
				list: [],
				loadingType: 'more',
				//状态 0全部 1待付款 2待成团 3已完成 4已取消
				nav:0,
				search:"",
				nw:"",
			}
		},
		onLoad() {
			let self=this;
			this.pageRefresh();
			this.nw=new Date().getTime();
			setInterval(function(){
				self.nw=new Date().getTime();
			},1000);
		},
		methods: {
			getBtTime:function(item,type){
				var that = this;
				var end;
				if(item.status=='2'){
					end = ((item.found.found_end_time*1000));
				}else if(item.status=='1'){
					end = ((item.end_pay_time*1000));
				};
				var leftTime = end - this.nw; //时间差    
				var d, h, m, s, ms;
				if (leftTime > 0) {
					d = Math.floor(leftTime / 1000 / 60 / 60 / 24);
					h = Math.floor(leftTime / 1000 / 60 / 60 % 24);
					m = Math.floor(leftTime / 1000 / 60 % 60);
					s = Math.floor(leftTime / 1000 % 60);
					ms = Math.floor(leftTime % 1000);
					ms = ms < 100 ? "0" + ms : ms
					s = s < 10 ? "0" + s : s
					m = m < 10 ? "0" + m : m
					h = h < 10 ? "0" + h : h
					let res=h + ":" + m + ":" + s
					if(item.status=='2'){
						item.CTtime=res;
					}else if(item.status=='1'){
						item.time=res;
					};
					return h + ":" + m + ":" + s
					//递归每秒调用countTime方法，显示动态时间效果
				} else {
					if(item.status=='2'){
						item.CTtime='已截止';
						item.ctInfo=true;
					}else if(item.status=='1'){
						item.time='已截止';
						item.status=4;
					};
					return '已截止'
				}
			},
			pageRefresh: function() {
				var self = this;
				uni.hideKeyboard();
				self.page = 1;
				self.list = [];
				self.loadingType = 'more';
				self.getBill();
			},
			getBill:function(){
				var self=this;
				if (self.loadingType === 'nomore') {
					return;
				} else {
					self.loadingType = 'loading';
				};
				self.loader=true;
				let url=config.api + "/shop.my.found";
				let send={
					page:self.page,
					limit:20,
					//状态 0全部 1待付款 2待成团 3已完成 4已取消
					status:self.nav,
					search:self.search,
				}
				uni.request({
					url: url,
					data: send,
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							for (var i = 0; i < res.data.data.length; i++) {
								var item = res.data.data[i];
								self.list.push(item);
							};
							if (res.data.data.length==0 || res.data.data.length<20) {
								self.loadingType = 'nomore';
							} else {
								self.loadingType = 'more';
							};
							self.page++;
						}else{
							self.app._toast(res.data.message);
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

<style lang="less">
	@import url("@/common/newStyle/base.css");
	@import url("@/common/newStyle/iconfont.css");
	@import url("@/common/newStyle/common.css");
	page{
		background-color: #F7F7F7;
	}
	.search-cont{
		width: 100%;height: 40px;border-radius: 20px;background-color: #F7F7F7;padding: 0px 12px;
		input{
			width: 100%;height: 100%;padding: 0px 10px;font-size: 14px;color: #333333;
		}
	}
	.nav-item{
		color: #333333;font-size: 14px;padding: 20px 0px;
	}
	.nav-item.active{
		color: #FA6C00;font-weight: bold;
	}
	.nav-item.active::after{
		position: absolute;bottom: 8px;left: 0px;width: 100%;height: 2px;background-color: #FA6C00;content: "";
	}
	.cont-list{
		image.status{
			width: 15px;height: 15px;
		}
		.status{
			color: #FF2021;
		}
		.cont-data{
			image{
				width: 83px;height: 83px;background-color: #E6E7F3;
			}
			.cont{
				width: calc(100% - 90px);
				.btn{
					height: 18px;line-height: 18px;padding: 0px 5px;margin: 0px;
					display: inline-block;background: linear-gradient(bottom,#FA6C00,#FFB500);
					color: #FFFFFF;border-radius: 2px;font-size: 10px;bottom: 2px;margin-right: 3px;
				}
			}
		}
	}
</style>
