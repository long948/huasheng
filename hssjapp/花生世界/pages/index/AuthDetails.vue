<template>
	<view class="box">
		<view class="page-close font-white" @click="app.goBack()"><i class="iconfont icon-ziyuan"></i></view>
		<view class="content pl-15 pr-15">
			<view class="page-title w-100 text-center font-18 pt-25 pb-25 bt_line">
				{{lang.atrs1}}
			</view>
			<view class="w-100" v-if="userInfo.auth_status==3">
				<view class="font-main font-14 pt-30 pb-30">{{lang.atrs2}}</view>
				<block v-if="data.is_giveAway==1">
					<view class="font-grey font-14 newlines">
						{{lang.atrs3}}
					</view>
					<view class="text-center pt-35">
						<image style="width: 116px;" mode="widthFix" src="../../static/img/57a4531f7eff39e93c7c2184c1f18a2.png"></image>
					</view>
					<view class="btn-cont flex-center flex-j-center pt-40">
						<button class="btn" @click="receive()">{{lang.atrs4}}</button>
					</view>
				</block>
			</view>
			<view class="w-100" v-if="userInfo.auth_status==2">
				<view class="font-red font-14 pt-30">{{lang.atrs5}}</view>
				<view class="font-grey font-14 pt-10 pb-30">{{lang.atrs6}}{{userInfo.auth_msg}}</view>
				<view class="text-center pt-35">
					<image style="width: 116px;" mode="widthFix" src="../../static/img/3b7ea07ab4d781263284d32c5b564fb.png"></image>
				</view>
				<view class="btn-cont flex-center flex-j-center pt-40">
					<button class="btn" :disabled="!status" @click="app.showOpen('auth/auth')">{{lang.atrs7}}</button>
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
				data:"",
				status:true,
			}
		},
		onLoad() {
			var self=this;
			// console.log(self.userInfo);
			self.getData();
		},
		computed:{
			...mapState(['userInfo','assets','lang']),
		},
		methods: {
			receive:function(){
				var self=this;
				self.status=false;
				let url=config.api + "/user-auth-giveAway";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.app._toastIcon(res.data.message);
							self.status=false;
							setTimeout(function(){
								self.app.goBack();
							},1000);
						}else{
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						self.status=true;
					}
				});
			},
			getData:function(){
				var self=this;
				let url=config.api + "/user-auth-is-giveAway";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						console.log(JSON.stringify(res));
						// config.api_status(res);
						if(res.data.status==1){
							self.data=res.data.data;
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
	page{
		height: 100vh;
		background: url(../../static/img/1e0cc92565daf015e510d11505f9289.png) no-repeat;
		background-size: 100% 100%;
		overflow-y: auto;
		position: relative;
	}
	page::after{
		position: absolute;
		top: 0px;
		left: 0px;
		right: 0px;
		bottom: 0px;
		content: "";
		background-color: rgba(4,4,4,0.4);
	}
	.page-close{position: fixed;top: 70px;right: 0px;padding-right: 20px;z-index: 10;}
	.page-close .iconfont{font-size: 25px;}
	.content{
		width: 100%;
		height: calc(100vh - 100px);
		position: fixed;
		left: 0px;
		bottom: 0px;
		background-color: #F2F2F2;
		border-radius: 10px 10px 0px 0px;
		overflow-y: scroll;
		z-index: 1;
	}
	.page-title{color: #3C3C3C;position: relative;line-height: 18px;}
	button.btn{width: 200px;}
	.details img{width: 100%;}
</style>
