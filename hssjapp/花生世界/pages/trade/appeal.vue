<template>
	<view class="box" @click="choiceSW=false" v-if="details">
		<view class="mt-15 bc-white pt-25 pb-25 pl-25 pr-25 shadow">
			<view class="flex-center nowrap font-cl-6 pb-15">
				<text class="one-row font-cl-9 font-14 mr-20">{{lang.appeal1}}</text>PT
			</view>
			<view class="flex-center flex-j-between pb-15">
				<view class="flex-center nowrap font-cl-6">
					<text class="one-row font-cl-9 font-14 mr-20">{{lang.appeal2}}</text>{{app._toFixed(details.Number,4)}} PT
				</view>
				<text class="one-row font-cl-9 font-14">{{lang.appeal3}}</text>
			</view>
			<view class="flex-center flex-j-between">
				<view class="flex-center nowrap font-cl-6">
					<text class="one-row font-cl-9 font-14 mr-20">{{lang.appeal4}}</text>{{app._accMul(details.Price,7)}} CNY
				</view>
				<text class="one-row font-dark-main font-21">¥{{ app._accMul(details.SumPrice,7) }}</text>
			</view>
		</view>
		<view class="mt-15 bc-white pt-20 pb-20 pl-25 pr-25 shadow mb-15">
			<view class="font-16 font-cl-6 pb-15">{{lang.appeal5}}</view>
			<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
				<text class="one-row font-cl-9 font-14 mr-20">{{lang.appeal6}}</text>
				<view class="newlines font-cl-9 font-14" v-text="details.BuyMember">影与光的浪漫</view>
			</view>
			<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
				<text class="one-row font-cl-9 font-14 mr-20">{{lang.appeal7}}</text>
				<view class="newlines font-cl-9 font-14">{{lang.appeal8}}</view>
			</view>
			<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
				<text class="one-row font-cl-9 font-14 mr-20">{{lang.appeal9}}</text>
				<view class="newlines font-cl-9 font-14" v-text="details.SellMember">云和山的彼端</view>
			</view>
			<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
				<text class="one-row font-cl-9 font-14 mr-20">{{lang.appeal10}}</text>
				<view class="newlines font-cl-9 font-14">{{details.OrderSn}}<image @click="app._copy(details.OrderSn)" class="ml-10" style="width: 16px;height: 18px;display: inline-block;" src="../../static/img/8cb6845e062449dcef4ce3bd75774b3.png"></image> </view>
			</view>
			<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
				<text class="one-row font-cl-9 font-14 mr-20">{{lang.appeal11}}</text>
				<view class="newlines font-cl-9 font-14" v-text="app._formatDate(details.AddTime)">2019-10-26 10:25:26</view>
			</view>
			<view class="flex-row flex-j-between lh-20 font-cl-6">
				<text class="one-row font-cl-9 font-14 mr-20" v-if="details.PayTime!=0">{{lang.appeal12}}</text>
				<view class="newlines font-cl-9 font-14" v-text="app._formatDate(details.PayTime)">2019-10-26 10:25:26</view>
			</view>
		</view>
		<view class="w-100 bc-white flex-center flex-j-between pt-15 pb-15 pl-15 pr-15 mb-15">
			<text class="font-16 font-cl-6 one-row">{{lang.appeal13}}</text>
			<text class="font-14 font-cl-6 nowrap" v-text="details.BuyMember">影与光的浪漫</text>
		</view>
		<view class="choice w-100 bc-white pt-15 pb-15 pl-15 pr-15 mb-15" @click.stop="">
			<view class="w-100 flex-center flex-j-between" @click="choiceSW=true">
				<text class="font-16 font-cl-6 one-row" :class="{'op-5':nav==-1}" v-text="nav==-1?lang.appeal14:reason[nav].Content">申诉原因</text>
				<i class="iconfont icon-shang-copy font-grey"></i>
			</view>
			<view class="reason-list bc-white pl-15 pr-15" v-if="choiceSW">
				<view class="w-100 nowrap pt-15 pb-15 font-12 font-cl-3 bt_line" v-for="(item,index) in reason " @click="nav=index;choiceSW=false;" :key="index" v-text="item.Content">原因1 </view>
			</view>
		</view>
		<view class="mt-15 bc-white pt-20 pb-20 pl-25 pr-25">
			<view class="w-100">
				<view class="font-cl-6 pt-15 pb-15">{{lang.appeal15}}</view>
				<textarea :placeholder="lang.appeal16" @input="change"></textarea>
			</view>
			<view class="btn-cont pt-20 pb-20">
				<button class="btn" :disabled="!appeal_status" @click="go_complaint()">{{lang.appeal17}}</button>
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
				id:"",
				details:"",
				reason:[],
				value:"",
				choiceSW:false,
				nav:"-1",
				appeal_status:true,
			}
		},
		onLoad(e) {
			var self=this;
			self.id=JSON.parse(e.data).Id;
			self.getReason();
			self.getdetails();
		},
		computed:{
			...mapState(['userInfo','qiniu','RewardInfo','lang']),
		},
		methods: {
			go_complaint:function(){
				var self=this;
				if(self.nav=="-1"){
					return self.app._toast(self.lang.appeal18);
				};
				if(self.value.trim().length==0){
					return self.app._toast(self.lang.appeal19);
				};
				let send={
					ReasonId:self.reason[self.nav].Id,
					Content:self.value,
					Id:self.id,
					Imgs:"",
				};
				self.appeal_status=false;
				let url=config.api + "/ctc-appeal";
				uni.request({
					url: url,
					data: send,
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.app._toastIcon(self.lang.appeal20);
							var page=self.app._prePage();
							page.$vm.getdetails();
							setTimeout(function(){
								self.app.goBack();
							},2000);
						}else{
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						self.appeal_status=true;
					}
				});
			},
			change:function(e){
				var self=this;
				self.value=e.detail.value;
			},
			getReason:function(){
				var self=this;
				let url=config.api + "/ctc-appeal-reason";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.reason=res.data.data;
						}else{
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {}
				});
			},
			getdetails:function(){
				var self=this;
				let send={
					Id:self.id,
				};
				let url=config.api + "/ctc-trade-detail";
				uni.request({
					url: url,
					data: send,
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.details=res.data.data;
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
	.shadow{box-shadow: 0px 0px 4px #E8EAF0;}
	.add-cont{width: 100%;}
	.pic{width: 30%;height: 100px;border-radius: 2px;background-color: #D8D8D8;margin-top: 15px;display: flex;align-items: center;justify-content: center;}
	.pic .iconfont{font-size: 35px;color: #FFFFFF;}
	.pic image{width: 100%;height: 100%;}
	.btn-cont button.btn{background: linear-gradient(bottom,#FA6C00,#FFB500);height: 44px;line-height: 44px;font-size: 14px;}
	textarea{width: 100%;height: 200px;border-radius: 2px;background-color: #D8D8D8;padding: 10px;font-size: 14px;color: #000000;}
	.choice{position: relative;}
	.reason-list{width: 90%;max-height: 200px;overflow-y: scroll;position: absolute;top: 100%;left: 5%;z-index: 10;border-radius: 3px;box-shadow: 0px 2px 4px 2px #E8EAF0;}

</style>
