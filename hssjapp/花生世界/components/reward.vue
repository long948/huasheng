<template>
	<view class="box flex-center flex-j-center flex-end pl-15 pr-15 flex-wrap" v-if="show">
		<view class="w-100 text-right"><view class="-close font-white" @click="closepage()"><i class="iconfont icon-ziyuan"></i></view></view>
		<view class="content pl-15 pr-15">
			<view class="page-title w-100 text-center font-18 pt-25 pb-25 bt_line">
				{{userInfo.auth_status==3?'恭喜获得':'通知'}}
			</view>
			<scroll-view scroll-y="">
				<view class="w-100" v-if="userInfo.auth_status==3">
					<view class="font-main font-14 pt-30 pb-30">恭喜您实名认证成功！</view>
					<block v-if="RewardInfo.is_giveAway==1">
						<view class="font-grey font-14 newlines">
							恭喜您通过实名认证，我们认可了你对花生世界的关注特赠送您“一亩体验小花田”，请再接再厉！
						</view>
						<view class="text-center pt-35">
							<image style="width: 116px;" mode="widthFix" src="@/static/img/5fb98cb177cf6887eba40d23a8af392.png"></image>
						</view>
						<view class="btn-cont flex-center flex-j-center pt-15">
							<button class="btn font-white flex-center flex-j-center" @click="receive()" v-text="'立即领取'"><text>立即领取</text></button>
						</view>
					</block>
				</view>
			</scroll-view>
			<view class="w-100" v-if="userInfo.auth_status==2">
				<view class="font-red font-14 pt-30">实名认证失败！</view>
				<view class="font-grey font-14 pt-10 pb-30">失败原因：{{userInfo.auth_msg}}</view>
				<view class="text-center pt-35">
					<image style="width: 116px;" mode="widthFix" src="@/static/img/3b7ea07ab4d781263284d32c5b564fb.png"></image>
				</view>
				<view class="btn-cont flex-center flex-j-center pt-15">
					<button class="btn" :disabled="!status" @click="closepage();app.showOpen('auth/auth')">重新申请</button>
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
		name:"reward",
		props:{
			show:{
				type:Boolean
			}
		},
		data() {
			return {
				data:"",
				status:true,
			}
		},
		onLoad() {
			var self=this;
			// console.log(self.userInfo);
			self.data=self.RewardInfo;
			console.log(self.data)
		},
		computed:{
			...mapState(['userInfo','assets','RewardInfo']),
		},
		methods: {
			...mapMutations(["setUserInfo","getReward"]),
			closepage:function(){
				this.$emit('update:rewardSW', false)
			},
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
							let val=uni.getStorageSync("RewardInfo");
							val.is_giveAway=0;
							uni.setStorageSync("RewardInfo",val);
							self.getReward();
							self.app._toastIcon(res.data.message);
							self.status=false;
							self.closepage();
							
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
						// console.log(JSON.stringify(res));
						config.api_status(res);
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
		width: 100%;
		height: 100vh;
		background: url(@/static/img/1e0cc92565daf015e510d11505f9289.png) no-repeat;
		background-size: 100% 100%;
		overflow-y: auto;
		z-index: 600;
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
	.box{
		height: 100vh;
		width: 100%;
		display: flex;
		align-items: center !important;
		align-content: center !important;
		justify-content: center !important;
		padding-bottom: 0px;
		padding-top: 40px;
		margin: 0px !important;
	}
	/* .page-close{position: fixed;top: 70px;right: 0px;padding-right: 20px;z-index: 10;} */
	.-close{position: relative;top:0px;bottom: 0px;right: 0px;}
	.-close .iconfont{font-size: 25px;}
	.content{
		width: 100%;
		/* min-height: calc(100vh - 200px); */
		min-height: 10vh;
		max-height: calc(100vh - 60px);
		background-color: #F2F2F2;
		border-radius: 10px 10px 10px 10px;
		overflow-y: scroll;
		z-index: 1;
		margin: 0px;
		padding-bottom: 20px;
		top: 0px !important;
	}
	.content scroll-view{width: 100%;height: 100%;}
	.page-title{color: #3C3C3C;position: relative;line-height: 18px;}
	.content button.btn{width: 200px;color: #FFFFFF;}
	.details img{width: 100%;}
</style>
