<template>
	<view class="box pl-15 pr-15">
		<view class="cont-list pt-15 pb-15 bt_line" v-for="(item,index) in list" :key="index">
			<view class="flex-center flex-j-between font-16 font-black">
				<view class="max-w-50 nowrap" :class="{'font-dark-green':item.method=='进账','font-dark-red':item.method=='出账'}" v-text="item.amount">-13.4524</view><view v-text="item.method">充值状态</view>
			</view>
			<view class="flex-center flex-j-between pt-10">
				<view class="font-12 font-cl-9 max-w-50 nowrap" v-text="item.type">充值加油卡100</view>
				<view class="font-12 font-cl-9" v-text="item.create_time">2020/06/24  17:30:07</view>
			</view>
			<!-- <view class="w-100 pt-10 font-12 font-cl-9" v-if="item.remarks" v-text="'备注：'+item.remarks"></view> -->
		</view>
		<uni-load-more :status="loadingType"></uni-load-more>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	export default {
		components: {
			uniLoadMore,
		},
		data() {
			return {
				page:1,
				list:[],
				loadingType: 'more',
			}
		},
		onLoad() {
			var self=this;
			self.getList();
		},
		onReachBottom(){
			var self = this;
			self.getList();
		},
		methods: {
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
					count:20,
				};
				let url=config.api + "/member-giveaway-list";
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
							if(res.data.data.length<20){
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
		}
	}
</script>

<style>
	page{background-color: #FFFFFF;}
	.font-dark-green{color: green !important;}
	.font-dark-red{color: red !important;}
</style>
