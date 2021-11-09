<template>
	<view class="box" @click="timeSW=false">
		<view class="pl-16 pr-16">
			<view class="flex-center flex-j-between pb-15 pt-15 bt_line">
				<view class="flex-center font-14 font-grey" @click.stop="">
					<view class="choice-time font-14 font-grey flex-center font-16" @click="timeSW=true">
						<view>{{y+'-'+m}}<i class="iconfont icon-shang-copy ml-10"></i></view>
						<view class="time-list" v-if="timeSW" @click.stop="">
							<view class="ct one-row font-gray font-14 text-center" v-text="year+'-'+(index+1)" @click="y=year;m=(index+1);timeSW=false;initlist();" v-for="(item,index) in 12" :key="index"></view>
						</view>
					</view>
				</view>
				<image @click="popupSW=true" style="width: 20px;height: 22px;" src="../../static/img/781ddd3707b7d86d6da83e817919f9e.png"></image>
			</view>
			<view class="cont-list bt_line flex-center flex-j-between pt-15 pb-20" v-for="(item,index) in list" :key="index">
				<image :src="item.Logo"></image>
				<view class="cont">
					<view class="flex-center flex-j-between">
						<view class="max-w-50 nowrap font-black font-14" v-text="item.CoinName">BTC</view>
						<view class="max-w-50 nowrap font-14 font-main" :class="{'font-dark-red':Number(item.Money)<0}" v-text="app._toFixed(item.Money,4)">+316546.015421</view>
					</view>
					<view class="flex-center flex-j-between mt-5">
						<view class="max-w-50 nowrap font-10 op-5 font-black" v-text="app._formatDate(item.AddTime)">2020-01-19 08:02</view>
						<view class="max-w-50 nowrap font-grey font-12" v-text="item.MoldTitle">提现</view>
					</view>
				</view>
			</view>
			<view class="base-no-list w-100 pt-30 pb-30 mt-50 text-center" v-if="list.length==0">
				<image mode="widthFix" style="height: 60x;width: 60px !important;" src="../../static/img/9504b34d513f880dbd287c5f72ac000.png"></image>
			</view>
			<uni-load-more :status="loadingType"></uni-load-more>
		</view>
		<!-- 筛选 -->
		<view class="winPopup flex-row flex-j-end" v-if="popupSW" @click="popupSW=false">
			<view class="Popup-content pl-15 pr-15 pt-15" @click.stop="">
				<view class="pb-30">
					<view class="font-14 text-left">{{lang.wallet8}}</view>
					<view class="choice-list flex-center flex-j-between">
						<view class="name nowrap text-center" :class="{'active':TxType==0}" @click="TxType=0">{{lang.wallet9}}</view>
						<view class="name nowrap text-center" :class="{'active':TxType==1}" @click="TxType=1">{{lang.wallet10}}</view>
						<view class="name nowrap text-center" :class="{'active':TxType==2}" @click="TxType=2">{{lang.wallet11}}</view>
					</view>
				</view>
				<view class="pb-30">
					<view class="font-14 text-left">{{lang.wallet12}}</view>
					<view class="choice-list flex-center flex-j-between">
						<view class="name nowrap text-center" :class="{'active':type==0}" @click="type=0">{{lang.wallet9}}</view>
						<view class="name nowrap text-center" :class="{'active':type==item.id}" @click="type=item.id" v-for="(item,index) in typelist" :key="index" v-text="item.title">全部</view>
					</view>
				</view>
				<view class="pb-30">
					<view class="font-14 text-left">币种</view>
					<view class="choice-list flex-center flex-j-between">
						<view class="name nowrap text-center" :class="{'active':coinInfo==0}" @click="coinInfo=0">全部</view>
						<view class="name nowrap text-center" :class="{'active':coinInfo==item.Id}" @click="coinInfo=item.Id" v-for="(item,index) in coinList" :key="index" v-text="item.EnName">全部</view>
					</view>
				</view>
				<view class="pb-30">
					<view class="font-14 text-left">{{lang.wallet13}}</view>
					<view class="Input-cont flex-center flex-j-between mt-15">
						<input type="text" :placeholder="lang.wallet14" v-model="min" />
						<input type="text" :placeholder="lang.wallet15" v-model="max" />
					</view>
				</view>
				<view class="btn-cont pt-25">
					<button class="btn" @click="popupSW=false;initlist()">{{lang.wallet16}}</button>
				</view>
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
				data:{
					EnName:"--",
					FullName:"--",
					Logo:"--",
					CoinPrice:"--",
					MoneyUSDT:"--",
					Available:"--",
					Forzen:"--",
				},
				page:1,
				count:20,
				list:[],
				loadingType: 'more',
				typelist:[],
				popupSW:false,
				TxType:0,//0全部 1转入 2转出
				type:0,
				min:"",
				max:"",
				// 时间
				timeSW:false,
				year:"",
				time:"",
				y:"",
				m:"",
				// 币种
				coinInfo:0,
			}
		},
		onLoad() {
			var self=this;
			// self.time=self.getTime(0);
			// self.getData();
			self.getTime(0)
			self.getType();
			self.getList();
			
		},
		onReachBottom(){
			var self = this;
			self.getList();
		},
		computed:{
			...mapState(['userInfo','assets','lang','coinList']),
		},
		methods: {
			getTime:function(num){
				var self=this;
				if(!num){num=0;};
				var time=new Date();
				time=time.getTime()+(num*24)*(60*60*1000);
				var t=new Date(time);
				self.year=t.getFullYear()
				let m=(t.getMonth()+1)<10?'0'+(t.getMonth()+1):(t.getMonth()+1);
				let d=t.getDate()<10?'0'+t.getDate():t.getDate();
				var str=t.getFullYear()+"-" + m;
				// return str;
				self.y=t.getFullYear();
				self.m=m;
			},
			getType:function(){
				var self=this;
				let url=config.api + "/finace-molds";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						// config.api_status(res);
						if(res.data.status==1){
							self.typelist=res.data.data;
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {}
				});
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
					min:self.min,
					max:self.max,
					TxType:self.TxType,
					Type:self.type,
					page:self.page,
					count:self.count,
					year:self.y,
					month:self.m,
					coinId:self.coinInfo,
				};
				let url=config.api + "/finace-list";
				uni.request({
					url: url,
					data: send,
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						console.log(JSON.stringify(res));
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
			getData:function(){
				var self=this;
				let url=config.api + "/get.wallet.info";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						// config.api_status(res);
						if(res.data.status==1){
							self.data=res.data.data;
							console.log(self.data);
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
		}
	}
</script>

<style>
	page{background-color: #F7F7F7;}
	.box{padding-bottom: 60px;min-height: 80vh;}
	.coin-data{width: 100%;}
	.coin-data image{width: 38px;height: 38px;border-radius: 50%;}
	.coin-data .cont{width: calc(100% - 50px);}
	.top-data{width: 100%;background: url(../../static/img/502f5141feca10b300ea54d4a73d6b2.png) no-repeat;background-size: 100% 100%;}
	.cont-list image{width: 24px;height: 24px;border-radius: 50%;}
	.cont-list .cont{width: calc(100% - 35px);}
	/* .cont-list .cont{width: 100%;} */
	.foot-cont{width: 100%;height: 60px;position: fixed;left: 0px;bottom: 0px;z-index: 10;}
	.foot-cont image{width: 16px;height: 18px;margin-right: 5px;}
	.foot-cont .cont{width: 50%;height: 100%;font-size: 14px;color: #FFFFFF;}
	.foot-cont .cont:first-child{background-color: #FFB500;}
	.foot-cont .cont:last-child{background-color: #FA6C00;}
	.winPopup{background-color: transparent;}
	.Popup-content{width: 70%;height: 100%;overflow-y: auto;position: relative;background-color: rgba(2,2,2,0.7);}
	.Popup-content .choice-list{width: 100%;flex-wrap: wrap;position: relative;}
	.Popup-content .choice-list::after{width: 30%;content: "";}
	.Popup-content .choice-list .name{display: inline-block;width: 28%;line-height: 24px;font-size: 12px;color: #FFB500;border: 1rpx solid #FFB500;border-radius: 3px;margin-top: 16px;}
	.Popup-content .choice-list .name.active{color: #FFFFFF;background:linear-gradient(bottom,#FA6C00,#FFB500);border: 0px;color: #FFFFFF;}
	.Popup-content .Input-cont{width: 100%;height: 45px;border-radius: 2px;background-color: #F5F5F5;overflow: hidden;position: relative;}
	.Popup-content .Input-cont input{width: calc(50% - 15px);height: 100%;text-align: center;font-size: 14px;color: #333333;padding: 0px 5px;}
	.Popup-content .Input-cont::after{width: 30px;height: 1px;background-color: #979797;top: 50%;left: calc(50% - 15px);position: absolute;content: "";}
	.choice-time{position: relative;z-index: 1;}
	.choice-time .time-list{position: absolute;top: 25px;left: 0px;width: auto;height: 200px;overflow-y: auto;background-color: #FFFFFF;box-shadow: 0px 2px 6px #E9E8E8;
		border-radius: 5px;padding: 10px 0px;;
	}
	.choice-time .time-list .ct{line-height: 30px;padding: 0px 15px;}
	.pl-16{
		padding-left: 16px;
	}
	.pr-16{
		padding-right: 16px;
	}
</style>
