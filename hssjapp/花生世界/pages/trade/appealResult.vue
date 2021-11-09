<template>
	<view class="box pl-50 pr-50">
		<view class="stauts-cont pt-50">
			<view class="num-cont flex-center flex-j-between">
				<view class="flex-center flex-j-center num font-white font-20" :class="{'active':details.Appeal.Result==0}">1</view>
				<view class="flex-center flex-j-center num font-white font-20" :class="{'active':details.Appeal.Result!=0}">2</view>
			</view>
			<view class="flex-center flex-j-between pt-10">
				<view class="font-cl-6">{{lang.appeal21}}</view>
				<view class="font-cl-6">{{lang.appeal22}}</view>
			</view>
			<view class="w-100 pt-40 newlines font-cl-6 font-14 lh-20" v-if="details.Appeal.Result==0">
				{{lang.appeal23}}
			</view>
			<block>
				<view class="w-100 pt-40 newlines font-cl-6 font-14 lh-20" v-if="details.Appeal.Result==1">
					<view class="font-14 font-dark-main pb-15">{{lang.appeal24}}</view>
					{{lang.appeal25}}
				</view>
				<view class="w-100 pt-40 newlines font-cl-6 font-14 lh-20" v-if="details.Appeal.Result==2">
					<view class="font-14 font-red pb-15">{{lang.appeal26}}</view>
					{{lang.appeal27}}
				</view>
			</block>
			<view class="pt-50">
				<button class="btn" @click="app.showOpenTab('trade/trade')">{{lang.appeal28}}</button>
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
				id:"",
				details:"",
			}
		},
		onLoad(e) {
			var self=this;
			self.id=JSON.parse(e.data).Id;
			self.details=JSON.parse(e.data);
			console.log(self.details)
		},
		computed:{
			...mapState(['userInfo','qiniu','RewardInfo','lang']),
		},
		methods: {
			
		}
	}
</script>

<style>
	page{background-color: #FFFFFF;}
	.box{width: 100%;border-top: 10px solid #F7F7F7;}
	.stauts-cont{width: 100%;}
	.stauts-cont .num-cont{width: 100%;position: relative;}
	.stauts-cont .num-cont .num{width: 56px;height: 56px;border-radius: 50%;background-color: #D8D8D8;}
	.stauts-cont .num-cont .num.active{background: linear-gradient(bottom,#FA6C00,#FFB500);}
	.stauts-cont .num-cont::after{position: absolute;left: calc(50% - 47px);width: 94px;height: 1px;content: "";background-color: #979797;opacity: 0.5;}
	button.btn{background: linear-gradient(bottom,#FA6C00,#FFB500);height: 44px;line-height: 44px;color: #FFFFFF;font-size: 14px;}
</style>
