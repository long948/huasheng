<template>
	<view class="box">
		<view class="w-100" v-if="data">
			<view class="loop-Img">
				<swiper class="swiper" :autoplay="true" :circular="true" :interval="5000" :duration="1000">
					<swiper-item v-for="(item,index) in data.carousel_img" :key="index">
						<image class="swiper-item" :src="item"></image>
					</swiper-item>
				</swiper>
			</view>
			<view class="w-100 pt-17 pb-17 pl-16 pr-16 flex-center flex-j-between font-white price-cont">
				<view class="flex-row flex-end nowrap font-w-b">
					<text class="font-16" v-text="data.active.team_price">100</text>
					<text class="font-12" v-text="data.active.payCoinName">USDT</text>
				</view>
				<view class="flex-center">
					<view class="progress-cont flex-center">
						<!-- <view class="val" :style="{'width':app._accMul((data.proportion,100),100)+'%'}"></view> -->
						<!-- <view class="val" :style="{'width':app._accMul(app._accDiv(data.active.store_count,(Number(data.active.sales_sum)+Number(data.active.virtual_num))) ,100)+'%'}"></view> -->
						<view class="val" :style="{'width':prosswidth+'%'}"></view>
					</view>
					<text class="font-10 ml-12">已拼{{(Number(data.active.sales_sum)+Number(data.active.virtual_num))}}件</text>
				</view>
			</view>
			<view class="w-100 bc-white mb-12">
				<view class="w-100 font-15 font-c-3 newlines lh-20 title-cont pt-15 pb-15 pl-16 pr-16">
					<view class="btn font-10 font-white br-2 one-row mr-5">{{data.active.needer}}中{{data.active.stock_limit}}</view>
					{{data.goods_name}}
				</view>
				<view class="w-100 pt-12 pb-12 pl-15 pr-15 flex-row flex-j-between">
					<view class="newlines font-12 newlines pr-10">
						限时抢购-未中奖可获得补偿金-试运营阶段推荐有奖
					</view>
					<i class="iconfont icon-wentizhenggai font-c-9 font-16 pl-10" @click="ruleSW=true"></i>
				</view>
			</view>
			<view class="w-100 newlines bc-white pl-15 pr-15 mb-12">
				<view class="w-100 flex-center flex-j-between pt-12">
					<text class="font-14 font-c-3">拼团列表</text>
					<view class="flex-center font-c-9 font-10" v-if="data.found.length" @click="teamSW=true">
						查看更多<i class="iconfont icon-icon-test10 font-16"></i>
					</view>
				</view>
				<view class="w-100 pt-12 pb-25 font-c-9 font-10 text-center" v-if="!data.found.length">
					<image src="../../static/img/72865c45d513a2731d12ad154e32e8c.png" style="height: 120px;width: 120px;"></image>
					<view>暂无人拼团</view>
				</view>
				<view class="w-100 flex-center flex-j-between bt_line pt-15 pb-15" v-for="(jtem,Jndex) in data.found" :key="Jndex">
					<view class="flex-center team-cont">
						<view class="member flex-center flex-j-center" v-for="(item,index) in jtem.peoples" :key="index" :style="{'right':7*index+'px'}" v-if="index<5">
							<image :src="item.user.Avatar?item.user.Avatar:'../../static/img/bacc1bf65f6c0e8ed8c29f22df3743b.png'"></image>
						</view>
						<view class="member flex-center flex-j-center font-c-9" :style="{'right':7*jtem.peoples.length+'px','background-color':'#F1F1F1'}" v-if="jtem.peoples.length>5">
							<i class="iconfont icon-gengduo3 font-18 lh-23"></i>
						</view>
					</view>
					<view class="flex-center">
						<view>
							<view class="font-c-3 font-12">还差<text class="font-dark-main">{{data.active.needer-jtem.peoples.length}}人</text>拼成</view>
							<view class="font-c-6 font-10" v-if="jtem.time!='已截止'">剩余{{(jtem.time)}}</view>
							<view class="font-c-6 font-10" v-else>已截止</view>
						</view>
						<view class="go-pd br-4 font-14 font-white lh-29 pl-8 pr-8 ml-12" @click="goCT(jtem)" v-if="jtem.time!='已截止'">前往拼单</view>
						<view class="go-pd br-4 font-14 font-white lh-29 pl-8 pr-8 ml-12" v-else>已截止</view>
					</view>
				</view>
			</view>
			<view class="w-100 newlines bc-white pt-16 pb-16 pl-15 pr-15 font-12 font-c-9 mb-12">
				拼团{{data.active.needer}}人，{{data.active.stock_limit}}人中签，未中签用户可获得补偿金{{Number(data.active.return_amount)+Number(data.active.team_price)}}{{data.active.payCoinName}}
			</view>
			<view class="w-100 bc-white">
				<view class="w-100 font-14 font-c-3 pt-15 pb-15 pl-16 pr-16">商品详情</view>
				<view class="w-100 newlines font-c-3 details pl-10 pr-10" v-html="data.goods_content">
					
				</view>
			</view>
			<view class="w-100" style="height: 60px;"></view>
			<view class="w-100 flex-center flex-j-between" style="position: fixed;bottom: 0px;left: 0px;height: 50px;">
				<view class="flex-center flex-wrap font-white" @click="goKT()" style="width: 130px;height: 100%;background: linear-gradient(#FFBE1E,#FF940B);">
					<view class="w-100 flex-row flex-end flex-j-center font-15 nowrap">
						{{data.active.team_price}}
						<text class="font-12" v-text="data.active.payCoinName">USDT</text>
					</view>
					<view class="w-100 text-center font-14">发起拼团</view>
				</view>
				<view class="flex-center flex-wrap font-white" @click="teamSW=true" style="width: calc(100% - 130px);background: linear-gradient(#FFB500,#FA6C00);height: 100%;">
					<view class="w-100 flex-row flex-end flex-j-center font-15 nowrap">
						{{data.active.team_price}}
						<text class="font-12" v-text="data.active.payCoinName">USDT</text>
					</view>
					<view class="w-100 text-center font-14">参与拼团</view>
				</view>
			</view>
		</view>
		<!-- 规则说明 -->
		<view class="winPopup w-100 flex-center flex-j-center pl-30 pr-30" v-if="ruleSW" @click="ruleSW=false">
			<view class="w-100 br-4 bc-white pl-24 pr-24 pt-24 pb-24" @click.stop="">
				<view class="w-100 text-center font-16 font-w-b font-w-b pb-20">规则说明</view>
				<view class="w-100 ruleInfo" style="max-height: 50vh;overflow-y: scroll;" v-html="ruleInfo">
					<view class="w-100 font-c-3 font-12 newlines lh-20">1、任何人均可发起拼图。发起拼图时需支付等值资金，发起人将成为第一个参与用户。</view>
					<view class="w-100 font-c-3 font-12 newlines lh-20">2、邀请用户参与拼图后，可获得支付金额1%的推荐奖。</view>
					<view class="w-100 font-c-3 font-12 newlines lh-20">3、再过定时间内，未完成拼团时，资金原路返回。</view>
					<view class="w-100 font-c-3 font-12 newlines lh-20">4、未中奖用户，出退还本金外，还将获得支付金额2%的补偿奖励。</view>
				</view>
				<view class="w-100 flex-center flex-j-center pt-20 pb-10">
					<button class="btn" @click="ruleSW=false">确定</button>
				</view>
			</view>
		</view>
		<!-- 选择拼团 -->
		<view class="winPopup w-100 flex-row flex-end flex-j-center" v-if="teamSW">
			<view class="w-100 pl-10 pr-10 pb-16" style="background-color: #F8F6F7;border-radius: 10px 10px 0px 0px;">
				<view class="w-100 pt-27 pb-27 flex-center flex-j-between font-16 font-w-b font-c-3">
					<i></i>选择拼团<i class="iconfont icon-icon-test6 font-20 font-c-9" @click="teamSW=false"></i>
				</view>
				<view class="w-100 bc-white br-4 pl-12 pr-12" style="max-height: 70vh;overflow-y: auto;">
					<view class="w-100" v-for="(jtem,Jndex) in data.found" :key="Jndex">
						<view class="w-100 flex-center flex-j-between bt-line pt-15 pb-15" @click="jtem.sw=!jtem.sw">
							<view class="flex-center team-cont">
								<view class="member flex-center flex-j-center" v-for="(item,index) in jtem.peoples" :key="index" :style="{'right':7*index+'px'}" v-if="index<5">
									<image :src="item.user.Avatar?item.user.Avatar:'../../static/img/bacc1bf65f6c0e8ed8c29f22df3743b.png'"></image>
								</view>
								<view class="member flex-center flex-j-center font-c-9" :style="{'right':7*jtem.peoples.length+'px','background-color':'#F1F1F1'}" v-if="jtem.peoples.length>5">
									<i class="iconfont icon-gengduo3 font-18 lh-23"></i>
								</view>
							</view>
							<view class="flex-center">
								<view>
									<view class="font-c-3 font-12">还差<text class="font-dark-main">{{data.active.needer-jtem.peoples.length}}人</text>拼成</view>
									<view class="font-c-6 font-10" v-text="'剩余'+(jtem.time)">剩余--</view>
								</view>
								<view class="go-pd br-4 font-14 font-white lh-29 pl-8 pr-8 ml-12" @click="goCT(jtem)" v-if="jtem.time!='已截止'">前往拼单</view>
								<view class="go-pd br-4 font-14 font-white lh-29 pl-8 pr-8 ml-12" v-else>已截止</view>
							</view>
						</view>
						<view class="w-100 flwex-center flex-j-between bt-line pt-11 pb-11" v-if="jtem.sw">
							<text class="font-c-9 font-10">发起人：</text>
							<text class="font-c-9 font-10" v-text="jtem.teamBoss.user.NickName">12345698712</text>
						</view>
					</view>
					<view class="w-100 pt-15 pb-15 text-center font-c-9" v-if="!data.found.length">暂无人开团</view>
				</view>
			</view>
		</view>
		<!-- 弹窗 -->
		<view class="winPopup w-100 flex-center flex-j-center pl-30 pr-30" v-if="pops.sw">
			<view class="w-100 br-4 bc-white pl-24 pr-24 pb-24">
				<i class="iconfont icon-butongyi close" @click="pops.sw=false"></i>
				<view class="font-c-3 font-w-b font-18 pt-25 pb-50 text-center">温馨提示</view>
				<view class="w-100 text-center font-c-3 font-11 font-14 pb-60 newlines">{{pops.text}}</view>
				<view class="w-100">
					<button class="btn" @click="goPop()">{{pops.btn}}</button>
				</view>
			</view>	
		</view>
		<!-- 支付弹窗 -->
		<view class="winPopup w-100 flex-center flex-j-center pl-30 pr-30" v-if="paySW">
			<view class="w-100 br-4 bc-white pl-24 pr-24 pb-24">
				<view class="w-100 pt-27 pb-27 flex-center flex-j-between font-16 font-w-b font-c-3">
					<i></i>支付<i class="iconfont icon-icon-test6 font-20 font-c-9" @click="paySW=false"></i>
				</view>
				<view class="w-100 font-12 font-c-3 newlines" v-if="us=='ct'">正在参与由{{us=='ct'?ctInfo.teamBoss.user.NickName:userInfo.NickName}}发起的拼购活动，需支付{{data.active.team_price}} {{data.active.payCoinName}}。</view>
				<view class="w-100 font-12 font-c-3 newlines" v-if="us=='kt'">正在发起拼购活动，需支付{{data.active.team_price}} {{data.active.payCoinName}}。</view>
				<view class="w-100 flex-center flex-j-between pt-70 pb-16">
					<text class="font-c-6 font-12">可用余额：</text>
					<text class="font-c-6 font-12">{{order.user.Money}} {{data.active.payCoinName}}</text>
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
	import uniCountdown from "@/components/uni-countdown/uni-countdown.vue";
	import {mapState,mapMutations} from 'vuex'
	
	export default {
		components: {
			loader,
			uniCountdown,
		},
		data() {
			return {
				loader:false,
				ruleSW:false,
				teamSW:false,
				data:{
					active:{
						team_price:"--",
						team_price:"--",
						payCoinName:"--",
						needer:"--",
						stock_limit:"--",
						return_amount:"--",
						virtual_num:0,
					},
					found:[],
					sales_sum:0,
				},
				paySW:false,
				pass:"",
				order:"",
				us:"",
				ctInfo:"",
				ruleInfo:"",
				nw:"",
				pops:{
					sw:false,
					text:"",
					btn:"",
					type:0,//1,开团成功，2，参团成功，4支付成功
				}
			}
		},
		onLoad(e) {
			let self=this;
			let data=JSON.parse(e.data);
			this.getDetails(data);
			this.getRule();
			this.nw=new Date().getTime();
			setInterval(function(){
				self.nw=new Date().getTime();
				self.data.found.forEach(function(item){
					self.getBtTime(item);
				})
			},1000);
		},
		computed:{
			...mapState(['userInfo','assets']),
			prosswidth:function(){
				let self=this;
				let ad=Number(self.data.active.virtual_num) + Number(self.data.active.sales_sum) + Number(self.data.active.store_count);
				var data=self.app._accDiv(self.data.active.store_count,ad);
				data=self.app._accMul(data,100)
				return data;
			},
		},
		methods: {
			goPop:function(){
				let self=this;
				if(self.pops.type==1 || self.pops.type==2 || self.pops.type==3){//参团成功
					self.paySW=true;
				}else if(self.pops.type==4){
					uni.redirectTo({
						url:"order-details?data="+JSON.stringify(self.order)
					});
				}
				self.pops.sw=false;
			},
			getBtTime:function(item){
				var that = this;
				// var date = new Date();
				// var now = date.getTime();
				// var endDate = new Date(that.data.endDate2); //设置截止时间
				// var end = endDate.getTime();
				
				// var end = ((item.found_end_time*1000)+(24*60*60*1000));
				var end = ((item.found_end_time*1000));
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
					item.time=res;
					return h + ":" + m + ":" + s
					//递归每秒调用countTime方法，显示动态时间效果
				} else {
					item.time='已截止';
					return '已截止'
					// return h + ":" + m + ":" + s
				}
			},
			getRule:function(){
				var self=this;
				self.loader=true;
				let url=config.api + "/get.shop.rule";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						// console.log(res);
						if(res.data.status==1){
							self.ruleInfo=res.data.data;
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
			goCT:function(e){
				var self=this;
				self.us="ct";
				self.loader=true;
				self.ctInfo=e;
				let url=config.api + "/shop.join.activity";
				let send={
					foundId:e.found_id,
				}
				uni.request({
					url: url,
					data: send,
					method: "POST",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						console.log(res);
						config.api_status(res);
						if(res.data.status==1){
							self.order=res.data.data;
							self.teamSW=false;
							self.paySW=true;
							// let pp_={
							// 	sw:true,
							// 	text:"您已参团成功,请前往支付!",
							// 	btn:"前往支付",
							// 	type:2,
							// }
							// self.pops=pp_;
						}else{
							let pp_={
								sw:true,
							};
							if(res.data.status==50007){
								pp_.text="你已参团成功，请前往支付！";
								pp_.btn="前往支付";
								pp_.type=3;
								self.pops=pp_;
								self.order=res.data.data;
							}else if(res.data.status==50007){
								pp_.text="你已参团成功，等待成团中！";
								pp_.btn="邀请好友";
								pp_.type=4;
								self.pops=pp_;
								self.order=res.data.data;
							}else{
								self.app._toast(res.data.message);
							}
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
				if(Number(self.order.user.Money) < Number(self.data.active.team_price)){
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
				console.log(send);
				uni.request({
					url: url,
					data: send,
					method: "POST",
					header: {Authorization: config.getToken()},
					success: res => {
						config.api_status(res);
						if(res.data.status==1){
							self.app._toast(res.data.message);
							self.paySW=false;
							try{
								self.order.follow_id=self.order.orderSn;
								console.log(self.order);
								// let pp_={
								// 	sw:true,
								// 	text:self.pops.type==1?"当前活动,您已开团成功,等待成团中！":"您已参团成功,等待成团中！",
								// 	btn:"邀请好友",
								// 	type:4,
								// }
								// self.pops=pp_;
								
								uni.redirectTo({
									url:"order-details?data="+JSON.stringify(self.order)
								});
							}catch(e){
								console.log(e);
							}
						}else{
							self.paySW=false;
							// let pp_={
							// 	sw:true,
							// 	text:"--",
							// 	btn:"确定",
							// 	type:-1,
							// }
							// console.log(res.data.status);
							// if(res.data.status==50001){
							// 	pp_.text="团已结束，请重新开团"
							// 	self.pops=pp_;
							// }else if(res.data.status==50002){
							// 	pp_.text="拼购活动不存在"
							// 	self.pops=pp_;
							// }else if(res.data.status==50003){
							// 	pp_.text="拼购商品不存在"
							// 	self.pops=pp_;
							// }else if(res.data.status==50004){
							// 	pp_.text="您已参加过该团了"
							// 	self.pops=pp_;
							// }else if(res.data.status==50005){
							// 	pp_.text="您不能参加自己开的团"
							// 	self.pops=pp_;
							// }else if(res.data.status==50006){
							// 	pp_.text="活动已结束"
							// 	self.pops=pp_;
							// }else if(res.data.status==50007){
							// 	pp_.text="拼购订单不存在"
							// 	self.pops=pp_;
							// }else if(res.data.status==50008){
							// 	pp_.text="该订单已支付"
							// 	self.pops=pp_;
							// }else if(res.data.status==50009){
							// 	pp_.text="订单超时 不能支付"
							// 	self.pops=pp_;
							// }else{
							// 	self.app._toast(res.data.message);
							// }
							self.app._toast(res.data.message);
							console.log(JSON.stringify(res));
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
			goKT:function(){//开团，直接购买
				var self=this;
				self.loader=true;
				self.us="kt";
				let url=config.api + "/shop.open.activity";
				let send={
					goodId:self.data.goods_id,
					spikeId:self.data.active.spikeId,
					activityId:self.data.active.activityId,
				}
				// console.log(send);
				uni.request({
					url: url,
					data: send,
					method: "POST",
					header: {Authorization: config.getToken()},
					success: res => {
						console.log(res);
						config.api_status(res);
						if(res.data.status==1){
							self.order=res.data.data;
							self.paySW=true;
							// let pp_={
							// 	sw:true,
							// 	text:"当前活动,您已开团成功,请前往支付!",
							// 	btn:"前往支付",
							// 	type:1,
							// }
							// self.pops=pp_;
						}else{
							let pp_={
								sw:true,
							};
							if(res.data.status==50007){
								pp_.text="当前活动，你已开团成功，请前往支付！";
								pp_.btn="前往支付";
								pp_.type=3;
								self.pops=pp_;
								self.order=res.data.data;
							}else if(res.data.status==50008){
								pp_.text="当前活动，你已开团成功，等待成团中！";
								pp_.btn="邀请好友";
								pp_.type=4;
								self.pops=pp_;
								self.order=res.data.data;
							}else{
								self.app._toast(res.data.message);
							}
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
			getDetails:function(e){
				var self=this;
				self.loader=true;
				let url=config.api + "/get.shop.details";
				uni.request({
					url: url,
					data: {
						goodId:e.goods_id,
						spikeId:e.active.spikeId,
					},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						console.log(res);
						config.api_status(res);
						if(res.data.status==1){
							res.data.data.found.forEach(function(item){
								let people=item.peoples.filter(function(jtem){
									return jtem.user.userIsFound;
								})[0];
								item.sw=false;
								item.time="";
								item.teamBoss=people;
							});
							self.data=res.data.data;
							console.log(self.data);
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
	.swiper,
	.swiper-item{
		width: 100%;height: 300px;
	}
	.price-cont{
		background: linear-gradient(bottom,#FA6C00,#FFB500);
	}
	.title-cont{
		border-bottom: 1px solid #F7F7F7;
		.btn{
			display: inline-block;
			line-height: 18px;padding: 0px 5px;
			background: linear-gradient(bottom,#FA6C00,#FFB500);margin-bottom: 2px;
		}
	}
	.team-cont{
		.member{
			width: 23px;height: 23px;line-height: 23px;border-radius: 50%;right: 11px;background-color: #F1F3F4;
			image{
				width: 100%;height: 100%;border-radius: 50%;
			}
		}
	}
	.go-pd{
		background: linear-gradient(bottom,#FA6C00,#FFB500);margin-bottom: 2px;
	}
	.details img{
		width: 100%;
	}
	.bt-line{
		border-bottom: 1px solid #FAFAFA;
	}
	.pay-input{
		width: 100%;height: 40px;background-color: #F7F7F7;font-size: 14px;color: #333333;
	}
	.ruleInfo img{
		width: 100%;
	}
	.progress-cont{
		width: 120px;height: 10px;border-radius: 5px;background-color: #FFFFFF;overflow: hidden;
		.val{
			height: 10px;border-radius: 5px;background-color: #FFCE89;
		}
	}
	.close{
		position: absolute;top: -30px;right: 0px;color: #FFFFFF;font-size: 25px;font-weight: bold;
	}
</style>
