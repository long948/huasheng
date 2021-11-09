<template>
	<view class="box pb-15 pr-15 pl-15">
		<view class="page-title flex-center pl-15 pr-15 font-18">
			<view @click="app.goBack()" class="flex-center font-18 font-white"><i class="iconfont icon-zuo font-20 mr-5"></i>{{lang.td1}}</view>
		</view>
		<view class="content pl-20 pr-20 mt-10 pb-20">
			<view class="head-portrait flex-center flex-j-center">
				<view class="head">
					<image :src="data.hd"></image>
				</view>
			</view>
			<view class="text-center font-dark-main font-18 pt-5 pb-10" v-text="details.nickname">树</view>
			<view class="flex-center font-12 nowrap font-grey lh-30">{{lang.td8}}<view class="font-dark-main font-12 ml-5"><text class="font-18">{{details.release_amount}} / </text>{{details.total_amount}} PT</view></view>
			<view class="progress-cont mb-30">
				<view class="value" :style="{'width': app._accMul( app._accDiv(details.release_amount,details.total_amount),100)+'%'}"></view>
			</view>
			<view class="pb-15 flex-row flex-j-between lh-20">
				<text class="one-row font-14 font-grey mr-20">{{lang.td2}}：</text>
				<view class="newlines font-14 font-grey" v-text="details.create_time">2020-04-20</view>
			</view>
			<view class="pb-15 flex-row flex-j-between lh-20">
				<text class="one-row font-14 font-grey mr-20">{{lang.td3}}：</text>
				<view class="newlines font-14 font-grey" v-text="details.total_freed+'天'">30天</view>
			</view>
			<view class="pb-15 flex-row flex-j-between lh-20">
				<text class="one-row font-14 font-grey mr-20">{{lang.td4}}：</text>
				<view class="newlines font-14 font-grey" v-text="Number(details.total_freed)-Number(details.freed)+lang.td7">15天</view>
			</view>
			<view class="pb-15 flex-row flex-j-between lh-20">
				<text class="one-row font-14 font-grey mr-20">{{lang.td5}}：</text>
				<view class="newlines font-14 font-grey" v-text="details.yield+' PT'">5.33 PT</view>
			</view>
			<view class="pb-15 flex-row flex-j-between lh-20">
				<text class="one-row font-14 font-grey mr-20">{{lang.td6}}：</text>
				<view class="newlines font-14 font-grey" v-text="details.computing_power+' T'">300 T</view>
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
				data:"",
				details:{
					nickname:"--",
					release_amount:"--",
					total_amount:"--",
					create_time:"--",
					total_freed:"--",
					total_freed:0,
					freed:0,
					yield:"--",
					computing_power:"--",
				},
				buy_status:true,
			}
		},
		onLoad(e) {
			var self=this;
			self.data=JSON.parse(e.data);
			self.getDetails();
		},
		computed:{
			...mapState(['userInfo','assets','lang']),
		},
		methods: {
			getDetails:function(){
				var self=this;
				let url=config.api + "/sapling-user-details";
				uni.request({
					url: url,
					data: {
						sapling_id:self.data.user_sapling_id
					},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.details=res.data.data;
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
	.box {
		height: 100vh;
		background: url(../../static/img/1e0cc92565daf015e510d11505f9289.png) no-repeat;
		background-size: 100% 100%;
		overflow-y: auto;
		position: relative;
		padding-bottom: 60px;
		padding-top: 150px;
	}
	.box::after {
		position: absolute;
		top: 0px;
		left: 0px;
		right: 0px;
		bottom: 0px;
		content: "";
		background-color: rgba(4, 4, 4, 0.4);
	}
	.box .page-title{
		width: 100%;
		position: absolute;
		left: 0px;
		top: 45px;
		z-index: 50;
	}
	.content{
		width: 100%;
		position: relative;
		z-index: 1;
		border-radius: 10px;
		background-color: #FFFFFF;
	}
	.head-portrait{
		width: 100%;
		height: 60px;
	}
	.head-portrait .head{
		height: 120px;
		width: 120px;
		position: absolute;
		top: -60px;
		left: calc(50% - 60px);
		border: 2px solid #FFFFFF;
		border-radius: 50%;
		overflow: hidden;
	}
	.head-portrait .head image{
		width: 100%;
		height: 100%;
		display: block;
	}
	.progress-cont{
		width: 100%;
		height: 16px;
		border-radius: 8px;
		background-color: #62A9FF;
		overflow: hidden;
	}
	.progress-cont .value{
		height: 100%;
		display: inline-block;
		border-radius: 8px;
		background: linear-gradient(#BEE860,#63AF19,#519B08);
	}
</style>
