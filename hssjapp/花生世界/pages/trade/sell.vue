<template>
	<view class="box">
		<view class="top-data">
			<view class="nowrap flex-center flex-j-between">
				<view class="flex-center">
					<text class="font-20 font-white nowrap" v-text="'¥'+(data.Price)">¥7.02</text>
					<text class="font-12 font-white op-7 ml-5 one-row">/USDT</text>
				</view>
				<view class="flex-center">
					<image src="../../static/img/53c183e8d60199ef97be47fdd75a547.png" v-if="data.IsAlipay"></image>
					<image src="../../static/img/bc613dffddbfeeb9fde4ebacc2ee407.png" v-if="data.IsAddress"></image>
				</view>
			</view>
		</view>
		<view class="content pl-20 pr-20">
			<view class="w-100 bc-white br-4 form-cont">
				<view class="w-100 flex-center flex-j-between">
					<image src="../../static/img/7fdaaa5c17a2ac8b7ae2d93a0d54d75.png"></image>
					<view class="Input-cont">
						<view class="contInput flex-center pt-5 bt_line">
							<input type="text" placeholder="请输入想要出售的总数量" class="font-14" v-model="num" @input="change1" />
							<text class="one-row font-12 op-7 font-grey">PT</text>
						</view>
						<view class="contInput flex-center pt-5 bt_line">
							<input type="text" placeholder="请输入想要出售的总金额" class="font-14" v-model="num1" @input="change2" />
							<text class="one-row font-12 op-7 font-grey">USDT</text>
						</view>
					</view>
				</view>
				<view class="font-14 font-grey op-7 pt-15">选择收款方式（不可多选）</view>
				<view class="cheques flex-center pt-25 pb-25">
					<view class="w-33 flex-center font-14 font-grey" :class="{'active':pay=='alipay'}" @click="pay='alipay'">
						<text class="icon iconfont icon-iconfontcheck"></text>支付宝
					</view>
					<view class="w-33 flex-center font-14 font-grey" :class="{'active':pay=='isusdt'}" @click="pay='isusdt'">
						<text class="icon iconfont icon-iconfontcheck"></text>USDT
					</view>
				</view>
				<view class="">
					<view class="font-12 font-red lh-20">平台限额{{app._accMul(data.MinMoney,7)}}-{{app._accMul(data.MaxMoney,7)}} CNY</view>
				</view>
				<view class="pt-25">
					<button class="btn" :disabled="!sell_status" @click="sell()">出售</button>
				</view>
			</view>
			<view class="w-100 bc-white br-4 user-Info pt-20 pl-20 pr-20 pb-10" v-if="userInfo">
				<view class="font-16 pb-15" style="color: #666666;">买家信息</view>
				<view class="flex-row flex-j-between lh-20 pb-15">
					<text class="title font-14 one-row">买家昵称</text>
					<view class="value font-14 newlines" v-text="userInfo.Name">影与光的浪漫</view>
				</view>
				<view class="flex-row flex-j-between lh-20 pb-15">
					<text class="title font-14 one-row">买家总交易单数</text>
					<view class="value font-14 newlines" v-text="userInfo.ServiceMember">1776</view>
				</view>
				<view class="flex-row flex-j-between lh-20 pb-15">
					<text class="title font-14 one-row">买家成交率</text>
					<view class="value font-14 newlines" v-text="userInfo.SuccessRation">97.77%</view>
				</view>
				<view class="flex-row flex-j-between lh-20 pb-15">
					<text class="title font-14 one-row">认证情况</text>
					<view class="value font-14 newlines">已实名认证</view>
				</view>
				<view class="flex-row flex-j-between lh-20 pb-15">
					<text class="title font-14 one-row">电话号码</text>
					<view class="value font-14 font-dark-green newlines" v-text="userInfo.Phone">13456789025</view>
				</view>
				<view class="flex-row flex-j-between lh-20 pb-15">
					<text class="title font-14 one-row">平均放款时间</text>
					<view class="value font-14 newlines" v-text="userInfo.AvgPayTime+'s'">00.00.00</view>
				</view>
				<view class="flex-row flex-j-between lh-20 pb-15">
					<text class="title font-14 one-row">平均放币时间</text>
					<view class="value font-14 newlines" v-text="userInfo.AvgConfrim+'s'">00.00.00</view>
				</view>
				<view class="flex-row flex-j-between lh-20 pb-15">
					<text class="title font-14 one-row">被投诉次数</text>
					<view class="value font-14 newlines" v-text="userInfo.AppealTime">0</view>
				</view>
				<view class="font-14 tips pt-10">
					请及时付款，请勿备注，备注不放币，黑钱没收。如微信付款请先扫码加好友再支付。
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	export default {
		data() {
			return {
				id:"",
				data:"",
				userInfo:"",
				price:0,
				pay:"",
				num:"",
				num1:"",
				sell_status:true,
			}
		},
		onLoad(e) {
			var self=this;
			self.id=JSON.parse(e.data).Id;
			self.price=e.price;
			self.data=JSON.parse(e.data);
			self.getUserInfo();
		},
		methods: {
			sell:function(){
				var self=this;
				if(self.pay==''){
					return self.app._toast("请选择收款方式");
				};
				if(isNaN(self.num)||self.num.length==0){
					return self.app._toast("请输入数量");
				};
				let send={
					Id:self.id,
					Number:self.num,
					IsAddress:self.pay=='isusdt'?true:false,
					IsWechat:false,
					IsAlipay:self.pay=='alipay'?true:false,
				};
				self.sell_status=false;
				let url=config.api + "/ctc-sell";
				uni.request({
					url: url,
					data: send,
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.app._toastIcon(res.data.message);
							setTimeout(function(){
								self.app.closeMeOpen('trade/order');
							},2000);
						}else{
							console.log(JSON.stringify(res));
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						setTimeout(function(){
							self.sell_status=true;
						},500);
					}
				});
			},
			change1:function(e){
				var self=this;
				let v=e.detail.value;
				if(isNaN(v)){
					setTimeout(function(){
						self.num="";
					},300);
					return self.app._toast("请输入数字");
				};
				self.num1=self.app._accMul(self.num,7);
			},
			change2:function(e){
				var self=this;
				let v=e.detail.value;
				if(isNaN(v)){
					setTimeout(function(){
						self.num1="";
					},300);
					return self.app._toast("请输入数字");
				};
				self.num=self.app._accDiv(self.num1,7);
			},
			getUserInfo:function(){
				var self=this;
				let send={
					Id:self.id,
				};
				let url=config.api + "/ctc-member-info";
				uni.request({
					url: url,
					data: send,
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.userInfo=res.data.data;
						}else{
							self.app._toast(res.data.message);
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
	page{background-color: #F7F7F7;}
	.top-data{width: 100%;background: url(../../static/img/2fcc5030bed4d81293053eada380843.png) no-repeat;background-size: 100% 100%;padding: 23px 25px;padding-bottom: 80px;}
	.top-data image{width: 19px;height: 19px;margin-left: 10px;}
	.content{width: 100%;position: relative;top: -50px;}
	.content .form-cont{width: 100%;box-shadow: 0px 2px 4px 2px #E8EAF0;padding: 15px 20px;}
	.content .form-cont image{width: 20px;height: 20px;display: block;}
	.content .form-cont .Input-cont{width: calc(100% - 30px);}
	.content .form-cont .Input-cont .contInput{height: 45px;width: 100%;}
	.content .form-cont .Input-cont .contInput input{width: 100%;height: 100%;color: #333333;}
	.content .form-cont .cheques{width: 100%;}
	.content .form-cont .cheques .icon{width: 15px;height: 15px;margin-right: 5px;border-radius: 50%;display: inline-flex;align-items: center;align-content: center;justify-content: center;border: 1px solid #979797;color: #FFFFFF;font-size: 12px;}
	.content .form-cont .cheques .active .icon{background: linear-gradient(#BEE860,#63AF19,#519B08);border: 0px;}
	.content .form-cont button.btn{background: linear-gradient(#BEE860,#63AF19,#519B08);font-size: 14px;height: 44px;line-height: 44px;}
	.content .form-cont button.btn[disabled]{background: #C0C0C0 !important;}
	.user-Info{width: 100%;box-shadow: 0px 2px 4px 2px #E8EAF0;padding: 15px 20px;margin-top: 20px;}
	.user-Info .title{padding-right: 10px;}
	.user-Info .title,
	.user-Info .tips{color: #999999;}
	.user-Info .value{color: #666666;}


</style>
