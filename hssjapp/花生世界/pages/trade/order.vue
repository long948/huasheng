<template>
	<view class="box pb-20">
		<view class="nav-cont flex-center flex-j-around bc-white mb-10">
			<view :class="{'active':nav==1}" @click="nav=1;initlist();">{{lang.order1}}</view>
			<view :class="{'active':nav==2}" @click="nav=2;initlist();">{{lang.order2}}</view>
		</view>
		<view class="cont-list bc-white pt-20 pb-20 pl-20 pr-20 mb-10" v-for="(item,index) in list" :key="index" @click="details(item)">
			<view class="flex-center flex-j-between">
				<view class="w-33 nowrap font-12 font-red" v-if="nav==1">{{lang.order3}}PT</view>
				<view class="w-33 nowrap font-12 font-red" v-else-if="nav==2">{{lang.order4}}PT</view>
				<view class="w-33 nowrap font-12 font-red text-center">{{lang.order5}} {{item.RemarkCode}}</view>
				<view class="w-33 nowrap font-12 font-red flex-center flex-j-end">{{get_status(item)}}<i class="iconfont icon-you"></i></view>
			</view>
			<view class="flex-center flex-j-between pt-10 pb-10 ">
				<view class="w-33 nowrap font-10 font-cl-9">{{lang.order6}}</view>
				<view class="w-33 nowrap font-10 font-cl-9 text-center">{{lang.order7}}（PT)</view>
				<view class="w-33 nowrap font-10 font-cl-9 text-right">{{lang.order8}}（CNY)</view>
			</view>
			<view class="flex-center flex-j-between">
				<view class="w-33 nowrap font-12 font-cl-3" v-text="getcreat(item.AddTime)">18:15 10/25</view>
				<view class="w-33 nowrap font-12 font-cl-3 text-center" v-text="app._toFixed(item.Number,4)">100.00000000</view>
				<view class="w-33 nowrap font-12 font-cl-3 text-right" v-text="app._toFixed(app._accMul(item.SumPrice,7),4)">711.00</view>
			</view>
		</view>
		<view class="base-no-list w-100 pt-30 pb-30 mt-50 text-center" v-if="list.length==0">
			<image mode="widthFix" style="height: 60x;width: 60px !important;" src="../../static/img/9504b34d513f880dbd287c5f72ac000.png"></image>
		</view>
		<uni-load-more :status="loadingType"></uni-load-more>
		<!-- 筛选 -->
		<view class="winPopup flex-row flex-j-end" v-if="popupSW" @click="popupSW=false">
			<view class="Popup-content pl-15 pr-15 pt-15" @click.stop="">
				<view class="pb-30">
					<view class="font-14 text-left">{{lang.order9}}</view>
					<view class="choice-list flex-center flex-j-between">
						<view class="name nowrap text-center" :class="{'active':status==0}" @click="status=0">{{lang.order10}}</view>
						<view class="name nowrap text-center" :class="{'active':status==1}" @click="status=1">{{lang.order11}}</view>
						<view class="name nowrap text-center" :class="{'active':status==2}" @click="status=2">{{lang.order12}}</view>
						<view class="name nowrap text-center" :class="{'active':status==2}" @click="status=3">{{lang.order13}}</view>
						<view class="name nowrap text-center" :class="{'active':status==2}" @click="status=4">{{lang.order14}}</view>
						<view class="name nowrap text-center" :class="{'active':status==2}" @click="status=5">{{lang.order15}}</view>
					</view>
				</view>
				<view class="pb-30">
					<view class="font-14 text-left">{{lang.order16}}</view>
					<view class="Input-cont flex-center flex-j-between mt-15">
						<input type="text" :placeholder="lang.order17" v-model="min" />
						<input type="text" :placeholder="lang.order18" v-model="max" />
					</view>
				</view>
				<view class="pb-30">
					<view class="font-14 text-left">{{lang.order19}}</view>
					<view class="Input-time flex-center flex-j-center mt-15 font-12 font-grey nowrap" @click="open">
						{{info.startDate}}-{{info.endDate}}
					</view>
				</view>
				<view class="btn-cont pt-25">
					<button class="btn" @click="popupSW=false;initlist()">{{lang.order20}}</button>
				</view>
				<uni-calendar ref="calendar" :date="info.date" :insert="info.insert" 
					:lunar="info.lunar" :startDate="info.startDate" :endDate="info.endDate" 
					:range="info.range" @confirm="confirm" />
			</view>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	import uniCalendar from '@/components/uni-calendar/uni-calendar.vue'
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		components: {
			uniLoadMore,
			uniCalendar
		},
		data() {
			return {
				page:1,
				count:20,
				list:[],
				loadingType: 'more',
				nav:1,
				popupSW:false,
				status:0,
				min:"",
				max:"",
				showCalendar: false,
				info: {
					date: '2020-01-01',
					// startDate: '2020-01-01',
					// endDate: '2090-01-01',
					startDate: '',
					endDate: '',
					lunar: true,
					range: true,
					insert: false,
					selected: []
				},
				start:"",
				end:"",
			}
		},
		onLoad() {
			var self=this;
			self.getList();
		},
		onNavigationBarButtonTap(e) {
			var self=this;
			if(e.index==0){
				self.popupSW=true;
			};
		},
		onReachBottom(){
			var self = this;
			self.getList();
		},
		computed:{
			...mapState(['userInfo','assets','lang']),
		},
		methods: {
			details:function(item){
				if(item.State==3||item.State==4){
					return
				}
				this.app.showOpen('trade/orderDetails?data='+JSON.stringify(item))
			},
			confirm(e) {
				var self=this;
				let before=new Date(e.range.before).getTime()/1000;
				let after=new Date(e.range.after).getTime()/1000;
				if(before>after){
					self.info.startDate=e.range.after;
					self.info.endDate=e.range.before;
					self.start=new Date(e.range.after).getTime()/1000;
					self.end=new Date(e.range.before).getTime()/1000;
				}else{
					self.info.startDate=e.range.before;
					self.info.endDate=e.range.after;
					self.start=new Date(e.range.before).getTime()/1000;
					self.end=new Date(e.range.after).getTime()/1000;
				};
			},
			open() {
				this.$refs.calendar.open()
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
					order_type:self.nav,//订单类型（1求购 2出售）
					order_status:self.status,//订单状态 0全部 1待付款 2待确认 3完成 4取消 5申诉
					min:self.min,
					max:self.max,
					start:self.start,
					end:self.end,
					page:self.page,
					count:self.count,
				};
				let url=config.api + "/ctc-trade-my-list";
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
			get_status:function(item){
				var self=this;
				if(item.State==0){
					return self.lang.order11;
				}else if(item.State==1){
					return self.lang.order12;
				}else if(item.State==2){
					return self.lang.order13;
				}else if(item.State==3){
					return self.lang.order21;
				}else if(item.State==4){
					return self.lang.order22;
				}else if(item.State==5){
					return self.lang.order15;
				};
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
				var str=h + ':' + minute + ' '+ m + '-' + d;
				return str;
				// return y + '-' + m + '-' + d +' ' + h + ':' + minute + ':' + second;
			}
		}
	}
</script>

<style>
	page{background-color: #F7F7F7;}
	.nav-cont{border-bottom: 10;}
	.nav-cont view{padding: 10px 0px;position: relative;color: #8C8C8C;font-size: 14px;}
	.nav-cont view.active{color: #FA6C00;}
	.nav-cont view.active::after{position: absolute;bottom: 0px;left: 0px;right: 0px;height: 2px;background-color: #FA6C00;content: "";border-radius: 2px;}
	.winPopup{background-color: transparent;}
	.Popup-content{width: 70%;height: 100%;overflow-y: auto;position: relative;background-color: rgba(2,2,2,0.7);}
	.Popup-content .choice-list{width: 100%;flex-wrap: wrap;position: relative;}
	.Popup-content .choice-list::after{width: 30%;content: "";}
	.Popup-content .choice-list .name{display: inline-block;width: 28%;line-height: 24px;font-size: 12px;color: #FFB500;border: 1px solid #FFB500;border-radius: 2px;margin-top: 16px;}
	.Popup-content .choice-list .name.active{color: #BD9E70;background:linear-gradient(bottom,#FA6C00,#FFB500);border: 0px;color: #FFFFFF;}
	.Popup-content .Input-cont{width: 100%;height: 45px;border-radius: 2px;background-color: #F5F5F5;overflow: hidden;position: relative;}
	.Popup-content .Input-cont input{width: calc(50% - 15px);height: 100%;text-align: center;font-size: 14px;color: #333333;padding: 0px 5px;}
	.Popup-content .Input-cont::after{width: 30px;height: 1px;background-color: #979797;top: 50%;left: calc(50% - 15px);position: absolute;content: "";}
	.Popup-content .Input-time{width: 100%;height: 45px;border-radius: 2px;background-color: #F5F5F5;overflow: hidden;position: relative;}
</style>
