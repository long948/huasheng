<template>
	<view class="box pl-15 pr-15">
		<view class="font-w-b font-c-3 font-16 pt-18 pb-18">拼土地专场</view>
		<view class="w-100 flex-center flex-j-between flex-wrap">
			<view class="cont-list mb-12" v-for="(item,index) in data.good" :key="index" @click="app.showOpen('Shopping/commodity?data='+JSON.stringify(item))">
				<view class="product-pic br-4 flex-center flex-j-center">
					<view class="type font-white font-12 nowrap">{{item.active.needer}}中{{item.active.stock_limit}}</view>
					<image :src="item.original_img"></image>
				</view>
				<view class="w-100 pl-8 pr-8 pt-12 pb-15">
					<view class="w-100 font-15 font-c-3 pb-8 nowrap" v-text="item.goods_name">金花生--可兑换逍</view>
					<view class="w-100 flex-center flex-j-between">
						<view class="flex-row flex-end nowrap price">
							<text class="font-16 font-dark-main lh-16 nowrap" v-text="item.active.team_price">1000000000</text>
							<text class="font-10 font-c-9 pt-3 ml-2 one-row" v-text="item.active.payCoinName">USDT</text>
						</view>
						<view class="btn font-10 font-white br-2 one-row">立即参与</view>
					</view>
				</view>
			</view>
			<view class="w-100 flex-center flex-a-center flex-j-center flex-wrap h-50" v-if="data.good.length==0">
				<i class="iconfont icon-zanwu font-100 font-c-9"></i>
				<view class="w-100 text-center font-12 pt-20 pb-20 font-c-9">暂无数据 ~</view>
			</view>
		</view>
		<view class="winPopup flex-center flex-j-center" v-if="loader">
			<loader type="loader2"></loader>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	import loader from "@/components/loader/loader.vue";
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	export default {
		components: {
			uniLoadMore,
			loader,
		},
		data() {
			return {
				loader:false,
				data:{
					good:[],
					title:"拼土地专场",
				},
			}
		},
		onLoad() {
			var self=this;
			self.getList();
		},
		onNavigationBarButtonTap(e) {
			if(e.index == 0){
				this.app.showOpen('Shopping/bill');
			};
		},
		methods: {
			getList:function(){
				var self=this;
				self.loader=true;
				let url=config.api + "/get.shop.index";
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
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						self.loader=false;
					}
				});
			},
		}
	}
</script>

<style lang="less">
	@import url("@/common/newStyle/base.css");
	@import url("@/common/newStyle/iconfont.css");
	@import url("@/common/newStyle/common.css");
	.cont-list{
		width: 48%;
		.product-pic{
			background-color: #E6E7F3;width: 100%;height: 165px;overflow: hidden;
			.type{
				position: absolute;top: 0px;left: 0px;z-index: 1;
				background: linear-gradient(bottom,#FA6C00,#FFB500);
				border-radius: 4px 0px 4px 0px;padding: 8px 12px;
			}
			image{
				width: 130px;height: 130px;
			}
		}
		.price{
			max-width: calc(100% - 60px);
		}
		.btn{
			line-height: 18px;padding: 0px 5px;
			background: linear-gradient(bottom,#FA6C00,#FFB500);
		}
	}
</style>
