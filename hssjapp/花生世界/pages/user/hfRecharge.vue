<template>
	<view class="box pt-15 pb-15 pl-15 pr-15">
		<view class="content bc-white br-4 pt-30 pb-30 pl-15 pr-15">
			
			<view class="flex-center flex-j-between cont-list">
				<view class="title one-row text-right">姓名</view>
				<view class="value bt_line">
					<input type="text" v-model="name" />
				</view>
			</view>
			<view class="flex-center flex-j-between cont-list">
				<view class="title one-row text-right">手机号</view>
				<view class="value bt_line">
					<input type="text" v-model="mobile" />
				</view>
			</view>
			<view class="flex-row flex-j-between cont-list">
				<view class="title one-row text-right">选择金额</view>
				<view class="value newlines">
					<view class="nav nowrap" :class="{'active':index == moneyindex}" v-for="(item,index) in config.phone" :key="index" v-text="item+'元'" @click="moneyindex=index">100元</view>>
				</view>
			</view>
			<view class="flex-center flex-j-between cont-list mt-5">
				<view class="title one-row text-right">交易密码</view>
				<view class="value bt_line">
					<input type="password" v-model="pass" />
				</view>
			</view>
			<view class="w-100 flex-center flex-j-between mt-20 pl-15 font-10" style="color: #666666;">
				<text class="font-main" @click="app.showOpen('login/forgetPay')">忘记密码</text>
				<view class="text-right font-10">预计扣除PT数量：<text class="font-main">{{ app._toFixed(app._accDiv(config.phone[moneyindex],config.price),2) }}</text></view>
			</view>
		</view>
		<view class="remarks pt-15 pl-15 pr-15">
			<view class="lh-25 newlines" v-text="config.phone_remarks"></view>
		</view>
		<view class="w-100 pt-20 pb-20 text-center">
			<button class="btn mb-10" @click="Recharge">立即缴费</button>
			<text class="font-12" style="color: #666666;" @click="app.showOpen('user/record')">缴费记录</text>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js"
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		data() {
			return {
				config:{phone:[],oil:['---'],phone_remarks:"--",price:"--"},
				moneyindex:0,
				address:"",
				id:"",
				name:"",
				mobile:"",
				pass:"",
				card_id:"",
			}
		},
		onLoad() {
			var self=this;
			// uni.setNavigationBarTitle({
			// 	title:self.lang.trade35
			// });
			self.getConfig();
		},
		computed:{
			...mapState(['qiniu','userInfo','lang']),
		},
		methods: {
			...mapMutations(["setUserInfo","set_assets"]),
			Recharge:function(){
				var self=this;
				if(self.name.length==0){
					return self.app._toast("请输入姓名");
				};
				// if(self.card_id.length==0){
				// 	return self.app._toast("请输入身份证");
				// };
				if(self.mobile.length==0){
					return self.app._toast("请输入手机号");
				};
				// if(self.address.length==0){
				// 	return self.app._toast("请输入地址");
				// };
				// if(self.id.length==0){
				// 	return self.app._toast("请输入油卡卡号");
				// };
				if(self.pass.length==0){
					return self.app._toast("请输入密码");
				};
				let send={
					type:1,
					child_type:self.ykindex,
					nickname:self.name,
					phone:self.mobile,
					address:self.address,
					card_num:self.mobile,
					amount:self.config.phone[self.moneyindex],
					pay_password:self.pass,
					card_id:self.card_id
				};
				uni.showNavigationBarLoading();
				let url=config.api + "/user-submit-ecology-order";
				uni.request({
					url: url,
					data: send,
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						uni.hideNavigationBarLoading();
						config.api_status(res);
						if(res.data.status==1){
							self.app._toast(res.data.message);
							self.set_assets();
							setTimeout(function(){
								self.app.goBack();
							},1000);
						}else{
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(res);
					},
					complete: (res) => {}
				});
			},
			getConfig:function(){
				var self=this;
				uni.showNavigationBarLoading();
				let url=config.api + "/user-submit-ecology-info";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						uni.hideNavigationBarLoading();
						config.api_status(res);
						if(res.data.status==1){
							self.config=res.data.data;
						}else{
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(res);
					},
					complete: (res) => {}
				});
			},
		}
	}
</script>

<style>
	page{background-color: #F7F7F7;}
	.content{}
	.cont-list{margin-top: 30px;line-height: 32px;}
	.title{width: 70px;color: #999999;font-size: 14px;}
	.value{width: calc(100% - 100px);}
	.value input{width: 100%;height: 32px;color: #3C3C3C;font-size: 14px;}
	.nav{color: #3C3C3C;font-size: 12px;height: 32px;line-height: 32px;border: 1rpx solid #BFBFBF;
		border-radius: 2px;margin-right: 12px;display: inline-block;padding: 0px 15px;	}
	.nav.active{background: linear-gradient(bottom,#FA6C00,#FFB500);color: #FFFFFF;border: 0px;}
	.remarks{width: 100%;color: #666666;font-size: 12px;}
	button.btn{background: linear-gradient(bottom,#FA6C00,#FFB500);line-height: 44px;height: 44px;}
</style>
