<template>
	<view class="box pl-15 pr-15">
		<view class="content">
			<view class="font-16 w-100 pb-20 pt-45 font-16">{{lang.store1}}</view>
			<view class="loop-Img">
				<swiper class="swiper" :autoplay="true" :circular="true" :interval="5000" :duration="1000">
					<swiper-item v-for="(item,index) in banner" :key="index">
						<image class="swiper-item br-4" :src="item.MainImg" @click="look_banner(item)"></image>
					</swiper-item>
				</swiper>
			</view>
			<scroll-view scroll-y="true">
				<view class="list-content">
					<view class="flex-center flex-j-between flex-wrap">
						<view class="w-100 mb-15 font-16 ">土地</view>
						<view class="cont-list mb-10" @click="app.showOpen('store/details?type=sapling&data='+JSON.stringify(jtem))" v-for="(jtem,jndex) in list.sapling" :key="'sapling'+jndex">
							<view class="store-pic">
								<image mode="widthFix" :src="jtem.icon"></image>
							</view>
							<view class="cont pt-15 pb-15 pl-15 pr-15">
								<view class="name text-center pb-5 lh-25 nowrap" v-text="jtem.nickname">爱心体验小树苗</view>
								<view class="progress-cont flex-center nowrap font-10">
									<view class="twig-cont mr-5">
										<view :style="{'width': app._accMul(jtem.proportion,100)+'%'}" class="twig"></view>
									</view>
								</view>
								<!-- <view class="price flex-center flex-j-center nowrap">
									<image src="../../static/img/9792a1060a1f476387b79c20a6292b8.png"></image>
									<view class="font-12 font-dark-main ml-10" v-text="jtem.price+'斤'">100斤</view>
								</view> -->
								<view class="price flex-center flex-j-center nowrap">
									<image :src="jtem.payCoinLogo"></image>
									<view class="font-12 font-dark-main ml-10" v-text="app.toNonExponential(jtem.price)+jtem.payCoinName">100斤</view>
								</view>
							</view>
						</view>
						<view class="w-100 mb-15 font-16 ">老鼠</view>
						<view class="cont-list mb-10" @click="app.showOpen('store/details?type=mouse&data='+JSON.stringify(jtem))" v-for="(jtem,jndex) in list.mouse" :key="'mouse'+jndex">
							<view class="store-pic">
								<image mode="widthFix" :src="jtem.icon"></image>
							</view>
							<view class="cont pt-15 pb-15 pl-15 pr-15">
								<view class="name text-center pb-5 lh-25 nowrap" v-text="jtem.nickname">爱心体验小树苗</view>
								<view class="progress-cont flex-center nowrap font-10">
									<view class="twig-cont mr-5">
										<view :style="{'width': app._accMul(jtem.proportion,100)+'%'}" class="twig"></view>
									</view>
								</view>
								<view class="price flex-center flex-j-center nowrap">
									<image src="../../static/img/9792a1060a1f476387b79c20a6292b8.png"></image>
									<view class="font-12 font-dark-main ml-10" v-text="app.toNonExponential(jtem.price)+'斤'">100斤</view>
								</view>
							</view>
						</view>
						<view class="w-100 mb-15 font-16 ">小狗</view>
						<view class="cont-list mb-10" @click="app.showOpen('store/details?type=dog&data='+JSON.stringify(jtem))" v-for="(jtem,jndex) in list.dog" :key="'dog'+jndex">
							<view class="store-pic">
								<image mode="widthFix" :src="jtem.icon"></image>
							</view>
							<view class="cont pt-15 pb-15 pl-15 pr-15">
								<view class="name text-center pb-5 lh-25 nowrap" v-text="jtem.nickname">爱心体验小树苗</view>
								<view class="progress-cont flex-center nowrap font-10">
									<view class="twig-cont mr-5">
										<view :style="{'width': app._accMul(jtem.proportion,100)+'%'}" class="twig"></view>
									</view>
								</view>
								<view class="price flex-center flex-j-center nowrap">
									<image src="../../static/img/9792a1060a1f476387b79c20a6292b8.png"></image>
									<view class="font-12 font-dark-main ml-10" v-text="app.toNonExponential(jtem.price)+'斤'">100斤</view>
								</view>
							</view>
						</view>
						
					</view>
				</view>
			</scroll-view>
		</view>
		<view class="winPopup flex-center flex-j-center" v-if="rewardSW">
			<reward :show="rewardSW" :rewardSW.sync="rewardSW"></reward>
		</view>
	</view>
</template>

<script>
	import reward from "@/components/reward.vue";
	import config from "@/common/js/config.js";
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		components: {
			reward
		},
		data() {
			return {
				list: [],
				banner:[],
				rewardSW:false,
			}
		},
		onShow() {
			var self=this;
			self.setTabBar();
			uni.setStorageSync("pop",1);
			self.getbanner();
			self.getList();
			self.getReward();
			if(self.RewardInfo.is_giveAway==1&&self.userInfo.auth_status==3){
				self.rewardSW=true;
			}else{
				self.rewardSW=false;
			};
		},
		onLoad() {
			var self=this;
		},
		onPullDownRefresh() {
			var self=this;
			self.getbanner();
			self.getList();
			self.set_assets();
			self.setUserInfo();
			self.getReward();
			setTimeout(function () {
				uni.stopPullDownRefresh();
			}, 1000);
		},
		computed:{
			...mapState(['userInfo','assets','RewardInfo','lang']),
		},
		methods: {
			...mapMutations(["setUserInfo","set_assets","getReward"]),
			setTabBar:function(){
				var self=this;
				uni.setTabBarItem({
					index: 0,
					text: self.lang.tabBar1,
				});
				uni.setTabBarItem({
					index: 1,
					text: self.lang.tabBar2,
				});
				uni.setTabBarItem({
					index: 2,
					text: self.lang.tabBar3,
				});
				uni.setTabBarItem({
					index: 3,
					text: self.lang.tabBar4,
				});
			},
			look_banner:function(item){
				plus.runtime.openURL(item.Link);
			},
			getbanner:function(){
				var self=this;
				let url=config.api + "/article-list";
				uni.request({
					url: url,
					data: {
						CallIndex :"store_banner"
					},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						// config.api_status(res);
						if(res.data.status==1){
							self.banner=res.data.data;
						}else{
							console.log(JSON.stringify(res));
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {}
				});
			},
			getList:function(){
				var self=this;
				let url=config.api + "/store";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.list=res.data.data;
						}else{
							console.log(JSON.stringify(res));
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {}
				});
			},
		},
		watch: {
			hasLogin: function(newValue, oldValue) {
				var self=this;
				if(newValue){
					self.getbanner();
					self.getList();
				};
			},
			RewardInfo:function(newValue){
				var self=this;
				if(newValue){
					if(self.RewardInfo.is_giveAway==1&&self.userInfo.auth_status==3){
						self.rewardSW=true;
					}else{
						self.rewardSW=false;
					};
				};
			}
		}
	}
</script>

<style>
	page{
		height: 100vh;
		background: url(../../static/img/1e0cc92565daf015e510d11505f9289.png) no-repeat;
		background-size: 100% 100%;
		position: relative;
	}
	.box {
		height: 100vh;
		overflow-y: auto;
		padding-bottom: 60px;
		position: relative;
		z-index: 10;
	}
	.scroll-view{
		width: 100%;
		height: 100%;
	}
	.content {
		width: 100%;
		position: relative;
		z-index: 1;
	}
	.loop-Img{width: 100%;position: relative;z-index: 1;margin-bottom: 20px;border-radius: 4px;}
	.loop-Img .swiper{width: 100%;height: 150px;}
	.loop-Img .swiper image{width: 100%;height: 100%;}
	.list-content {
		width: 100%;
	}
	.cont-list {
		width: 48%;
		background-color: #FFFFFF;
		position: relative;
		border-radius: 6px;
	}
	.cont-list .store-pic{
		width: 100%;
		padding: 10px;
		height: 150px;
		display: flex;
		align-items: flex-end;
		justify-content: center;
	}
	.cont-list .store-pic image{
		width: 115px;
		max-height: 100%;
	}
	.cont-list .cont {
		width: 100%;
	}
	.cont-list .cont .name{
		color: #666666;
		font-size: 12px;
	}
	.cont-list .progress-cont {
		width: 100%;
		color: #F8E4BA;
	}
	.cont-list .progress-cont .twig-cont {
		width: 100%;
		height: 9px;
		background-color: #FDEEC6;
		border-radius: 5px;
	}
	.cont-list .progress-cont .twig-cont .twig {
		height: 100%;
		border-radius: 5px;
		background: linear-gradient(bottom,#FA6C00,#FFB500);
	}
	.cont-list .price{
		width: 100%;
		margin-top: 10px;
	}
	.cont-list .price image{
		height: 18px;
		width: 28px;
	}
</style>
