<template>
	<view class="box">
		<view class="page-close font-white" @click="app.goBack()"><i class="iconfont icon-ziyuan"></i></view>
		<view class="content pl-15 pr-15">
			<view class="page-title w-100 text-center font-18 pt-25 pb-25 bt_line">
				{{lang.appeal29}}
			</view>
			<view class="w-100">
				<view class="font-dark-main font-14 pt-30 pb-30" v-text="'花费'+money+'PT立马解冻'">{{lang.appeal30}}</view>
				<view class="w-100 select-pay">
					<view class="pay-item" :class="{'active':type==1}" @click="type=1">
						<text class="nowrap">钱包支付(可用：{{data.Available ? app._toFixed(data.Available,4)+'斤' : '--'}})</text>
						<text class="one-row">
							<i class="iconfont icon-iconfontcheck"></i>
						</text>
					</view>
					<view class="pay-item" :class="{'active':type==2}" @click="type=2">
						<text class="nowrap">备用金支付(可用：{{ (assets.userGiveAwayAmount?assets.userGiveAwayAmount:'--')+' (斤)' }})</text>
						<text class="one-row">
							<i class="iconfont icon-iconfontcheck"></i>
						</text>
					</view>
				</view>
				<view class="btn-cont flex-center flex-j-center pt-40">
					<button class="btn" :disabled="!status" @click="go_frozen()">{{lang.appeal31}}</button>
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
				status:true,
				type:1,
				money:"-",
				data:"",
			}
		},
		onLoad() {
			var self=this;
			self.getMoney();
			self.getData();
			self.set_assets();
		},
		computed:{
			...mapState(['userInfo','assets','lang']),
		},
		methods: {
			...mapMutations(["setUserInfo","set_assets"]),
			getMoney:function(){
				var self=this;
				let url=config.api + "/get.frozen.amount";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.money=res.data.data;
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
				let url=config.api + "/get.wallet.info";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						// config.api_status(res);
						if(res.data.status==1){
							self.data=res.data.data;
						}else{
							console.log(JSON.stringify(res));
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {}
				});
			},
			go_frozen:function(){
				var self=this;
				// let url=config.api + "/post.ctc.unfrozen";
				let url=config.api + "/user.frozen";
				self.status=false;
				uni.request({
					url: url,
					data: {
						type:self.type
					},
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log( {
						// 	type:self.type
						// } );
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.setUserInfo();
							self.set_assets();
							self.app._toast(res.data.message);
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
			}
		}
	}
</script>

<style>
	.select-pay{
		width: 100%;
		background: #F7F7F7;border-radius: 2px;
	}
	.select-pay .pay-item:last-child{
		border-bottom: 0px;
	}
	.pay-item{
		width: 100%;display: flex;align-items: center;justify-content: space-between;
		color: #333333;font-size: 12px;padding: 14px 15px;border-bottom: 1px solid #EBEBEB;
	}
	.pay-item .iconfont{
		border: 1px solid #FA6C00;color: #FA6C00;
		height: 14px;width: 14px;font-size: 12px;
		border-radius: 50%;display: inline-flex;align-items: center;justify-content: center;
		display: none;
	}
	.pay-item.active .iconfont{
		display: inline-flex;
	}
	.bd{
		border: 1px solid red;
	}
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
		/* background-color: #F2F2F2; */
		background-color: #FFFFFF;
		border-radius: 10px 10px 0px 0px;
		overflow-y: scroll;
		z-index: 1;
	}
	.page-title{color: #3C3C3C;position: relative;line-height: 18px;}
	/* button.btn{width: 200px;} */
	button.btn{width: 100%;}
	.details img{width: 100%;}
</style>
