<template>
	<view class="box pl-15 pr-15 pt-15 pb-15">
		<view class="cont-list w-100 br-5 mb-15" v-for="(item,index) in list" :key="index" @click="app.showOpen('index/newsDetails?id='+item.Id)">
			<view class="title show-row w-100" v-text="item.TypeTitle">
				时间倒序排列，时间倒序排列！。
			</view>
			<view class="flex-center flex-j-between mt-5">
				<text class="time" v-text="app._formatDate(item.AddTime)">2019-05-31</text>
				<view class="look flex-center">{{lang.news3}}<i class="iconfont icon-you"></i></view>
			</view>
		</view>
		<!-- <uni-load-more :status="loadingType"></uni-load-more> -->
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
			uniLoadMore,
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
			self.getList();
		},
		computed:{
			...mapState(['userInfo','assets','lang']),
		},
		// onReachBottom(){
		// 	var self = this;
		// 	self.getList();
		// },
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
					CallIndex:"notice",
					// page:self.page,
					// count:self.count,
				};
				let url=config.api + "/article-list";
				uni.request({
					url: url,
					data: send,
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.list=res.data.data
							// for(var i=0;i<res.data.data.length;i++){
							// 	var item=res.data.data[i];
							// 	self.list.push(item);
							// };
							// if(res.data.data.length<self.count){
							// 	self.loadingType = 'nomore';
							// }else{
							// 	self.loadingType = 'more';
							// };
							// self.page++;
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
	.cont-list{
		box-shadow: 0px 1px 6px #F5F5F5	;
		padding: 17px 13px;
	}
	.title{
		width: 100%;
		color: #333333;
		font-size: 12px;
	}
	.time{
		color: #999999;
		font-size: 11px;
	}
	.look{
		color: #999999;
		font-size: 11px;
	}
	.look .iconfont{
		margin-left: 5px;
		color: #999999;
	}
</style>
