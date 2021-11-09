<template>
	<view class="box">
		<view class="top-data" :class="{'active':timeSW}">
			<view class="pl-40 pr-40 pb-10">
				<view class="flex-center flex-j-between">
					<view class="w-50 text-center">
						<view class="w-100 font-20 font-white nowrap" v-text="ctcInfo.coin_price+' $'">0.0000</view>
						<view class="font-12 font-white op-8">{{lang.trade1}}</view>
					</view>
					<view class="w-50 text-center">
						<view class="w-100 font-20 font-white nowrap" v-text="ctcInfo.coin_price!='--'?app._accMul(ctcInfo.coin_price,7)+' ¥':'---'">0.0000</view>
						<view class="font-12 font-white op-8">{{lang.trade1}}</view>
					</view>
				</view>
				<view class="flex-center flex-j-between pt-10">
					<view class="w-50 text-center">
						<view class="w-100 font-20 font-white nowrap" v-text="ctcInfo.buy_amount">0.0000</view>
						<view class="font-12 font-white op-8">{{lang.trade2}}</view>
					</view>
					<view class="w-50 text-center">
						<view class="w-100 font-20 font-white nowrap" v-text="ctcInfo.tx_amount">0.0000</view>
						<view class="font-12 font-white op-8">{{lang.trade3}}</view>
					</view>
				</view>
			</view>
			<view class="notice-cont flex-center flex-j-between pl-15 pr-15">
				<image src="../../static/img/fd54b04f438f2eff271e2d96937bcec.png"></image>
				<swiper class="notice nowrap" autoplay="true" circular="true" interval="6000">
					<swiper-item v-for="(item, index) in notice" :key="index">
						<text class="w-100 nowrap" @click="app.showOpen('index/newsDetails?id='+item.Id)" v-text="item.TypeTitle"></text>
					</swiper-item>
				</swiper>
				<view class="Tday font-12 one-row" style="color: #E02020;">
					<text v-if="timeSW">距离交易结束还剩 {{Tday}}</text>
				</view>
			</view>
		</view>
		<view class="pl-15 pr-15 w-100">
			<view class="link-btn w-100 flex-center flex-j-between" v-if="timeSW">
				<button @click="goBuy()">{{lang.trade4}}</button>
				<button @click="app.showOpen('trade/mybuy')">{{lang.trade5}}</button>
				<button @click="app.showOpen('trade/order')">{{lang.trade6}}</button>
			</view>
			<view class="link-btn w-100 flex-center flex-j-between" v-else>
				<button disabled="true" >{{lang.trade4}}</button>
				<button disabled="true" >{{lang.trade5}}</button>
				<button disabled="true" >{{lang.trade6}}</button>
			</view>
			<view class="w-100 pt-5">
				<view class="choice-price one-row font-12 flex-center flex-j-between">
					<view class="ch-pri font-grey mr-20 flex-center" :class="{'active':nav=='-1'}" @click="nav='-1';initlist()">
						<text class="mr-10 font-w-b">{{lang.trade7}}</text>
					</view>
					<view class="ch-pri font-grey mr-20 flex-center" v-for="(item,index) in typeList" :class="{'active':index==nav}" :key="index" @click="nav=index;initlist()">
						<text class="mr-10 font-w-b">{{item}}</text>
					</view>
				</view>
			</view>
			<view class="w-100 pt-5" v-if="timeSW">
				<view class="cont-list bc-white br-4 mb-15" v-for="(item,index) in list" :key="index">
					<view class="user-Info w-100 nowrap flex-center flex-j-between">
						<view class="font-16 font-grey flex-center nowrap" style="width: 60%;">
							<image class="mr-10 Avatar" :src="item.Avatar?item.Avatar:'../../static/img/bacc1bf65f6c0e8ed8c29f22df3743b.png'"></image>
							{{item.Phone}}
							<view class="flex-center ml-10 mt-5">
								<image class="py" src="../../static/img/bc613dffddbfeeb9fde4ebacc2ee407.png" v-if="item.IsAlipay"></image>
								<image class="py" src="../../static/img/53c183e8d60199ef97be47fdd75a547.png" v-if="item.IsAddress"></image>
							</view>
						</view>
						<view class="flex-row flex-end nowrap">
							<text class="font-main font-16 font-w-b nowrap">{{app._toFixed(app._accMul(item.Price,7),4)}} CNY</text>
							<!-- <text class="font-main font-16 font-w-b nowrap">{{app._toFixed(app._accMul(ctcInfo.coin_price,7),4)}} CNY</text> -->
							<text class="op-7 font-grey font-12 one-row ml-5 lh-20">/PT</text>
						</view>
					</view>
					<view class="btn-cont w-100 flex-center flex-j-between pt-5">
						<view class="cont">
							<view class="flex-center nowrap" style="margin-top: 8px;">
								<text class="one-row font-grey font-16 op-5">{{lang.trade8}}：</text>
								<view class="font-grey nowrap font-16"> {{app._toFixed(item.Number,2)}}PT</view>
							</view>
							<view class="flex-center nowrap" style="margin-top: 8px;">
								<text class="one-row font-grey font-16 op-5">{{lang.trade9}}：</text>
								<view class="font-grey nowrap font-16"> {{app._toFixed(app._accMul(item.Number,app._accMul(ctcInfo.coin_price,7) ),2)}} ¥</view>
							</view>
						</view>
						<button class="btn font-16 font-white text-center" @click="goSell(item)">{{lang.trade10}}</button>
					</view>
				</view>
				<uni-load-more :status="loadingType"></uni-load-more>
			</view>
			<view class="w-100 pt-15" v-else>
				<view class="text-center font-w-b font-18 pt-30 pb-10" style="color: #E02020;">{{lang.trade11}}</view>
				<view class="text-center font-grey">{{lang.trade12}}：{{timeInfo.start_time}}:00-{{timeInfo.end_time}}:59</view>
			</view>
		</view>
		<view class="winPopup flex-center flex-j-center pl-20 pr-20" v-if="rule">
			<view class="grade-content w-100">
				<view class="grade-popup-close font-white" @click="readRule()">
					<i class="iconfont icon-ziyuan"></i>
				</view>
				<view class="content bc-white pl-15 pr-15 pb-30 w-100 br-10">
					<view class="w-100 text-center font-18 bt_line pb-15 pt-15 lh-20 font-grey">{{lang.trade13}}</view>
					<view class="w-100 font-12 font-cl-3 lh-20 newlines pt-15">
						<rich-text :nodes="RuleInfo"></rich-text>
					</view>
				</view>
			</view>
		</view>
		<!-- <tabBar :name="'trade'"></tabBar> -->
		<!-- 出售弹窗 -->
		<view class="winPopup flex-center flex-j-center pl-25 pr-25 pt-50 pb-50" v-if="sellSW">
			<view class="sell-content w-100 br-8 bc-white">
				<view class="title font-18 font-grey text-center">
					{{lang.trade14}}PT
					<view class="sell-popup-close font-white flex-center" @click="sellSW=false;">
						<i class="iconfont icon-ziyuan"></i>
					</view>
				</view>
				<view class="w-100 pl-15 pr-15">
					<view class="flex-row flex-j-between lh-20 mb-15">
						<text class="font-14 one-row mr-15 name">{{lang.trade15}}：</text>
						<view class="font-14 font-grey newlines" v-text="sellInfo.Phone">139****3521</view>
					</view>
					<view class="flex-row flex-j-between lh-20 mb-15">
						<text class="font-14 one-row mr-15 name">{{lang.trade16}}：</text>
						<view class="font-14 font-grey newlines">{{app._toFixed(app._accMul(ctcInfo.coin_price,7),2)}} CNY</view>
					</view>
					<view class="flex-row flex-j-between lh-20 mb-15">
						<text class="font-14 one-row mr-15 name">{{lang.trade17}}：</text>
						<view class="font-14 font-grey newlines">{{app._toFixed(sellInfo.Number,2)}} PT</view>
					</view>
					<view class="flex-row flex-j-between lh-20 mb-15">
						<text class="font-14 one-row mr-15 name">{{lang.trade18}}：</text>
						<view class="font-14 font-grey newlines">{{app._toFixed(app._accMul(app._accMul(ctcInfo.coin_price,7),sellInfo.Number),2)}} CNY</view>
					</view>
					<view class="flex-row flex-j-between lh-20 mb-15">
						<text class="font-14 one-row mr-15 name">{{lang.trade19}}：</text>
						<view class="font-14 font-grey newlines">{{app._accMul(userInfo.Fee,100)}} %</view>
					</view>
					<view class="w-1005 bt_line"></view>
					<view class="font-14 font-grey op-7 pt-15">{{lang.trade20}}</view>
					<view class="cheques flex-center pt-15">
						<view class="w-33 flex-center font-14 font-grey" v-if="sellInfo.IsAlipay==1" :class="{'active':pay=='alipay'}" @click="pay='alipay'">
							<text class="icon iconfont icon-iconfontcheck"></text>{{lang.trade21}}
						</view>
						<view class="w-33 flex-center font-14 font-grey" v-if="sellInfo.IsAddress==1" :class="{'active':pay=='isusdt'}" @click="pay='isusdt'">
							<text class="icon iconfont icon-iconfontcheck"></text>USDT
						</view>
					</view>
					<view class="flex-center flex-j-between pt-15 pb-10">
						<input type="password" :placeholder="lang.trade22" v-model="pass" />
						<text class="font-12 font-main one-row pl-15" @click="app.showOpen('login/forgetPay')">{{lang.trade23}}</text>
					</view>
					
					<view class="flex-center flex-j-between pt-15 pb-30">
						<input type="text" :placeholder="lang.trade24" v-model="code" />
						<view class="one-row pl-15">
							<button class="get-code-btn" :disabled="!code_status" v-if="!CodeSW" @click="sendCode()">{{lang.trade25}}</button>
							<button class="get-code-btn" disabled v-else>{{second}}S{{lang.fgp4}}</button>
						</view>
					</view>
					<view class="w-100 pb-30">
						<button class="btn" :disabled="!sell_status" @click="sell()">{{lang.trade26}}</button>
					</view>
				</view>
			</view>
		</view>
		<view class="winPopup" v-if="rewardSW">
			<reward :show="rewardSW" :rewardSW.sync="rewardSW"></reward>
		</view>
		<view class="winPopup" v-if="authSW">
			<authResult :show="authSW" :authSW.sync="authSW"></authResult>
		</view>
	</view>
</template>

<script>
	var DifferenceHour = -1
	var DifferenceMinute = -1
	var DifferenceSecond = -1
	// import tabBar from "@/components/tabBar/tabBar.vue";
	import config from "@/common/js/config.js";
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	import reward from "@/components/reward.vue";
	import authResult from "@/components/authResult.vue";
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		components: {
			// tabBar,
			uniLoadMore,
			reward,
			authResult
		},
		data() {
			return {
				RuleInfo:"------",
				rule:false,
				ctcInfo:{
					coin_price:"--",
					buy_amount:"--",
					tx_amount:"--",
					fee:"",
				},
				typeList:[],
				page:1,
				count:20,
				list:[],
				loadingType: 'more',
				nav:"-1",
				notice:[],
				sellInfo:"",
				sellSW:false,
				pay:"",
				pass:"",
				code:"",
				second:60,//倒计时
				CodeSW:false,//验证码开关
				code_status:true,
				sell_status:true,
				timeInfo:{
					start_time:"",
					end_time:"",
				},
				loadSW:true,
				rewardSW:false,
				authSW:false,
				timeSW:true,
				Tday:"",
				downTime:"",
			}
		},
		onShow() {
			var self=this;
			self.setTabBar();
			uni.setStorageSync("pop",1);
			let showRule=uni.getStorageSync("showRule");
			if(showRule){
				self.getRule();
			};
			self.getData();
			self.initlist();
			self.getNotice();
			self.getReward();
			if(self.RewardInfo.is_giveAway==1&&self.userInfo.auth_status==3){
				self.rewardSW=true;
			}else{
				self.rewardSW=false;
			};
		},
		onLoad() {
			let self=this;
		},
		onPullDownRefresh() {
			var self=this;
			self.getData();
			self.initlist();
			self.getNotice();
			self.setUserInfo();
			setTimeout(function () {
				uni.stopPullDownRefresh();
			}, 1000);
		},
		onReachBottom(){
			var self = this;
			self.getList();
		},
		computed:{
			...mapState(['userInfo','qiniu','RewardInfo','lang']),
		},
		methods: {
			...mapMutations(["setUserInfo","logout","getReward"]),
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
			goBuy:function(){
				var self=this;
				if(self.userInfo.IsFrozenCTC==1){
					setTimeout(function(){
						self.app.showOpen('trade/frozen');
					},500);
					return self.app._toast(self.lang.trade27);
				};
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
					return self.app._toast(self.lang.trade28);
				};
				// if(self.userInfo.IsSetPayPass!=1){
				// 	setTimeout(function(){
				// 		self.app.showOpen('user/userInfo');
				// 	},500);
				// 	return self.app._toast("请先去设置交易密码");
				// };
				// if(self.userInfo.IsBindAddress!=1||self.userInfo.IsBindAlipay!=1){
				// 	setTimeout(function(){
				// 		self.app.showOpen('user/userInfo');
				// 	},500);
				// 	return self.app._toast("请先去绑定收款钱包或者绑定支付宝");
				// };
				self.app.showOpen('trade/buy?price='+self.ctcInfo.coin_price)
			},
			goSell:function(item){
				var self=this;
				if(self.userInfo.IsFrozenCTC==1){
					setTimeout(function(){
						self.app.showOpen('trade/frozen');
					},500);
					return self.app._toast(self.lang.trade27);
				};
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
					return self.app._toast(self.lang.trade28);
				};
				if(self.userInfo.IsSetPayPass!=1){
					setTimeout(function(){
						self.app.showOpen('login/setPay');
					},500);
					return self.app._toast(self.lang.trade29);
				};
				if(item.IsAlipay==1){
					if(self.userInfo.IsBindAlipay!=1){
						setTimeout(function(){
							self.app.showOpen('user/alipay');
						},500);
						return self.app._toast(self.lang.trade30);
					};
				}
				if(item.IsAddress==1){
					if(self.userInfo.IsBindAddress!=1){
						setTimeout(function(){
							self.app.showOpen('user/address');
						},500);
						return self.app._toast(self.lang.trade31);
					};
				}
				self.sellInfo=item;
				self.sellSW=true;
			},
			sell:function(){
				var self=this;
				if(self.pay==''){
					return self.app._toast(self.lang.trade32);
				};
				if(self.code.trim().length==0){
					return self.app._toast(self.lang.trade33);
				};
				if(self.pass.trim().length==0){
					return self.app._toast(self.lang.trade34);
				};
				self.sell_status=false;
				let send={
					Id:self.sellInfo.Id,
					Number:self.sellInfo.Number,
					IsAddress:self.pay=='isusdt'?true:false,
					IsWechat:false,
					IsAlipay:self.pay=='alipay'?true:false,
					PayPassword:self.pass,
					AuthCode:self.code,
				};
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
							self.getData();
							self.initlist();
							self.sellSW=false;
							self.pass="";
							self.code="";
						}else{
							self.app._toast(res.data.message);
							console.log(JSON.stringify(res));
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						setTimeout(function(){
							self.sell_status=true;
						},1000);
					}
				});
			},
			sendCode:function() {//获取验证码
				var self=this;
				let send={
					mobile:self.mobile
				};
				self.code_status=false;
				let url=config.api + "/sms-vcode";
				uni.request({
					url: url,
					data: {},
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						if(res.data.status==1){
							self.app._toastIcon(self.lang.fgp9);
							self.movetime();
						}else{
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
						self.app._toast("发送失败!");
					},
					complete: (res) => {
						setTimeout(function(){
							self.code_status=true;
						},500);
					}
				});
			},
			movetime: function() {//倒计时几秒发送验证码的函数
				var self = this;
				self.CodeSW = true;
				var interval = setInterval(function() {
					if(self.second != 0) {
						self.second--;
					} else {
						clearInterval(interval);
						self.CodeSW = false;
						self.second = 60;
					};
				}, 1000);
			},
			readRule:function(){
				var self=this;
				self.rule=false;
				uni.setStorageSync("showRule",false);
			},
			getRule:function(){
				var self=this;
				let url=config.api + "/article-list";
				uni.request({
					url: url,
					data: {
						CallIndex:"tx_rule"
					},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.rule=true;
							self.RuleInfo=res.data.data[0].ArticleDetails;
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {}
				});
			},
			getNotice:function(){
				var self=this;
				let url=config.api + "/article-list";
				uni.request({
					url: url,
					data: {
						CallIndex:"notice",
					},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						if(res.data.status==1){
							self.notice=res.data.data;
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
			getData:function(){
				var self=this;
				let url=config.api + "/get.ctc.info";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.typeList=res.data.data.filter;
							self.ctcInfo={
								coin_price:res.data.data.coin_price,
								buy_amount:res.data.data.buy_amount,
								tx_amount:res.data.data.tx_amount,
								fee:res.data.data.fee,
							};
							// self.initlist();
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {}
				});
			},
			initlist:function(){
				var self=this;
				self.page=1;
				self.list=[];
				self.loadingType="more";
				self.getList();
			},
			getList:function(){
				var self=this;
				if(self.loadingType === 'nomore'){
					return;
				}else{
					self.loadingType = 'loading';
				};
				let send={
					page:self.page,
					count:self.count,
					filter:self.nav=='-1' ? ' ' : self.typeList[self.nav],
				};
				// console.log(send);
				let url=config.api + "/ctc-list";
				if(!self.loadSW){
					return;
				};
				self.loadSW=false;
				uni.request({
					url: url,
					data: send,
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.timeInfo=res.data.data;
							if(!res.data.data.data){
								self.loadingType = 'nomore';
								self.timeSW=false;
								return;
							};
							self.countdown();
							self.timeSW=true;
							if(self.page==1){
								self.list=[];
							};
							for(var i=0;i<res.data.data.data.list.length;i++){
								var item=res.data.data.data.list[i];
								self.list.push(item);
							};
							if(res.data.data.data.list.length<self.count){
								self.loadingType = 'nomore';
							}else{
								self.loadingType = 'more';
							};
							// self.loadSW=true;
							self.page++;
						}else{
							self.loadingType = 'nomore';
							console.log(JSON.stringify(res));
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						self.loadSW=true;
					}
				});
			},
			countdown:function(){
				let self=this;
				let ed=Number(self.timeInfo.end_time)+1;
				var date = new Date();
				clearInterval(self.downTime);
				self.downTime=setInterval(function(){
					self.leftTimer(date.getFullYear(), (date.getMonth() + 1) ,date.getDate(),ed,"00","00")
				},1000);
			},
			leftTimer:function(year,month,day,hour,minute,second){
				let self=this;
				var leftTime = (new Date(year,month-1,day,hour,minute,second)) - (new Date()); //计算剩余的毫秒数
				var days = parseInt(leftTime / 1000 / 60 / 60 / 24 , 10); //计算剩余的天数 
				var hours = parseInt(leftTime / 1000 / 60 / 60 % 24 , 10); //计算剩余的小时 
				var minutes = parseInt(leftTime / 1000 / 60 % 60, 10);//计算剩余的分钟 
				var seconds = parseInt(leftTime / 1000 % 60, 10);//计算剩余的秒数 
				days = self.checkTime(days); 
				hours = self.checkTime(hours); 
				minutes = self.checkTime(minutes); 
				seconds = self.checkTime(seconds); 
				// self.Tday=days+"天" + hours+"小时" + minutes+"分"+seconds+"秒";
				self.Tday=hours+":" + minutes+":"+seconds;
				if(days=="00"&&hours=="00"&&minutes=="00"&&seconds=="00"){
					clearInterval(self.downTime);
					self.initlist();
				};
				// self.Tday=days+"-" + hours+"-" + minutes+"-"+seconds
			},
			checkTime:function(i){
				if(i<10)
				{ 
				i = "0" + i; 
				} 
				return i; 
			}
			
			  
		},
		watch: {
			hasLogin: function(newValue, oldValue) {
				var self=this;
				if(newValue){
					let showRule=uni.getStorageSync("showRule");
					if(showRule){
						self.getRule();
					};
					self.getData();
					// self.getList();
					self.getNotice();
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
	page{background-color: #F7F7F7;}
	.box{padding-bottom: 60px;}
	.top-data.active{width: 100%;background: url(../../static/img/652d5d4fd3f7f6060a04415a1dba9bc.png) no-repeat;background-size: 100% 100%;position: relative;padding-top: 60px;}
	.top-data{width: 100%;background: url(../../static/img/367aab38b1409d001e2f3a19b4fa4a9.png) no-repeat;background-size: 100% 100%;position: relative;padding-top: 60px;}
	.top-data .notice-cont{width: 100%;height: 40px;background-color: rgba(60,60,60,0.2);}
	.top-data .notice-cont image{width: 14px;height: 13px;}
	.top-data .notice-cont .notice{width: calc(100% - 165px);height: 100%;line-height: 40px;font-size: 12px;}
	.Tday{width: 165px;overflow: hidden;text-align: right;}
	.link-btn{padding-top: 12px;}
	.link-btn button{height: 40px;width: 30%;border-radius: 3px;background: linear-gradient(bottom,#FA6C00,#FFB500);color: #FFFFFF;font-size: 16px;}
	.link-btn button[disabled]{background: #D8D8D8 !important;}
	.choice-price{width: 100%;overflow-x: scroll;padding: 12px 0px;}
	.choice-price .ch-pri{width: 25%;text-align: center;display: inline-block;border-right: 1px solid #FA6C00;height: 15px;line-height: 15px;}
	.choice-price .ch-pri:last-child{border-right: 0px;}
	.choice-price .active{color: #FA6C00 !important;}
	.cont-list{width: 100%;padding: 9px 12px 12px;box-shadow: 0px 2px 4px 2px #BABCC0;}
	.cont-list .user-Info image.Avatar{width: 24px;height: 24px;border-radius: 50%;}
	.cont-list .btn-cont{width: 100%;}
	.cont-list .btn-cont .cont{width: calc(100% - 100px);}
	.cont-list image.py{width: 14px;height: 14px;display: block;margin-right: 8px;margin-bottom: 4px;}
	.cont-list .btn-cont button.btn{width: 80px;height: 28px;line-height: 28px;background: linear-gradient(bottom,#FA6C00,#FFB500);border-radius: 3px;}
	.sell-content{}
	.sell-content .title{width: 100%;padding: 12px 0px;position: relative;}
	.sell-content .title .sell-popup-close{height: 100%;position: absolute;top: 0px;right: 0px;padding-right: 20px;}
	.sell-content .title .sell-popup-close .iconfont{font-size: 22px;color: #3C3C3C;}
	.sell-content .name{color: #8C8C8C;}
	.sell-content input{background-color: #F9F9F9;border-radius: 4px;padding: 0px 15px;font-size: 14px;color: #333333;height: 40px;width: calc(100% - 90px);}
	.sell-content button.btn{background: linear-gradient(bottom,#FA6C00,#FFB500);height: 44px;line-height: 44px;}
	.sell-content button.btn[disabled]{background: #D8D8D8 !important;}
	.sell-content .cheques{width: 100%;}
	.sell-content .cheques .icon{width: 15px;height: 15px;margin-right: 5px;border-radius: 50%;display: inline-flex;align-items: center;align-content: center;justify-content: center;border: 1px solid #979797;color: #FFFFFF;font-size: 12px;}
	.sell-content .cheques .active .icon{background: linear-gradient(bottom,#FA6C00,#FFB500);border: 0px;} 
</style>
