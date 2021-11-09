<template>
	<view class="box">
		<view class="cont-list bc-white pt-20 pb-20 pl-20 pr-20 mb-10" v-for="(item,index) in list" :key="index" @click="app.showOpen('trade/orderDetails?data='+JSON.stringify(item))">
			<view class="flex-center flex-j-between">
				<view class="w-33 nowrap font-12 font-red">{{lang.buy17}}USDT</view>
				<view class="w-33 nowrap font-12 font-red text-center">{{lang.buy18}} {{item.RemarkCode}}</view>
				<view class="w-33 nowrap font-12 font-red flex-center flex-j-end">{{get_status(item)}}<i class="iconfont icon-you"></i></view>
			</view>
			<view class="flex-center flex-j-between pt-10 pb-10 ">
				<view class="w-33 nowrap font-10 font-cl-9">{{lang.buy19}}</view>
				<view class="w-33 nowrap font-10 font-cl-9 text-center">{{lang.buy20}}（USDT)</view>
				<view class="w-33 nowrap font-10 font-cl-9 text-right">{{lang.buy21}}（CNY)</view>
			</view>
			<view class="flex-center flex-j-between">
				<view class="w-33 nowrap font-12 font-cl-3" v-text="getcreat(item.AddTime)">18:15 10/25</view>
				<view class="w-33 nowrap font-12 font-cl-3 text-center" v-text="app._toFixed(item.Number,4)">100.00000000</view>
				<view class="w-33 nowrap font-12 font-cl-3 text-right" v-text="app._toFixed(item.SumPrice,4)">711.00</view>
			</view>
		</view>
		<uni-load-more :status="loadingType"></uni-load-more>
	</view>
</template>

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
				id:"",
				page:1,
				count:20,
				list:[],
				loadingType:'more',
			}
		},
		onLoad(e) {
			var self=this;
			self.id=JSON.parse(e.data).Id;
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
			get_status:function(item){
				if(item.State==0){
					return self.lang.buy22;
				}else if(item.State==1){
					return self.lang.buy23;
				}else if(item.State==2){
					return self.lang.buy24;
				}else if(item.State==3){
					return self.lang.buy25;
				}else if(item.State==4){
					return self.lang.buy26;
				}else if(item.State==5){
					return self.lang.buy27;
				};
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
					Id:self.id,
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
</style>
