<template>
	<view class="tabBar-content flex-center flex-j-between">
		<view class="tabBar-cont flex-center flex-j-center" :class="{'active':name=='index'}" @click="navPage('../../pages/index/index')">
			<image mode="widthFix" src="@/static/img/index-active.png" v-if="name=='index'"></image>
			<image mode="widthFix" src="@/static/img/index.png" v-else></image>
			首页
		</view>
		<view class="tabBar-cont flex-center flex-j-center" :class="{'active':name=='store'}" @click="navPage('../../pages/store/store')">
			<image mode="widthFix" src="@/static/img/store-active.png" v-if="name=='store'"></image>
			<image mode="widthFix" src="@/static/img/store.png" v-else></image>
			商店
		</view>
		<view class="tabBar-cont flex-center flex-j-center" :class="{'active':name=='trade'}" @click="navPage('../../pages/trade/trade')">
			<image mode="widthFix" src="@/static/img/trade-active.png" v-if="name=='trade'"></image>
			<image mode="widthFix" src="@/static/img/trade.png" v-else></image>
			交易
		</view>
		<view class="tabBar-cont flex-center flex-j-center" :class="{'active':name=='user'}" @click="navPage('../../pages/user/user')">
			<image mode="widthFix" src="@/static/img/user-active.png" v-if="name=='user'"></image>
			<image mode="widthFix" src="@/static/img/user.png" v-else></image>
			我的
		</view>
	</view>
</template>

<script>
	export default {
		name:"tabBar",
		props:{
			name:{
				type: String,
				default: 'index'
			},
		},
		data() {
			return {
				
			};
		},
		methods:{
			navPage:function(url){
				uni.reLaunch({
				    url: url
				});
			}
		}
	}
</script>

<style>
	.tabBar-content{
		width: 100%;
		height: 60px;
		background-color: #66B34B;
		position: fixed;
		bottom: 0px;
		left: 0px;
		z-index: 100;
	}
	.tabBar-cont navigator{
		width: 100%;
		height: 100%;
		display: inline-flex;
		align-items: center;
		align-content: center;
		justify-content: center;
	}
	.tabBar-content::after{
		position: absolute;
		top: 0px;
		left: 0px;
		width: 100%;
		height: 14px;
		content: "";
		background: url(../../static/img/a7e3c25644b74d94e7a582908fa185e.png) no-repeat;
		background-size: 100% 100%;
	}
	.tabBar-content::before{
		position: absolute;
		bottom: 0px;
		left: 0px;
		width: 100%;
		height: 14px;
		content: "";
		background: url(../../static/img/a7e3c25644b74d94e7a582908fa185e.png) no-repeat;
		background-size: 100% 100%;
		transform: rotate(180deg);
	}
	.tabBar-content .tabBar-cont{
		width: 25%;
		height: 100%;
		color: #FFFFFF;
		font-size: 14px;
		color: rgba(255,255,255,0.7);
	}
	.tabBar-content .tabBar-cont image{
		width: 18px;
		height: 18px;
		margin-right: 5px;
		display: block;
	}
	.tabBar-content .tabBar-cont.active{
		background-color: #448730;
	}
</style>
