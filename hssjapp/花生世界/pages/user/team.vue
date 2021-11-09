<template>
	<view class="box">
		<view class="top-data pt-35 pb-35 pl-10 pr-10">
			<view class="data">
				<view class="flex-center flex-j-between">
					<view class="w-50 text-center font-12 font-white op-7">团队总亩数 (亩)</view>
					<view class="w-50 text-center font-12 font-white op-7">{{lang.team2}}</view>
				</view>
				<view class="flex-center flex-j-between mt-5">
					<view class="w-50 text-center font-20 font-white nowrap" v-text="data.total_computing_power">---</view><!-- @click="popSW=true" -->
					<view class="w-50 text-center font-20 font-white nowrap" v-text="data.exchange_people_count">---</view>
				</view>
			</view>
			<view class="data mt-25">
				<view class="flex-center flex-j-between">
					<view class="w-50 text-center font-12 font-white op-7">{{lang.team3}}</view>
					<view class="w-50 text-center font-12 font-white op-7">{{lang.team4}}</view>
				</view>
				<view class="flex-center flex-j-between mt-5">
					<view class="w-50 text-center font-20 font-white nowrap" v-text="data.total_people_count">---</view>
					<view class="w-50 text-center font-20 font-white nowrap" v-text="data.effective_people_count">---</view>
				</view>
			</view>
		</view>
		<view class="pl-10 pr-10">
			<view class="w-100 bc-white br-4 pt-10 pb-10 pl-10 pr-10 mt-15">
				<view class="flex-center flex-j-between font-16 font-grey pb-10">
					{{lang.team5}}
					<text class="font-12 font-grey">{{lang.team6}}：{{data.reward}}</text>
				</view>
				<view class="w-100 flex-center flex-j-between mb-5">
					<view class="flex-center font-12 font-grey newlines">
						<view class="flex-center font-12 font-grey newlines">{{data.info}}<text class="font-18 font-main ml-5">{{data.existing_direct_push_count}}/</text><strong></strong></view>
						<text class="font-12 font-main one-row">{{data.total_direct_push_count}}{{lang.team17}}</text>
					</view>
					
				</view>
				<view class="progress">
					<view class="val" :style="{'width': app._accMul(data.proportion,100)+'%'}"></view>
				</view>
				<view class="font-12 lh-20 w-100 newlines op-8 mt-5" style="color: #8C8C8C;">{{lang.team7}}：{{data.next_level}}</view>
			</view>
			
			<view class="flex-center font-16 font-grey pt-15 pb-15 bt_line mb-10 status">
				<view class="mr-30" :class="{'font-main':status==1}" @click="status=1;init();">{{lang.team8}}</view>
				<view class="mr-30" :class="{'font-main':status==0}" @click="status=0;init();">{{lang.team9}}</view>
			</view>
			<view class="cont-list flex-center flex-j-between br-8 bc-white mb-10" v-for="(item,index) in list" :key="index">
				<view class="head-portrait">
					<image :src="item.Avatar?item.Avatar:'../../static/img/bacc1bf65f6c0e8ed8c29f22df3743b.png'"></image>
					<view class="status text-center" v-text="item.IsAuth==1?lang.team8:lang.team9">已实名</view>
				</view>
				<view class="cont">
					<view class="flex-center flex-j-between">
						<view class="max-w-50 font-14 font-grey nowrap" v-text="item.Name">这里是一个昵称</view>
						<view class="max-w-50 font-12 font-grey nowrap op-7">亩数：{{app._toFixed(item.Power,4)}}</view>
					</view>
					<view class="flex-center flex-j-between mt-5">
						<view class="max-w-50 font-12 font-grey nowrap"><text class="op-6">{{item.Phone}}</text><text class="font-12 font-main ml-5" @click="goPhone(item.RealPhone)">{{lang.team12}}</text></view>
						<view class="max-w-50 font-12 font-grey nowrap op-6">{{lang.team11}}：{{item.Team}}{{lang.team17}}</view>
					</view>
				</view>
			</view>
			<uni-load-more :status="loadingType"></uni-load-more>
		</view>
		<view class="winPopup flex-center flex-j-center pl-20 pr-20" v-if="popSW">
			<view class="grade-content w-100">
				<view class="grade-popup-close font-white" @click="popSW=false">
					<i class="iconfont icon-ziyuan"></i>
				</view>
				<view class="content bc-white pl-15 pr-15 pb-30 w-100 br-10">
					<view class="w-100 text-center font-18 bt_line pb-15 pt-15 lh-20 font-grey">{{lang.team13}}</view>
					<view class="w-100 text-center nowrap font-black mt-20">
						<text class="font-grey one-row mr-5 op-5 font-10">{{lang.team14}}:</text>
						<view class="text-center nowrap font-14" v-text="app._toFixed(data.total_computing_power,4)">0.0000</view>
					</view>
					<view class="flex-center flex-j-between mt-20">
						<view class="w-50 text-center nowrap font-black">
							<text class="font-grey one-row mr-5 op-5 font-10">{{lang.team15}}:</text>
							<view class="text-center nowrap font-14" v-text="app._toFixed(data.total_invite_computing_power,4)">0.0000</view>
						</view>
						<view class="w-50 text-center font-black">
							<text class="font-grey one-row mr-5 op-5 font-10">{{lang.team16}}:</text>
							<view class="text-center nowrap font-14" v-text="app._toFixed(data.total_exchange_computing_power,4)">0.0000</view>
						</view>
					</view>
				</view>
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
				data:{
					total_computing_power:'---',
					// self_computing_power:'---',
					existing_direct_push_count:'---',
					info:'---',
					total_direct_push_count:'---',
					reward:'---',
					next_level:'---',
					// proportion:'---',
					total_people_count:"---",
					effective_people_count:"---",
					exchange_people_count:"---",
					total_invite_computing_power:"---",
					total_exchange_computing_power:"---",
				},
				page:1,
				count:20,
				list:[],
				loadingType: 'more',
				status:1,
				popSW:false,
			}
		},
		onLoad() {
			var self=this;
			self.getData();
			self.init();
			uni.setNavigationBarTitle({
				title:self.lang.user14
			});
		},
		onReachBottom(){
			var self = this;
			self.getRecord();
		},
		onPullDownRefresh() {
			var self=this;
			self.getData();
			self.init();
			setTimeout(function () {
				uni.stopPullDownRefresh();
			}, 500);
		},
		computed:{
			...mapState(['userInfo','qiniu','lang']),
		},
		methods: {
			goPhone:function(num){
				uni.makePhoneCall({
				    phoneNumber: num
				});
			},
			getData:function(){
				var self=this;
				let url=config.api + "/user-my-team-sapling-info";
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
							self.data.total_computing_power=self.app._toFixed(res.data.data.total_computing_power,4);
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
			init:function(){
				var self=this;
				self.page=1;
				self.list=[];
				self.loadingType="more";
				self.getRecord();
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
					auth_status:self.status,
				};
				// console.log(send)
				let url=config.api + "/invite-list";
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
			}
		}
	}
</script>

<style>
	page{background-color: #F7F7F7;}
	.top-data{width: 100%;background: url(../../static/img/9ce733986f3f910123ff2348c2cda48.png) no-repeat;background-size: 100% 100%;}
	.top-data .data{width: 100%;position: relative;}
	.top-data .data::after{position: absolute;top: 10px;bottom: 10px;left: 50%;width: 1px;background-color: #FFFFFF;opacity: 0.6;-webkit-transform: scaleX(.5);;transform: scaleX(.5);content: "";}
	.progress{width: 100%;height: 16px;background-color: #FDEDC7;border-radius: 8px;}
	.progress .val{height: 100%;background:linear-gradient(bottom,#FA6C00,#FFB500);border-radius: 8px;display: inline-block;}
	.cont-list{width: 100%;padding: 13px 14px;}
	.cont-list .head-portrait{position: relative;}
	.cont-list .head-portrait image{width: 56px;height: 56px;display: inline-block;border-radius: 50%;}
	.cont-list .head-portrait .status{background: linear-gradient(#F8E4BA,#F8D792,#FD9253);color: #A0340A;font-size: 10px;width: 100%;height: 15px;line-height: 15px;border-radius: 8px;position: absolute;bottom: 3px;left: 0px;}
	.cont-list .cont{width: calc(100% - 70px);}
	.status .font-main{position: relative;}
	.status .font-main::after{position: absolute;bottom: -16px;left: 0px;right: 0px;height: 3px;border-radius: 3px;background-color: #FFB500;content: "";}
</style>
