<template>
	<view class="box">
		<view class="top-data font-20 font-white flex-center flex-j-center">
			<image class="mr-10" src="../../static/img/3a2ec6913d0d1c6b2b197127d7369c1.png"></image>{{lang.order75}}
		</view>
		<view class="pl-20 pr-20 pt-30 pb-30">
			<view class="pb-30">
				<button class="btn font-14" @click="app.closeMeOpen('trade/buy?price='+price)">{{lang.order76}}</button>
			</view>
			<view class="pb-30">
				<button class="btn font-14" @click="app.goBack()">{{lang.order77}}</button>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		data() {
			return {
				price:0,
			}
		},
		onLoad(e) {
			var self=this;
			self.price=e.price;
		},
		computed:{
			...mapState(['userInfo','qiniu','lang']),
		},
		methods: {
			
		}
	}
</script>

<style>
	page{background-color: #F7F7F7;}
	.top-data{width: 100%;height: 150px;background: linear-gradient(bottom,#FA6C00,#FFB500);}
	.top-data image{width: 21px;height: 22px;}
	button.btn{width: 100%;background: linear-gradient(bottom,#FA6C00,#FFB500);height: 44px;line-height: 44px;}
</style>
