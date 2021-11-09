<template>
	<view class="box">
		<view class="page-close font-white" @click="app.goBack()"><i class="iconfont icon-ziyuan"></i></view>
		<view class="content">
			<view class="page-title w-100 text-center font-18 mt-25 mb-25">
				{{lang.news1}}
				<text class="read font-dark-main" v-if="read_status" @click="allRead()">{{lang.news2}}</text>
				<text class="read" v-else>{{lang.news2}}</text>
			</view>
			<view class="pl-15 pr-15">
				<view class="cont-list br-3 bc-white font-14 mb-10" :class="{'active':item.IsRead==0}" v-for="(item,index) in list" :key="index" @click="item.IsRead=1;look(item);">
					<view class="cont nowrap" v-text="item.Title">您的订单有新动态</view>
				</view>
				<uni-load-more :status="loadingType"></uni-load-more>
			</view>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js"
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
				read_status:true,
			}
		},
		onLoad() {
			var self = this;
			self.getRecord();
		},
		onReachBottom(){
			var self = this;
			self.getRecord();
		},
		computed:{
			...mapState(['userInfo','assets','lang']),
		},
		methods: {
			...mapMutations(["setUserInfo","logout"]),
			look:function(item){
				var self=this;
				self.app.showOpen('index/details?id='+item.Id)
			},
			allRead:function(){
				var self=this;
				let val=uni.getStorageSync("userInfo");
				val.notify=0;
				uni.setStorageSync("userInfo",val);
				
				self.read_status=false;
				let url=config.api + "/post.read.all";
				uni.request({
					url: url,
					data: {},
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						self.setUserInfo();
						if(res.data.status==1){
							self.list.forEach(function(item){
								item.IsRead=1;
							});
						}else{
							self.app._toast(res.data.message);
							console.log(JSON.stringify(res));
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						setTimeout(function(){
							self.read_status=true;
						},2000);
					}
				});
			},
			getRecord:function(){
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
				let url=config.api + "/notice-list";
				uni.request({
					url: url,
					data: send,
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							for(var i=0;i<res.data.data.length;i++){
								var item=res.data.data[i];
								self.list.push(item);
							};
							if(res.data.data.length<self.count){
								self.loadingType = 'nomore';
							}else{
								self.loadingType = 'more';
							};
							self.page++;
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
			look_details:function(){
				uni.navigateTo({
					url:"newsDetails"
				})
			}
		}
	}
</script>

<style>
	page{
		height: 100vh;
		background: url(../../static/img/1e0cc92565daf015e510d11505f9289.png) no-repeat;
		background-size: 100% 100%;
		overflow-y: auto;
		position: relative;
	}
	page::after{
		position: absolute;
		top: 0px;
		left: 0px;
		right: 0px;
		bottom: 0px;
		content: "";
		background-color: rgba(4,4,4,0.4);
	}
	.page-close{position: fixed;top: 30px;right: 0px;padding-right: 20px;z-index: 10;}
	.page-close .iconfont{font-size: 25px;}
	.content{
		width: 100%;
		height: calc(100vh - 60px);
		position: fixed;
		left: 0px;
		bottom: 0px;
		background-color: #F2F2F2;
		border-radius: 10px 10px 0px 0px;
		overflow-y: scroll;
		z-index: 1;
	}
	.page-title{color: #3C3C3C;position: relative;line-height: 18px;}
	.page-title .read{position: absolute;right: 0px;top: 0px;line-height: 18px;padding: 0px 20px;color: #3C3C3C;font-size: 12px;opacity: 0.7;}
	.cont-list{width: 100%;padding: 10px 13px;color: rgba(60,60,60,0.7);position: relative;}
	.cont-list .cont{color: rgba(60,60,60,0.7);}
	.cont-list.active{color: #3C3C3C;}
	.cont-list.active .cont{color: #3C3C3C;}
	.cont-list.active::after{position: absolute;top: -4px;left: -4px;width: 8px;height: 8px;display: inline-block;border-radius: 50%;background-color: #FF0000;content: "";}
</style>
