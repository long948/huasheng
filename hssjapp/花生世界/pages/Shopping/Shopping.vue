<template>
	<view class="box">
		<view class="w-100" v-if="data">
			<!-- <view class="w-100 pt-26 pb-26 pl-16 pr-16 flex-center flex-j-between font-white order-time" v-if="data.status==1">
				<view class="flex-center nowrap font-14">
					<image style="width: 15px;height: 15px;" class="mr-10" src="../../static/img/ff917a552a00ee1c66bb1bb7a2982c0.png"></image>
					距订单关闭{{getBtTime(data,'zf')}}
				</view>	
				<view class="one-row font-14">待付款</view>
			</view>
			<view class="w-100 pt-26 pb-26 pl-16 pr-16 flex-center flex-j-between font-white order-time" v-if="data.status==2">
				<view class="flex-center nowrap font-14">
					<image style="width: 15px;height: 15px;" class="mr-10" src="../../static/img/ff917a552a00ee1c66bb1bb7a2982c0.png"></image>
					距成团时间还有{{getBtTime(data,'ct')}}
				</view>	
				<view class="one-row font-14">待成团</view>
			</view> -->
			<view class="w-100 pt-12 pl-16 pr-16">
				<view class="w-100 bc-white br-4 pl-12 pr-12 pb-15 mb-16 cont-list">
					<view class="w-100 pb-15">
						<view class="w-100 flex-center flex-j-between pt-13 pb-12">
							<!-- 0全部 1待付款  2待成团 3已完成 4 已取消 -->
							<view class="flex-center nowrap">
								<image class="status mr-8" src="../../static/img/b1d1b3297c737adbd7a604d322885cc.png" v-if="data.status==1"></image>
								<image class="status mr-8" src="../../static/img/13c0d368b42f685a1a3682160b63259.png" v-if="data.status==4"></image>
								<text class="font-12 font-c-3 nowrap" v-text="'团队截止时间：'+app._formatDate(data.found_end_time)">据成团截止</text>
							</view>
							<view class="one-row status font-12" v-if="data.status==1">待付款</view>
							<view class="one-row status font-12" v-if="data.status==2">待成团</view>
							<view class="one-row status font-12" v-if="data.status==3">已完成</view>
							<view class="one-row status font-12" v-if="data.status==4">已取消</view>
						</view>
						<view class="w-100 flex-row flex-j-between cont-data">
							<image class="br-4" :src="data.good.original_img"></image>
							<view class="cont">
								<view class="w-100 font-15 font-c-3 newlines lh-18 show-row">
									<view class="btn font-10 font-white br-2 one-row mr-5">{{data.active.needer}}中{{data.active.stock_limit}}</view>
									{{data.good.good_name}}
								</view>
								<view class="w-100 flex-center flex-j-between mt-20">
									<view class="nowrap font-10 max-w-50 font-dark-main" v-if="data.is_lock_draw">恭喜获得{{data.active.luck_amount}} {{data.luckCoinName}}</view>
									<view class="nowrap font-10 max-w-50 font-dark-main" v-else>未中补偿{{ Number(data.active.return_amount)+Number(data.active.team_price) }} {{data.active.payCoinName}}</view>
									<view class="nowrap font-10 max-w-50 font-dark-main">
										<text class="font-14 font-c-9">需付:</text>
										<text class="font-14 font-c-3" v-text="data.active.team_price">100</text>
										<text class="font-10 font-c-3" v-text="data.payCoinName">斤花生</text>
									</view>
								</view>
							</view>
						</view>
					</view>
					<view class="w-100 br-4 text-center font-white font-14 font-w-b font-w-b" @click="goCT()" v-if="data.need-data.join!=0" style="height: 44px;line-height: 44px;background: linear-gradient(#FFB500,#FA6C00);">
						立即参团
					</view>
					<view class="w-100 br-4 text-center font-white font-14 font-w-b font-w-b" v-else style="height: 44px;line-height: 44px;background: linear-gradient(#FFB500,#FA6C00);" @click="goBuy()">
						发起拼团
					</view>
				</view>
				<view class="w-100 pl-12 pr-12 mb-16 bc-white br-4">
					<view class="w-100 flex-center flex-j-between pt-21 pb-21" @click="teamSW=!teamSW">
						<view class="team-cont nowrap flex-center">
							<view class="member flex-center flex-j-center" v-for="(item,index) in data.peoples" :key="index" :style="{'right':7*index+'px'}" v-if="index<5">
								<image :src="item.avatar?item.avatar:'../../static/img/bacc1bf65f6c0e8ed8c29f22df3743b.png'"></image>
							</view>
							<view class="member flex-center flex-j-center font-c-9" :style="{'right':7*(data.peoples.length)+'px','background-color':'#F1F1F1'}" v-if="data.peoples.length.length>5">
								<i class="iconfont icon-gengduo3 font-18 lh-23"></i>
							</view>
						</view>
						<view class="flex-center font-c-3 font-10 one-row">
							<view v-if="data.active.needer-data.peoples.length!=0">
								还差<text style="color: #FF2022;">{{data.active.needer-data.peoples.length}}人</text>拼成
							</view>
							<view v-else>
								拼团人数已满
							</view>
							<i class="iconfont icon-icon-test10 font-18 font-c-9 lh-12" v-if="!teamSW"></i>
							<i class="iconfont icon-icon-test9 font-18 font-c-9 lh-12" v-else></i>
						</view>
					</view>
					<view class="w-100 pt-8 pb-8" style="border-top: 1px solid #F8F8F8;" v-if="teamSW">
						<view class="w-100 pt-8 pb-8 flex-center flex-j-between" v-for="(item,index) in data.peoples" :key="index">
							<view class="flex-center nowrap">
								<image class="team-pic mr-6" :src="item.avatar?item.avatar:'../../static/img/bacc1bf65f6c0e8ed8c29f22df3743b.png'"></image>
								<text class="font-12 font-c-3 mr-8" v-text="item.user_name">189****1239</text>
								<text class="team-boss" v-if="item.userIsFound">团主</text>
							</view>
							<view class="font-12 font-c-9 one-row" v-text="app._formatDate(item.follow_time)">2020-02-15 15:45:06</view>
						</view>
					</view>
				</view>
				<view class="w-100 flex-center flex-j-center font-c-3 font-16 font-w-b pt-19 pb-19">
					<i class="icon-circle mr-8"></i>
					<i class="icon-circle mr-8" style="width: 4px;height: 4px;"></i>
					<i class="icon-circle mr-8" style="width: 5px;height: 5px;"></i>
					更多推荐
					<i class="icon-circle ml-8" style="width: 5px;height: 5px;"></i>
					<i class="icon-circle ml-8" style="width: 4px;height: 4px;"></i>
					<i class="icon-circle ml-8"></i>
				</view>
				<view class="w-100">
					<ptRecomd></ptRecomd>
				</view>
			</view>
		</view>
		<!-- 分享弹窗 -->
		<view class="winPopup w-100 flex-center flex-j-center pl-16 pr-16" v-if="inviteSW">
			<view class="w-100 bc-white br-4 pt-16 pl-16 pr-16">
				<view class="w-100" style="background-color: #E6E7F3;">
					<image style="width: 311px;height: 311px;" :src="data.good.original_img"></image>
				</view>
				<view class="w-100 flex-row flex-end font-24 font-w-b pt-16 pb-16" style="color: #FF2021;">
					{{data.active.team_price}}<text class="font-12 mb-2 ml-2" v-text="data.active.payCoinName">斤花生</text>
				</view>
				<view class="w-100 flex-row flex-j-between">
					<view style="width: calc(100% - 110px);">
						<view class="w-100 font-15 font-c-3 newlines lh-20 show-row">
							<view class="font-10 font-white br-2 one-row mr-5" style="display: inline-block;line-height: 20px;padding: 0px 5px;background: linear-gradient(#FFB500,#FA6C00);margin: 0px;">{{data.active.needer}}中{{data.active.stock_limit}}</view>
							{{data.good.good_name}}
						</view>
						<view class="w-100 font-dark-main font-12 pt-12 pb-16 newlines" v-if="data.is_lock_draw">
							恭喜获得{{data.active.luck_amount}} {{data.active.luckCoinName}}
						</view>
						<view class="w-100 font-dark-main font-12 pt-12 pb-16 newlines" v-else>
							未中补偿{{data.active.return_amount}} {{data.active.payCoinName}}
						</view>
						<view class="w-100 font-c-9 font-12 pt-12 pb-32 newlines">
							宣传口号宣传口号宣传口号！
						</view>
					</view>
					<tki-qrcode ref="qrcode" :val="data.active.invitation_code" :size="100" background="#fff" foreground="#000" pdground="#000" :onval="true" :loadMake="true"  :show="true" unit="px">
						
					</tki-qrcode>
				</view>
				<view class="w-100 flex-center flex-j-between pb-25">
					<view class="save-btn br-2 text-center" style="background: linear-gradient(#FFBE1E,#FF940B);">
						保存图片
					</view>
					<view class="save-btn br-2 text-center" style="background: linear-gradient(#FFB500,#FA6C00);" @click="app._copy(data.active.invitation_code)">
						复制口令
					</view>
				</view>
			</view>
		</view>
		<!-- 支付弹窗 -->
		<view class="winPopup w-100 flex-center flex-j-center pl-30 pr-30" v-if="paySW" @click="inviteSW=false">
			<view class="w-100 br-4 bc-white pl-24 pr-24 pb-24" @click.stop="">
				<view class="w-100 pt-27 pb-27 flex-center flex-j-between font-16 font-w-b font-c-3">
					<i></i>支付<i class="iconfont icon-icon-test6 font-20 font-c-9" @click="paySW=false"></i>
				</view>
				<view class="w-100 font-12 font-c-3 newlines">正在参与由{{data.teamBoss?data.teamBoss.user_name:userInfo.NickName}}发起的拼购活动，需支付{{data.active.team_price}} {{data.active.payCoinName}}。</view>
				<view class="w-100 flex-center flex-j-between pt-70 pb-16">
					<text class="font-c-6 font-12">可用余额：</text>
					<text class="font-c-6 font-12">{{order.user.Money}} {{data.payCoinName}}</text>
				</view>
				<view class="w-100">
					<input type="password" placeholder="请输入交易密码" class="pay-input pl-16 pr-16 br-2" v-model="pass" />
				</view>
				<view class="w-100 flex-center flex-j-center pt-20 pb-10">
					<button class="btn" @click="goPay()">确定</button>
				</view>
			</view>
		</view>
		<view class="winPopup flex-center flex-j-center" v-if="loader">
			<loader type="loader2"></loader>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	import loader from "@/components/loader/loader.vue";
	import ptRecomd from "@/components/pt-recomd.vue";
	import tkiQrcode from "@/components/tki-qrcode/tki-qrcode.vue"
	import {mapState,mapMutations} from 'vuex'
	
	export default {
		components: {
			ptRecomd,
			loader,
			tkiQrcode
		},
		data() {
			return {
				loader:false,
				data:"",
				teamSW:false,
				pass:"",
				paySW:false,
				inviteSW:false,
				inviteInfo:"",
				order:"",
				nw:"",
			}
		},
		onLoad(e) {
			let self=this;
			let data=JSON.parse(e.data);
			console.log(data);
			self.data=data;
			// this.getDetails(data);
			
			// self.nw=new Date().getTime();
			// setInterval(function(){
			// 	self.nw=new Date().getTime();
			// },1000);
		},
		computed:{
			...mapState(['userInfo','assets']),
		},
		methods: {
			getBtTime:function(item,type){
				var that = this;
				var end;
				if(type=='ct'){
					end = ((item.found.found_end_time*1000)+(24*60*60*1000));
				}else{
					end = ((item.end_pay_time*1000)+(24*60*60*1000));
				};
				var leftTime = end - this.nw; //时间差    
				var d, h, m, s, ms;
				if (leftTime >= 0) {
					d = Math.floor(leftTime / 1000 / 60 / 60 / 24);
					h = Math.floor(leftTime / 1000 / 60 / 60 % 24);
					m = Math.floor(leftTime / 1000 / 60 % 60);
					s = Math.floor(leftTime / 1000 % 60);
					ms = Math.floor(leftTime % 1000);
					ms = ms < 100 ? "0" + ms : ms
					s = s < 10 ? "0" + s : s
					m = m < 10 ? "0" + m : m
					h = h < 10 ? "0" + h : h
					let res=h + ":" + m + ":" + s
					if(type=='ct'){
						item.CTtime=res;
					}else{
						item.time=res;
					};
					return h + ":" + m + ":" + s
					//递归每秒调用countTime方法，显示动态时间效果
				} else {
					if(type=='ct'){
						item.CTtime='已截止';
					}else{
						item.time='已截止';
					};
					return '已截止'
				}
			},
			goCT:function(){
				var self=this;
				self.loader=true;
				let url=config.api + "/shop.join.activity";
				let send={
					foundId:self.data.found_id,
				}
				uni.request({
					url: url,
					data: send,
					method: "POST",
					header: {Authorization: config.getToken()},
					success: res => {
						console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.paySW=true;
							self.order=res.data.data;
						}else{
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						self.loader=false;
					}
				});
			},
			goPay:function(){
				var self=this;
				if(self.order.user.Money < self.data.team_price){
					return self.app._toast("余额不足");
				}
				if(!self.pass||self.pass.length==0){
					return self.app._toast("请输入支付密码");
				}
				self.loader=true;
				let url=config.api + "/shop.activity.pay";
				let send={
					password:self.pass,
					orderSn:self.order.orderSn,
				}
				uni.request({
					url: url,
					data: send,
					method: "POST",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						self.app._toast(res.data.message);
						if(res.data.status==1){
							setTimeout(function(){
								self.app.goBack();
							},300);
						};
						self.paySW=false;
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						self.loader=false;
					}
				});
			},
			goAgain:function(){
				uni.navigateBack({
					delta: 2,
					success:function(res){
						
					}
				});
			},
			goBuy:function(){
				let self=this;
				uni.redirectTo({
					url:"commodity?data="+JSON.stringify(self.data)
				});
			},
			getDetails:function(e){
				var self=this;
				self.loader=true;
				let url=config.api + "/shop.my.found.details";
				uni.request({
					url: url,
					data: {
						followId:e.follow_id,
					},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						console.log(res);
						config.api_status(res);
						if(res.data.status==1){
							let people=res.data.data.peoples.filter(function(item){
								return item.userIsFound;
							})[0];
							res.data.data.teamBoss=people;
							self.data=res.data.data;
							console.log(self.data);
						}else{
							// self.app.goBack();
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						self.loader=false;
					}
				});
			},
		}
	}
</script>

<style lang="less">
	@import url("@/common/newStyle/base.css");
	@import url("@/common/newStyle/iconfont.css");
	@import url("@/common/newStyle/common.css");
	page{
		background-color: #F7F7F7;
	}
	.order-time{
		background: linear-gradient(bottom,#FA6C00,#FFB500);
	}
	.cont-list{
		image.status{
			width: 15px;height: 15px;
		}
		.status{
			color: #FF2021;
		}
		.cont-data{
			image{
				width: 83px;height: 83px;background-color: #E6E7F3;
			}
			.cont{
				width: calc(100% - 100px);
				.btn{
					display: inline-block;
					line-height: 20px;padding: 0px 5px;
					background: linear-gradient(bottom,#FA6C00,#FFB500);margin: 0px;
				}
			}
		}
	}
	.team-cont{
		.member{
			width: 23px;height: 23px;line-height: 23px;border-radius: 50%;right: 11px;
			image{
				width: 100%;height: 100%;border-radius: 50%;background-color: #E6E7F3;
			}
		}
	}
	.icon-circle{
		background: linear-gradient(#FA6C00,#FFB500);
		border-radius: 50%;display: inline-flex;
		width: 5rpx;height: 5rpx;
	}
	.team-pic{
		width: 23px;height: 23px;background-color: #E6E7F3;border-radius: 50%;
	}
	.team-boss{
		border: 1rpx solid #FA6C00;background-color: #FFF7F1;
		color: #FA6C00;font-size: 10px;padding: 3px 9px;border-radius: 10px;
	}
	.save-btn{
		width: 48%;height: 40px;line-height: 40px;color: #FFFFFF;
	}
	.foot-btn{
		height: 44px;line-height: 44px;
		background: linear-gradient(#FFB500,#FA6C00);
		position: fixed;
		bottom: 0px;
		left: 0px;
		z-index: 11;
	}
	.pay-input{
		width: 100%;height: 40px;background-color: #F7F7F7;font-size: 14px;color: #333333;
	}
</style>
