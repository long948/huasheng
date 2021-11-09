<template>
	<view class="box">
		<view class="top-data pt-45 pl-20 pr-20">
			<image class="bc" src="../../static/img/bc46dd3e574ff66753d63d2a028abc1.png"></image>
			<view class="set-up flex-center flex-j-end">
				<image class="ml-20" src="../../static/img/421b0c083ba075df417d6a6d7b79926.png" @click="go_withdraw()"></image>
				<image class="ml-20" src="../../static/img/b5f67d1ced39a7d88a4f6863d3b8913.png" @click="app.showOpen('wallet/recharge')"></image>
			</view>
			<view class="user-Info flex-center flex-j-between">
				<view class="head-portrait">
					<image :src="userInfo.Avatar" @click="userInfo.auth_status==2?app.showOpen('index/AuthDetails'):userInfo.auth_status==3?app.showOpen('index/AuthDetails'):''"></image>
					<view class="status text-center" v-text="userInfo.auth_status==0?lang.user1:userInfo.auth_status==1?lang.user2:userInfo.auth_status==2?lang.user3:lang.user4" @click="getAuth()">已实名</view>
				</view>
				<view class="user-data" >
					<view class="name flex-center nowrap pb-20" style="align-items: flex-start;">
						<text class="font-16 font-white" v-text="userInfo.NickName">用户名</text>
						<!-- <image v-if="data.user.level" class="ml-5" mode="widthFix" :src="data.user.level.url"></image> -->
					</view>
					<view class="status-Info flex-center nowrap">
						<!-- <view class="bt br-1 font-10">邀请码：{{userInfo.InviteCode}}</view> -->
						<!-- <view class="bt br-1 font-10 ml-10" v-if="data.user.package">机器人已开通</view> -->
						<!-- <view class="bt br-1 font-10 ml-10" v-else>机器人未开通</view> -->
						<view class="grade br-8 font-10 font-white" @click="gradeSW=true">{{lang.user5}}{{userInfo.Level}}</view>
						<view class="name br-8 font-10 ml-10 font-white" v-if="userInfo.isPartner">{{lang.user6}}</view>
						<block v-if="userInfo.IsFrozenCTC==1">
							<view class="bt br-1 font-10 ml-10">{{lang.user7}}</view>
							<view @click="app.showOpen('trade/frozen')" class="bt br-1 font-10 ml-10 br-2" style="background: transparent;border: 1px solid #FFB500;">{{lang.user8}}</view>
						</block>
					</view>
				</view>
			</view>
		</view>
		<view class="content pl-10 pr-10 pb-50">
			<view class="assets flex-center flex-j-between bc-white br-4 pt-25 pb-25 mb-10">
				<view class="w-50">
					<view class="nowrap w-100 text-center font-14 font-main" v-text="'花田亩数'">{{lang.user9}}</view>
					<view class="nowrap w-100 text-center font-14 font-grey mt-5" v-text="assets.power"> 20500.0000 </view>
				</view>
				<view class="w-50">
					<view class="nowrap w-100 text-center font-14 font-main">花田备用斤</view>
					<view class="nowrap w-100 text-center font-14 font-grey mt-5" v-text="(assets.userGiveAwayAmount?assets.userGiveAwayAmount:'--')+' (斤)'"> 20500.0000 </view>
				</view>
				<!-- <view class="w-33">
					<view class="nowrap w-100 text-center font-14 font-main">{{lang.user10}}</view>
					<view class="nowrap w-100 text-center font-14 font-grey mt-5" v-text="(assets.output?assets.output:'--')+' (斤)'"> 20500.0000 </view>
				</view> -->
			</view>
			<view class="amount-assets bc-white br-4 pt-25 pb-25 pl-25 pr-25 mb-10">
				<view class="font-14 pb-10" style="color: rgba(60,60,60,0.6);">{{lang.user11}} <text class="font-dark-main ml-10" @click="tfSW=true">去划转</text></view>
				<view class="font-14 font-grey flex-center nowrap">
					<text>{{assets.total_assets}} PT </text>
					<text class="ml-10" style="color: rgba(60,60,60,0.6);">≈{{assets.total_assets_cny}} CNY</text>
				</view>
				<view class="btn pl-10" @click="app.showOpen('wallet/wallet')">{{lang.user12}}</view>
			</view>
			<view class="bc-white w-100 br-4 pt-20 pb-25">
				<view class="font-14 font-grey font-w-b pb-10 pl-15 pr-15">{{lang.user13}}</view>
				<view class="list flex-center flex-j-around mt-20">
					<view class="text-center w-30" @click="app.showOpen('user/team')">
						<image src="../../static/img/ec530342795890a681e20cec4b19dd5.png"></image>
						<view class="w-100 text-center font-12 font-grey pt-5">{{lang.user14}}</view>
					</view>
					<view class="text-center w-30" @click="goShare()">
						<image src="../../static/img/e59693d03d3b274e0b57ff30a6833ca.png"></image>
						<view class="w-100 text-center font-12 font-grey pt-5">{{lang.user15}}</view>
					</view>
					<view class="text-center w-30" @click="app.showOpen('user/article')">
						<image src="../../static/img/04c09a84ddec8a509c52e602aa46eee.png"></image>
						<view class="w-100 text-center font-12 font-grey pt-5">{{lang.user16}}</view>
					</view>
				</view>
				<view class="list flex-center flex-j-around mt-20">
					<view class="text-center w-30" @click="app.showOpen('user/problem')">
						<image src="../../static/img/fdcba99e829d0e3cd340b7e9d6aad4c.png"></image>
						<view class="w-100 text-center font-12 font-grey pt-5">问题反馈</view>
					</view>
					<view class="text-center w-30" @click="app._toast(lang.user23)">
						<image src="../../static/img/b1dd0af875acde452e5730e998d2eb7.png"></image>
						<view class="w-100 text-center font-12 font-grey pt-5">交易所</view>
					</view>
					<view class="text-center w-30" @click="app._toast(lang.user23)">
						<image src="../../static/img/f2af3f19d503cf3b5b17c2d3d351680.png"></image>
						<view class="w-100 text-center font-12 font-grey pt-5">{{lang.user19}}</view>
					</view>
				</view>
				<view class="list flex-center flex-j-around mt-20">
					<view class="text-center w-30" @click="app._toast(lang.user23)"><!-- @click="rechargeSW=true" -->
						<image src="../../static/img/32f11e774fd4286d1df7e75ed99faa9.png"></image>
						<view class="w-100 text-center font-12 font-grey pt-5">{{lang.user20}}</view>
					</view>
					<!-- <view class="text-center w-30" @click="app._toast(lang.user23)">
						<image src="../../static/img/7df228ede3b213af53c4e7887bd12d5.png"></image>
						<view class="w-100 text-center font-12 font-grey pt-5">{{lang.user21}}</view>
					</view> -->
					<view class="text-center w-30" @click="app.showOpen('Shopping/index')">
						<image src="../../static/img/7df228ede3b213af53c4e7887bd12d5.png"></image>
						<view class="w-100 text-center font-12 font-grey pt-5">拼团</view>
					</view>
					<view class="text-center w-30" @click="app.showOpen('user/userInfo')">
						<image src="../../static/img/9695cb05674aab7ae3e76c5231e730f.png"></image>
						<view class="w-100 text-center font-12 font-grey pt-5">{{lang.user22}}</view>
					</view>
				</view>
			</view>
			<view class="w-100 pb-20"></view>
		</view>
		<view class="winPopup flex-center flex-j-center pl-20 pr-20" v-if="gradeSW">
			<view class="grade-content w-100">
				<view class="grade-popup-close font-white" @click="gradeSW=false">
					<i class="iconfont icon-ziyuan"></i>
				</view>
				<view class="content bc-white pl-15 pr-15 pb-20 w-100 br-10">
					<view class="w-100 text-center font-18 bt_line pb-15 pt-15 lh-20 font-grey">{{lang.user24}}</view>
					<block v-if="gradeInfo.user_info">
						<view class="w-100 flex-center flex-j-between pt-30 pb-10">
							<view class="w-33 op-8 font-14 font-grey text-left" v-text="gradeInfo.user_info.now_level">E1</view>
							<view class="w-33 op-8 font-14 font-grey text-center" v-text="gradeInfo.user_info.now_invite+'/'+gradeInfo.user_info.target_invite+lang.user25">2/5</view>
							<view class="w-33 op-8 font-14 font-grey text-right" v-text="gradeInfo.user_info.target_level">E2</view>
						</view>
						<view class="progress w-100 flex-center flex-j-between mb-20">
							<view class="val" :style="{'width':app._accMul(gradeInfo.user_info.invite_rate,100)+'%;'}"></view>
						</view>
						<view class="w-100 flex-center flex-j-between pb-10">
							<view class="w-33 op-8 font-14 font-grey text-left" v-text="gradeInfo.user_info.now_level">E1</view>
							<view class="w-33 op-8 font-14 font-grey text-center" v-text="gradeInfo.user_info.now_buy+'/'+gradeInfo.user_info.target_buy+'PT'">2/5</view>
							<view class="w-33 op-8 font-14 font-grey text-right" v-text="gradeInfo.user_info.target_level">E2</view>
						</view>
						<view class="progress w-100 flex-center flex-j-between mb-30">
							<view class="val" :style="{'width':app._accMul(gradeInfo.user_info.buy_rate,100)+'%;'}"></view>
						</view>
					</block>
					<view class="form-cont">
						<view class="flex-center flex-j-between w-100">
							<view class="ct text-center nowrap ct-title">{{lang.user26}}</view>
							<view class="ct text-center nowrap ct-title">{{lang.user27}}/PT</view>
							<view class="ct text-center nowrap ct-title">{{lang.user28}}</view>
						</view>
						<view class="flex-center flex-j-between w-100" v-for="(item,index) in gradeInfo.rule" :key="index">
							<view class="ct text-center nowrap" v-text="item.name">E1</view>
							<view class="ct text-center nowrap" v-text="item.invite+'/'+item.buy">0</view>
							<view class="ct text-center nowrap" v-text="+app._accMul(item.rate,100)+'%'">50%</view>
						</view>
					</view>
					<view class="w-100 newlines font-dark-grey font-12 pt-10 pl-10 pr-10">{{lang.user29}}</view>
				</view>
			</view>
		</view>
		<share :show="shareSW" :shareSW.sync="shareSW"></share>
		<view class="winPopup" v-if="rewardSW">
			<reward :show="rewardSW" :rewardSW.sync="rewardSW"></reward>
		</view>
		<!-- <tabBar :name="'user'"></tabBar> -->
		<view class="winPopup" v-if="authSW">
			<authResult :show="authSW" :authSW.sync="authSW"></authResult>
		</view>
		<!-- <view class="winPopup pl-15 pr-15 flex-center flex-j-center" v-if="rechargeSW">
			<view class="w-100 recharge-cont">
				<view class="-close font-white" @click="rechargeSW=false">
					<i class="iconfont icon-ziyuan"></i>
				</view>
				<button class="btn w-100 mb-30" @click="app.showOpen('user/ykRecharge');rechargeSW=false;">油卡充值</button>
				<button class="btn w-100" @click="app.showOpen('user/hfRecharge');rechargeSW=false;">话费充值</button>
			</view>
		</view> -->
		<view class="winPopup flex-center flex-j-center pl-15 pr-15 pb-50" v-if="tfSW">
			<view class="transfer-cont font-black bc-white pb-25 w-100 br-6">
				<i class="iconfont icon-Group- close font-20" @click="tfSW=false"></i>
				<!-- <image class="close" src="../../static/img/f1be31ff039fecb8b1f832c951e42a1.png" @click="tfSW=false"></image> -->
				<view class="text-center font-16 w-100 font-w-b lh-30 mb-10">账户划转</view>
				<view class="input-list flex-center flex-j-between">
					<input type="number" placeholder="请输入划转数量" v-model="num" />
				</view>
				<view class="input-list flex-center flex-j-between">
					<input type="password" placeholder="请输入交易密码" v-model="pass" />
				</view>
				<view class="w-100 text-right mb-10 font-12 font-dark-main"><text @click="app.showOpen('login/forgetPay')">忘记交易密码</text></view>
				<view class="w-100">
					<button class="btn" @click="gotransfer()">确定</button>
				</view>
				<!-- <view class="w-100 flex-center nowrap pt-15">
					<view class="font-12 nowrap">可划转余额：{{app._toFixed(assets.money,2)}} USDT</view>
					<text class="one-row font-blue font-12 ml-10" @click="num=app._toFixed(assets.money,2)">全部划转</text>
				</view> -->
				
			</view>
		</view>
		<view class="w-100 winPopup flex-center flex-j-center pl-20 pr-20" v-if="ctpop">
			<view class="w-100 bc-white br-4 pt-10 pl-20 pr-20 pb-20 font-black">
				<view class="w-100 text-center pt-15 pb-15 font-c-3 font-16 font-w-b">
					邀请参团
				</view>
				<view class="w-100 font-14 font-c-6 newlines pb-15">
					<!-- 用户{{ctInfo.yqPeoples}}正在邀请您参团，海量奖励等你来拿! -->
					你的好友正在邀请您参团，海量奖励等你来拿!
				</view>
				<view class="w-100 flex-center flex-j-between" style="margin-top: 20px;">
					<view class="yq-btn" @click="ctpop=false">取消</view>
					<view class="yq-btn" @click="goCT()">确定</view>
				</view>
			</view>
		</view>
		<view class="winPopup" v-if="loader">
			<loader type="loader2"></loader>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js"
	// import tabBar from "@/components/tabBar/tabBar.vue";
	import share from "@/components/share.vue";
	import reward from "@/components/reward.vue";
	import authResult from "@/components/authResult.vue";
	import loader from "@/components/loader/loader.vue";
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		components: {
			// tabBar,
			share,
			reward,
			authResult,
			loader
		},
		data() {
			return {
				loader:false,
				gradeSW:false,
				gradeInfo:"",
				data:"",
				shareSW:false,
				qq:"2648268870",
				rewardSW:false,
				authSW:false,
				rechargeSW:false,
				tfSW:false,
				num:"",
				pass:"",
				//邀请参团的弹窗
				ctpop:false,
				ctInfo:"",
			}
		},
		onShow() {
			var self=this;
			self.setTabBar();
			uni.setStorageSync("pop",1);
			self.data=uni.getStorageSync("levelInfo");
			self.set_assets();
			self.get_Grade();
			self.getQQ();
			self.setUserInfo();
			self.getReward();
			if(self.RewardInfo.is_giveAway==1&&self.userInfo.auth_status==3){
				self.rewardSW=true;
			}else{
				self.rewardSW=false;
			};
			// console.log(self.userInfo);
			// console.log(config.getToken());
		},
		onPullDownRefresh() {
			var self=this;
			self.data=uni.getStorageSync("levelInfo");
			self.setUserInfo();
			self.set_assets();
			self.get_Grade();
			self.getQQ();
			setTimeout(function () {
				uni.stopPullDownRefresh();
			}, 1000);
		},
		computed:{
			...mapState(['userInfo','assets','RewardInfo','lang']),
		},
		methods: {
			...mapMutations(["setUserInfo","set_assets","getReward"]),
			setTabBar:function(){
				var self=this;
				uni.setTabBarItem({
					index: 0,
					text: self.lang.tabBar1,
				});
				uni.setTabBarItem({
					index: 1,
					text: self.lang.tabBar2,
				});
				uni.setTabBarItem({
					index: 2,
					text: self.lang.tabBar3,
				});
				uni.setTabBarItem({
					index: 3,
					text: self.lang.tabBar4,
				});
			},
			gotransfer:function(){
				var self=this;
				if(!self.pass){
					return self.app._toast("请输入交易密码");
				};
				if(!self.num){
					return self.app._toast("请输入划转数量");
				};
				let send={
					amount:self.num,
					pay_password:self.pass,
				};
				self.loader=true;
				let url=config.api + "/user-income-turning";
				uni.request({
					url: url,
					data: send,
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.tfSW=false;
							self.num="";
							self.pass="";
							self.app._toast(res.data.message);
							self.set_assets();
						}else{
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(res);
					},
					complete: (res) => {
						setTimeout(function(){
							self.loader=false;
						},500);
					}
				});
			},
			getAuth:function(){
				var self=this;
				if(self.userInfo.auth_status==2){
					self.app.showOpen('index/AuthDetails')
				}else if(self.userInfo.auth_status==3){
					self.app.showOpen('user/userInfo')
				}else if(self.userInfo.auth_status==0){
					self.app.showOpen('auth/auth')
				};
			},
			goShare:function(){
				var self=this;
				if(self.userInfo.auth_status==1){
					self.authSW=true;
					return;
				};
				if(self.userInfo.auth_status==2){
					self.app.showOpen('index/AuthDetails');
					return;
				};
				if(self.userInfo.auth_status!=3){
					setTimeout(function(){
						self.app.showOpen('auth/auth');
					},500);
					return self.app._toast(self.lang.user30);
				};
				self.shareSW=true;
			},
			goCT:function(){
				let self=this;
				self.app.showOpen('Shopping/Shopping?data='+self.ctInfo);
				self.ctpop=false;
			},
			go_withdraw:function(){
				var self=this;
				uni.scanCode({
					scanType:["qrCode"],
				    success: function (res) {
						console.log(res);
						let result=res.result;
						// self.app.showOpen('wallet/withdraw?address='+result);
						// return;
						try{
							let len=result.indexOf("&code=");
							console.log(result);
							console.log(len);
							if(result && result.startsWith('฿') && len!=-1){
								let url=config.api + "/shop.found.code";
								let code=result.substr(len+6);
								uni.request({
									url: url,
									data: {
										code:code,
									},
									method: "get",
									header: {Authorization: config.getToken()},
									success: res => {
										console.log(res);
										if(res.data.status==1){
											if(res.data.data){
												// res.data.data.yqPeoples=value.user;
												self.ctInfo=JSON.stringify(res.data.data);
												self.ctpop=true;
											}
										}else{
											console.log(res);
										};
									},
									fail: (res) => {
										console.log(JSON.stringify(res));
									},
									complete: (res) => {
										
									}
								});
							}else{
								self.app.showOpen('wallet/withdraw?address='+result);
							}
						}catch(e){
							console.log(e)
							// self.app.showOpen('wallet/withdraw?address='+result);
						}
				    }
				});
			},
			get_Grade:function(){
				var self=this;
				let url=config.api + "/get.tx.power";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.gradeInfo=res.data.data;
						}else{
							console.log((res));
						};
					},
					fail: (res) => {
						console.log((res));
					},
					complete: (res) => {}
				});
			},
			getQQ:function(){
				var self=this;
				let url=config.api + "/qq";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.qq=res.data.data;
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
			openQQ: function (e) {
				var self=this;
				// crm   wpa
				// let url="mqqwpa://im/chat?chat_type=wpa&uin="+self.qq;
				let url="mqqwpa://im/chat?chat_type=wpa&uin="+self.qq+"&version=1&src_type=web&web_src=oicqzone.com";
				plus.runtime.openURL(url,function (res) {
					plus.nativeUI.alert(self.lang.user31);  
				});			},
		},
		watch: {
			hasLogin: function(newValue, oldValue) {
				var self=this;
				if(newValue){
					self.data=uni.getStorageSync("levelInfo");
					self.set_assets();
					self.get_Grade();
					self.getQQ();
				};
			},
			RewardInfo:function(newValue){
				var self=this;
				if(newValue){
					if(self.RewardInfo.is_giveAway==1&&self.userInfo.auth_status==3){
						self.rewardSW=true;
					}else{
						self.rewardSW=false;
					};
				};
			}
		}
	}
</script>

<style>
	page{background-color: #F5F5F5;}
	.top-data{
		width: 100%;
		height: 180px;
		/* background: url(../../static/img/fe390698e01a5909f42f03f273f8368.png) no-repeat; */
		/* background: url(../../static/img/bc46dd3e574ff66753d63d2a028abc1.gif) no-repeat; */
		background-size: 100% 100%;
		position: relative;
	}
	.top-data image.bc{position: absolute;top: 0px;left: 0px;bottom: 0px;right: 0px;width: 100%;height: 100%;display: block;}
	.top-data .set-up,.top-data .user-Info{position: relative;z-index: 1;}
	.top-data .set-up image{width: 18px;height: 18px;}
	.top-data .head-portrait{position: relative;}
	.top-data .head-portrait image{width: 56px;height: 56px;display: inline-block;border-radius: 50%;}
	.top-data .head-portrait .status{background: linear-gradient(#F8E4BA,#F8D792,#FD9253);color: #A0340A;font-size: 10px;width: 100%;height: 15px;line-height: 15px;border-radius: 8px;position: absolute;bottom: 1px;left: 0px;}
	.top-data .user-data{width: calc(100% - 70px);}
	.top-data .user-data .name{width: 100%;}
	.top-data .user-data .name image{width: 40px;display: inline-block;}
	.top-data .user-data .bt{color: rgba(255,255,255,0.8);height: 18px;line-height: 18px;padding: 0px 8px;background-color: rgba(250,108,0);}
	.top-data .user-data .grade{height: 18px;line-height: 18px;padding: 0px 8px;background: linear-gradient(bottom,#FA6C00,#FFB500);}
	.top-data .user-data .status-Info .name{display: inline-block;height: 18px;line-height: 18px;padding: 0px 8px;background: linear-gradient(#F8E4BA,#F8D792,#FD9253);width: auto;}
	.box>.content{position: relative;top: -30px;z-index: 2;width: 100%;}
	.content .assets{box-shadow: 0px 2px 4px 2px #E8EAF0;position: relative;}
	.content .assets::after{position: absolute;top: 25px;bottom: 25px;left: 50%;width: 1px;background-color: #979797;opacity: 0.3;content: "";-webkit-transform: scaleX(.5);transform: scaleX(.5);}
	.content .amount-assets{box-shadow: 0px 2px 4px 2px #E8EAF0;position: relative;}
	.content .amount-assets .btn{
		position: absolute;right: 0px;top: calc(50% - 15px);width: 70px;height: 30px;line-height: 30px;border-radius: 25px 0px 0px 25px;background: linear-gradient(bottom,#FA6C00,#FFB500);text-align: center;
	}
	.content .list{width: 100%;height: auto;}
	.content .list image{width: 54px;height: 54px;display: inline-block;}
	.recharge-cont{position: relative;}
	.recharge-cont button.btn{background: linear-gradient(bottom,#FA6C00,#FFB500);height: 44px;line-height: 44px;}
	.recharge-cont .-close{position: absolute;top:-50px;right: 10px;}
	.recharge-cont .-close .iconfont{font-size: 25px;font-weight: bold;}
	
	.transfer-cont{
		padding: 20px 15px;position: relative;
	}
	.transfer-cont .close{
		width: 20px;height: 20px;position: absolute;right: 9px;top: 9px;
	}
	.transfer-cont .input-list{
		width: 100%;height: 40px;border: 1rpx solid #E8E8E8;border-radius: 2px;
		margin-bottom: 20px;
	}
	.transfer-cont .input-list input{
		width: 100%;height: 100%;font-size: 14px;color: #333333;padding: 0px 15px;
	}
	.transfer-cont .input-list picker{
		width: 100%;height: 100%;line-height: 40px;padding: 0px 15px;box-sizing: border-box;
	}
	.transfer-cont button{
		width: 100%;
		background: linear-gradient(right,#FA6C00,#FFB500);
	}
	.yq-btn{
		width: 47%;height: 40px;line-height: 40px;border: 1px solid #FA6C00;color: #FA6C00;
		border-radius: 5px;text-align: center;
	}
	.yq-btn:nth-child(2){
		background-color: #FA6C00;color: #FFFFFF;
	}
</style>
