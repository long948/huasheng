<template>
	<view class="box">
		<view class="w-100" v-if="data">
			<!-- 0全部 1待付款  2待成团 3已完成 4 已取消 -->
			<view class="w-100 pt-26 pb-26 pl-16 pr-16 flex-center flex-j-between font-white order-time" v-if="data.status==1">
				<view class="flex-center nowrap font-14">
					<image style="width: 15px;height: 15px;" class="mr-10" src="../../static/img/ff917a552a00ee1c66bb1bb7a2982c0.png"></image>
					距订单关闭{{getBtTime(data,'zf')}}
				</view>	
				<view class="one-row font-14">待付款</view>
			</view>
			<view class="w-100 pt-26 pb-26 pl-16 pr-16 flex-center flex-j-between font-white order-time" v-if="data.status==2 && !data.ctInfo">
				<view class="flex-center nowrap font-14">
					<image style="width: 15px;height: 15px;" class="mr-10" src="../../static/img/ff917a552a00ee1c66bb1bb7a2982c0.png"></image>
					距成团时间还有{{getBtTime(data,'ct')}}
				</view>	
				<view class="one-row font-14">待成团</view>
			</view>
			<view class="w-100 pt-12 pl-16 pr-16">
				<view class="w-100 bc-white br-4 pl-12 pr-12 pb-15 mb-16 cont-list">
					<view class="w-100 flex-center flex-j-between pt-13 pb-12">
						<!-- 待付款|待成团 -->
						<view class="flex-center nowrap" v-if="data.status==1 || data.status==2">
							<image class="status mr-8" src="../../static/img/b1d1b3297c737adbd7a604d322885cc.png"></image>
							<text class="font-12 font-c-3 nowrap" v-if="data.status==1">距订单关闭还有{{getBtTime(data,'zf')}}</text>
							<text class="font-12 font-c-3 nowrap" v-if="data.status==2">
								<block v-if="data.ctInfo">
									结算中
								</block>
								<block v-else>
									距成团截止还有{{getBtTime(data,'ct')}}
								</block>
							</text>
						</view>
						<!-- 已完成 -->
						<view class="flex-center nowrap" v-else-if="data.status==3 && !data.is_lock_draw">
							<image class="status mr-8" src="../../static/img/5c18f82064ca192e2b5348a107c50b7.png"></image>
							<text class="font-12 font-c-3 nowrap">很遗憾，您未中签</text>
						</view>
						<view class="flex-center nowrap" v-else-if="data.status==3 && data.is_lock_draw">
							<image class="status mr-8" src="../../static/img/f613cbd5966ee300b6e84bc39ed5749.png"></image>
							<text class="font-12 font-c-3 nowrap">恭喜中签！</text>
						</view>
						<!-- 已取消 -->
						<view class="flex-center nowrap" v-else-if="data.status==4">
							<image class="status mr-8" src="../../static/img/13c0d368b42f685a1a3682160b63259.png"></image>
							<text class="font-12 font-c-3 nowrap">订单已取消</text>
							<!-- <text class="font-12 font-c-3 nowrap">支付超时，订单已取消</text> -->
							<!-- <text class="font-12 font-c-3 nowrap">未能成团，订单已取消</text> -->
						</view>
						<!-- 右边提示的状态 -->
						<view class="one-row status font-12" v-if="data.status==1">待付款</view>
						<view class="one-row status font-12" v-else-if="data.status==2">{{data.ctInfo?'结算中':'待成团'}}</view>
						<view class="one-row status font-12" v-else-if="data.status==3">已完成</view>
						<view class="one-row status font-12" v-else-if="data.status==4">已取消</view>
					</view>
					<view class="w-100 flex-row flex-j-between cont-data">
						<image class="br-4" :src="data.good.original_img"></image>
						<view class="cont">
							<view class="w-100 font-15 font-c-3 newlines lh-22 show-row">
								<view class="btn one-row">
									{{data.active.needer}}中{{data.active.stock_limit}}
								</view>
								{{data.good.good_name}}
							</view>
							<view class="w-100 flex-center flex-j-between mt-20">
								<view class="nowrap font-10 max-w-50 font-dark-main" v-if="data.status==3&&data.is_lock_draw">恭喜获得{{data.active.luck_amount}} {{data.active.luckCoinName}}</view>
								<view class="nowrap font-10 max-w-50 font-dark-main" v-else>未中可获得{{ Number(data.active.return_amount)+Number(data.active.team_price) }} {{data.active.payCoinName}}</view>
								<view class="nowrap font-10 max-w-50 font-dark-main">
									<!-- <text class="font-14 font-c-9">需付:</text> -->
									<text class="font-14 font-dark-main" v-text="data.active.team_price">100</text>
									<text class="font-10 font-c-3" v-text="data.active.payCoinName">斤花生</text>
								</view>
							</view>
						</view>
					</view>
				</view>
				<view class="w-100 pl-12 pr-12 mb-16 bc-white br-4">
					<view class="w-100 flex-center flex-j-between pt-21 pb-21" @click="teamSW=!teamSW">
						<view class="team-cont nowrap flex-center">
							<view class="member flex-center flex-j-center" v-for="(item,index) in data.found.peoples" :key="index" :style="{'right':7*index+'px'}" v-if="index<5">
								<image :src="item.avatar?item.avatar:'../../static/img/bacc1bf65f6c0e8ed8c29f22df3743b.png'"></image>
							</view>
							<view class="member flex-center flex-j-center font-c-9" :style="{'right':7*(data.found.peoples.length)+'px','background-color':'#F1F1F1'}" v-if="data.found.peoples.length.length>5">
								<i class="iconfont icon-gengduo3 font-18 lh-23"></i>
							</view>
							<view class="member flex-center flex-j-center" style="right: 0px;" v-if="data.found.peoples.length==0">
								<image class="team-pic" src="../../static/img/24f149562bd9c3331dccbff3409e07b.png"></image>
							</view>
						</view>
						<view class="flex-center font-c-3 font-10 one-row">
							<view v-if="data.status==3">
								拼团成功
							</view>
							<view v-else-if="data.status==4">
								拼团取消
							</view>
							<view v-else-if="data.status==2 && data.ctInfo">
								拼团结束
							</view>
							<block v-else>
								<view v-if="data.active.needer-data.found.peoples.length!=0">
									还差<text style="color: #FF2022;">{{data.active.needer-data.found.peoples.length}}人</text>拼成
								</view>
								<view v-else>
									拼团人数已满
								</view>
							</block>
							<i class="iconfont icon-icon-test10 font-18 font-c-9 lh-12" v-if="!teamSW"></i>
							<i class="iconfont icon-icon-test9 font-18 font-c-9 lh-12" v-else></i>
						</view>
					</view>
					<view class="w-100 pt-8 pb-8" style="border-top: 1px solid #F8F8F8;" v-if="teamSW">
						<view class="w-100 pt-8 pb-8 flex-center flex-j-between" v-for="(item,index) in data.found.peoples" :key="index">
							<view class="flex-center nowrap">
								<image class="team-pic mr-6" :src="item.avatar?item.avatar:'../../static/img/bacc1bf65f6c0e8ed8c29f22df3743b.png'"></image>
								<text class="font-12 font-c-3 mr-8" v-text="item.user_name">189****1239</text>
								<text class="team-boss" v-if="item.userIsFound">团主</text>
							</view>
							<view class="font-12 font-c-9 one-row" v-text="app._formatDate(item.follow_time)">2020-02-15 15:45:06</view>
						</view>
						<view class="w-100 pt-8 pb-8 flex-center flex-j-between" v-if="data.found.peoples.length==0">
							<view class="flex-center nowrap">
								<image class="team-pic mr-6" src="../../static/img/24f149562bd9c3331dccbff3409e07b.png"></image>
							</view>
						</view>
					</view>
				</view>
				<view class="w-100 bc-white br-4 pt-16 pb-5 pl-13 pr-13">
					<view class="w-100 flex-center flex-j-between nowrap pb-12">
						<view class="one-row font-12 font-c-9 mr-10">参团人数：</view>
						<view class="nowrap font-12 font-c-3">{{data.active.needer}}人团</view>
					</view>
					<view class="w-100 flex-center flex-j-between nowrap pb-12">
						<view class="one-row font-12 font-c-9 mr-10">参团期号：</view>
						<view class="nowrap font-12 font-c-3" v-text="data.found_id">123456789789</view>
					</view>
					<view class="w-100 flex-center flex-j-between nowrap pb-12">
						<view class="one-row font-12 font-c-9 mr-10">下单时间：</view>
						<view class="nowrap font-12 font-c-3" v-text="app._formatDate(data.follow_time)">2020-12-23 12:12:45</view>
					</view>
					<view class="w-100 flex-center flex-j-between nowrap pb-12" v-if="(data.status==2) || (data.status==3)">
						<view class="one-row font-12 font-c-9 mr-10">支付时间：</view>
						<view class="nowrap font-12 font-c-3" v-text="app._formatDate(data.pay_time)">2020-12-23 12:12:45</view>
					</view>
					<view class="w-100 flex-center flex-j-between nowrap pb-12" v-if="data.status==3">
						<view class="one-row font-12 font-c-9 mr-10">完成时间：</view>
						<view class="nowrap font-12 font-c-3" v-text="app._formatDate(data.found.found_end_time)">2020-12-23 12:12:45</view>
					</view>
					<view class="w-100 flex-center flex-j-between nowrap pb-12" v-if="data.status==4">
						<view class="one-row font-12 font-c-9 mr-10">取消时间：</view>
						<view class="nowrap font-12 font-c-3" v-text="app._formatDate(data.end_pay_time)">2020-12-23 12:12:45</view>
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
		<view class="w-100">
			<view class="w-100" style="height: 50px;"></view>
			<view class="w-100 text-center font-white font-14 font-w-b foot-btn" @click="goAgain()" v-if="data.status==3">
				再次参团
			</view>
			<view class="w-100 text-center font-white font-14 font-w-b foot-btn" @click="inviteSW=true" v-if="data.status==2">
				邀请好友
			</view>
			<view class="w-100 text-center font-white font-14 font-w-b foot-btn" @click="paySW=true" v-if="data.status==1">
				立即付款
			</view>
		</view>
		<!-- 分享弹窗 -->
		<!-- <view class="winPopup share w-100 flex-center flex-j-center pl-16 pr-16" :class="{'hide':!inviteSW}" @click="inviteSW=false"> -->
		<view class="winPopup share w-100 flex-center flex-j-center pl-16 pr-16" v-if="inviteSW" @click="inviteSW=false">
			<view class="w-100 bc-white br-4 pt-16 pl-16 pr-16" @click.stop="">
				<view class="w-100" style="background-color: #E6E7F3;overflow: hidden;" v-if="data">
					<image style="width: 311px;height: 311px;" :src="data.good.original_img"></image>
				</view>
				<view class="w-100 flex-row flex-end font-24 font-w-b pt-16 pb-16" style="color: #FF2021;" v-if="data">
					{{data.active.team_price}}<text class="font-12 mb-2 ml-2" v-text="data.active.payCoinName">斤花生</text>
				</view>
				<view class="w-100 flex-row flex-j-between">
					<view style="width: calc(100% - 110px);" v-if="data">
						<view class="w-100 font-15 font-c-3 newlines lh-20 show-row">
							<view class="font-10 font-white br-2 one-row mr-5" style="display: inline-block;line-height: 20px;padding: 0px 5px;background: linear-gradient(#FFB500,#FA6C00);margin: 0px;">{{data.active.needer}}中{{data.active.stock_limit}}</view>
							{{data.good.good_name}}
						</view>
						<view class="w-100 font-dark-main font-12 pt-12 pb-16 newlines" v-if="data.is_lock_draw">
							恭喜获得{{data.active.luck_amount}} {{data.active.luckCoinName}}
						</view>
						<view class="w-100 font-dark-main font-12 pt-12 pb-16 newlines" v-else>
							未中补偿{{Number(data.active.return_amount)+Number(data.active.team_price)}} {{data.active.payCoinName}}
						</view>
						<view class="w-100 font-c-9 font-12 pt-12 pb-32 newlines">
							{{data.active.act_name}}
						</view>
					</view>
					<tki-qrcode ref="qrcode" :val="inviteInfo" :size="100" background="#fff" foreground="#000" pdground="#000" :onval="true" :loadMake="true"  :show="true" unit="px">
						
					</tki-qrcode>
					<!-- <canvas canvas-id="qrcode"  style="width: 100px;height: 100px;" /> -->
				</view>
				<view class="w-100 flex-center flex-j-between pb-25">
					<view class="save-btn br-2 text-center" style="background: linear-gradient(#FFBE1E,#FF940B);" @click="save()">
						保存图片
					</view>
					<view class="save-btn br-2 text-center" style="background: linear-gradient(#FFB500,#FA6C00);" @click="app._copy(inviteCode)">
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
				<view class="w-100 font-12 font-c-3 newlines">正在参与由{{data.found.teamBoss?data.found.teamBoss.user_name:userInfo.NickName}}发起的拼购活动，需支付{{data.active.team_price}} {{data.active.payCoinName}}。</view>
				<view class="w-100 flex-center flex-j-between pt-70 pb-16">
					<text class="font-c-6 font-12">可用余额：</text>
					<text class="font-c-6 font-12">{{data.user.Money}} {{data.active.payCoinName}}</text>
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
	
	// import uQRCode from '@/common/js/uqrcode.js'
	// import UniQrcode from '@/components/uni-qrcode/uni-qrcode'
	import {mapState,mapMutations} from 'vuex'
	
	export default {
		components: {
			ptRecomd,
			loader,
			tkiQrcode,
			// UniQrcode
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
				nw:"",
				backButtonPress:0,
				inviteCode:"",
			}
		},
		onLoad(e) {
			let self=this;
			let data=JSON.parse(e.data);
			this.getDetails(data);
			this.nw=new Date().getTime();
			setInterval(function(){
				self.nw=new Date().getTime();
			},1000);
		},
		onBackPress(options) {  
			let self=this;
			if(self.inviteSW){
				this.backButtonPress++;
				if (this.backButtonPress > 1) { 
					// plus.runtime.quit();
				} else {
					// plus.nativeUI.toast('再按一次退出应用');
				} 
				self.inviteSW=false
				setTimeout(function() {
					this.backButtonPress = 0;
				}, 1000);
				return true;
			}
		}, 
		computed:{
			...mapState(['userInfo','assets']),
		},
		methods: {
			getBtTime:function(item,type){
				var that = this;
				var end;
				// if(type=='ct'){
				// 	end = ((item.found.found_end_time*1000)+(24*60*60*1000));
				// }else{
				// 	end = ((item.end_pay_time*1000)+(24*60*60*1000));
				// };
				if(type=='ct'){
					end = ((item.found.found_end_time*1000));
				}else{
					end = ((item.end_pay_time*1000));
				};
				var leftTime = end - this.nw; //时间差    
				var d, h, m, s, ms;
				if (leftTime > 0) {
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
						item.CTtime='00:00:00';
						that.data.ctInfo=true;
					}else{
						item.time='00:00:00';
						that.data.status=4;
					};
					return '00:00:00'
				}
			},
			goPay:function(){
				var self=this;
				if(Number(self.data.user.Money)<Number(self.data.active.team_price)){
					return self.app._toast("余额不足");
				}
				if(!self.pass||self.pass.length==0){
					return self.app._toast("请输入支付密码");
				}
				self.loader=true;
				let url=config.api + "/shop.activity.pay";
				let send={
					password:self.pass,
					orderSn:self.data.orderSn,
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
							self.paySW=false;
							var page=self.app._prePage();
							page.$vm.pageRefresh();
							setTimeout(function(){
								uni.navigateBack({
									delta: 1,
									success:function(res){
										
									}
								});
							},300);
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
			goAgain:function(){
				let self=this;
				self.data.goods_id=self.data.good_id;
				uni.redirectTo({
					url:"commodity?data="+JSON.stringify(self.data)
				});
			},
			save(){
				let self = this;
				// var dataUrl = canvas.toDataURL();  
				// var b=new plus.nativeObj.Bitmap();  

				// b.loadBase64Data(dataUrl,function(){  
				// 	console.log("创建成功");  
				// },function(){  
				// 	console.log("创建失败");  
				// });  
				// b.save('_www/img1.jpg',{overwrite:true},function(){  
				// 	console.log("保存成功");  
				// },function(){  
				// 	console.log("保存失败");  
				// });  
				// plus.gallery.save( '_www/img1.jpg', function () {  
				// 	console.log( "保存图片到相册成功" );  
				// },function(){  
				// 	console.log( "保存图片到相册失败" );
				// })	
				// return;
				var pages = getCurrentPages();  
				var page = pages[pages.length - 1];  
				var bitmap=null;  
				var currentWebview = page.$getAppWebview();    
				bitmap = new plus.nativeObj.Bitmap('.share');  
				// 将webview内容绘制到Bitmap对象中  
				currentWebview.draw(bitmap,function(){  
				    // console.log('截屏绘制图片成功');  
				    bitmap.save( "_doc/a.jpg"
				    ,{}  
				    ,function(i){  
				        // console.log('保存图片成功：'+JSON.stringify(i));  	
				        uni.saveImageToPhotosAlbum({  
				            filePath: i.target,  
				            success: function () {  
				                bitmap.clear(); //销毁Bitmap图片  
				                uni.showToast({  
				                    title: '保存图片成功',  
				                    mask: false,  
				                    duration: 1500  
				                });  
				            }  
				        });
				    }  
				    ,function(e){  
				        console.log('保存图片失败：'+JSON.stringify(e));
				    });  
				},function(e){  
				    console.log('截屏绘制图片失败：'+JSON.stringify(e));
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
							let people=res.data.data.found.peoples.filter(function(item){
								return item.userIsFound;
							})[0];
							res.data.data.found.teamBoss=people;
							self.data=res.data.data;
							let _name=(self.userInfo.NickName);//用户名encodeURIComponent加密-decodeURIComponent（解密）
							let _code=self.data.active.invitation_code;
							// self.inviteInfo='฿拼小王,拼的多赚的多,复制这段话,打开App参与活动@'+config.shareURL+'?user='+_name+'&code='+_code;	//二维码
							// self.inviteCode='฿'+self.data.good.good_name+","+self.data.active.act_name+'拼小王,拼的多赚的多,复制这段话,打开App参与活动@'+config.shareURL+'?user='+_name+'&code='+_code;//复制
							self.inviteInfo='฿拼小王,拼的多赚的多,复制这段话,打开App参与活动@'+'?user='+_name+'&code='+_code;	//二维码
							self.inviteCode='฿'+self.data.good.good_name+","+self.data.active.act_name+'拼小王,拼的多赚的多,复制这段话,打开App参与活动@'+'?user='+_name+'&code='+_code;//复制
							
							console.log(self.inviteInfo);
							console.log(self.inviteCode);
							// uQRCode.make({
							// 	canvasId: 'qrcode',
							// 	text: self.inviteInfo,
							// 	size: 100,
							// 	margin: 5
							// }).then(res => {
								
							// }).finally(() => {
							// 	uni.hideLoading()
							// })
						}else{
							console.log(res);
							console.log(e.follow_id);
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
	.hide{
		display: none !important;
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
				width: calc(100% - 90px);
				.btn{
					height: 18px;line-height: 18px;padding: 0px 5px;margin: 0px;
					display: inline-block;background: linear-gradient(bottom,#FA6C00,#FFB500);
					color: #FFFFFF;border-radius: 2px;font-size: 10px;bottom: 2px;margin-right: 3px;
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
