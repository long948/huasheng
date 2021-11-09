<template>
	<view class="box">
		<view class="top-data pt-25 pb-25 pl-25 pr-25">
			<view class="flex-center flex-j-between pb-5">
				<view class="font-20 font-white" v-text="get_status(details)">等待买家付款</view>
				<view class="font-18 font-white" v-text="details.RemarkCode">093251</view>
			</view>
			<view class="flex-center flex-j-between">
				<block v-if="details.State==0||details.State==1">
					<view class="font-12 font-white" v-if="time<0">{{lang.order23}}</view>
					<view class="font-12 font-white" v-else-if="time>0">{{lang.order24}}<text style="color: #FFDC7B;">{{time}}s</text>，{{lang.order23}}</view>
				</block>
				<text v-else></text>
				<view class="font-12 font-white">{{lang.order25}}</view>
			</view>
		</view>
		<view class="mt-15 bc-white pt-25 pb-25 pl-25 pr-25 shadow">
			<view class="flex-center nowrap font-cl-6 pb-15">
				<text class="one-row font-cl-9 font-14 mr-20">{{lang.order26}}</text>PT
			</view>
			<view class="flex-center flex-j-between pb-15">
				<view class="flex-center nowrap font-cl-6">
					<text class="one-row font-cl-9 font-14 mr-20">{{lang.order27}}</text>{{app._toFixed(details.Number,4)}} PT
				</view>
				<text class="one-row font-cl-9 font-14">{{lang.order28}}</text>
			</view>
			<view class="flex-center flex-j-between">
				<view class="flex-center nowrap font-cl-6">
					<text class="one-row font-cl-9 font-14 mr-20">{{lang.order29}}</text>{{app._accMul(details.Price,7)}} CNY
				</view>
				<text class="one-row font-dark-main font-21" v-if="details.IsAddress==1">USDT {{ app._accMul(details.Price,details.Number) }}个</text>
				<text class="one-row font-dark-main font-21" v-else>¥{{ app._accMul(details.SumPrice,7) }}</text>
			</view>
		</view>
		<view class="mt-15 bc-white pt-20 pb-20 pl-25 pr-25 shadow"><!-- v-if="details&&details.State==1||details.State==2" -->
			<view class="w-100">
				<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
					<text class="one-row font-cl-9 font-14 mr-20">{{lang.order30}}</text>
					<view class="newlines font-cl-9 font-14" v-if="details.IsAddress==1"><image style="width: 20px;height: 20px;display: inline-block;" src="../../static/img/53c183e8d60199ef97be47fdd75a547.png"></image></view>
					<view class="newlines font-cl-9 font-14" v-if="details.IsAlipay==1"><image style="width: 20px;height: 20px;display: inline-block;" src="../../static/img/bc613dffddbfeeb9fde4ebacc2ee407.png"></image></view>
				</view>
				<view v-if="details.IsAddress==1">
					<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
						<text class="one-row font-cl-9 font-14 mr-20">{{lang.order31}}</text>
						<view class="flex-center font-grey flex-wrap font-cl-9 font-14" style="width: calc(100% - 50px);">
							<view class="w-100 nowrap text-right">{{details.Address.Account}}</view>
							<view class="w-100 text-right pt-5">
								<image @click="app._copy(details.Address.Account)" class="ml-10" style="width: 16px;height: 18px;display: inline-block;" src="../../static/img/8cb6845e062449dcef4ce3bd75774b3.png"></image>
							</view>
						</view>
					</view>
					<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
						<text class="one-row font-cl-9 font-14 mr-20">{{lang.order32}}</text>
						<view class="flex-center newlines font-cl-9 font-14">
							<text class="look" @click="codeSW1=true">{{lang.order33}}</text>
							<image class="ml-10" style="width: 20px;height: 20px;display: inline-block;" src="../../static/img/67d0de82f678be9368dc9deb0a92dad.png"></image>
						</view>
					</view>
				</view>
				<view class="w-100" v-if="details.IsAlipay==1">
					<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
						<text class="one-row font-cl-9 font-14 mr-20">{{lang.order34}}</text>
						<view class="newlines font-cl-9 font-14" v-text="details.Alipay.NickName"></view>
					</view>
					<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
						<text class="one-row font-cl-9 font-14 mr-20">{{lang.order32}}</text>
						<view class="flex-row">
							<text class="look" @click="codeSW=true">{{lang.order33}}</text>
							<image class="ml-10" style="width: 20px;height: 20px;display: inline-block;" src="../../static/img/67d0de82f678be9368dc9deb0a92dad.png"></image>
						</view>
					</view>
					<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
						<text class="one-row font-cl-9 font-14 mr-20">{{lang.order35}}</text>
						<view class="newlines font-gray font-cl-9 font-14 flex-center">{{details.Alipay.Account}}
							<text class="font-dark-main ml-10" @click="Pullout(details.Alipay.Account)">拨打</text>
							<text class="font-dark-main ml-10" @click="app._copy(details.Alipay.Account)">复制</text>
						</view>
					</view>
					<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
						<text class="one-row font-cl-9 font-14 mr-20">{{lang.order25}}</text>
						<view class="flex-center newlines font-cl-9 font-14">
							{{details.RemarkCode}}
							<image @click="app._copy(details.RemarkCode)" class="ml-10" style="width: 16px;height: 18px;display: inline-block;" src="../../static/img/8cb6845e062449dcef4ce3bd75774b3.png"></image>
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="mt-15 bc-white pt-20 pb-20 pl-25 pr-25 shadow">
			<view class="font-16 font-cl-6 pb-15">{{lang.order36}}</view>
			<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
				<text class="one-row font-cl-9 font-14 mr-20">{{lang.order37}}</text>
				<view class="newlines font-cl-9 font-14" v-text="details.BuyMember">影与光的浪漫</view>
			</view>
			<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
				<text class="one-row font-cl-9 font-14 mr-20">买家联系方式</text>
				<view class="newlines font-cl-9 font-14">
					{{details.BuyMemberPhone}}
					<image @click="app._copy(details.BuyMemberPhone)" class="ml-10" style="width: 16px;height: 18px;display: inline-block;" src="../../static/img/8cb6845e062449dcef4ce3bd75774b3.png"></image>
				</view>
			</view>
			<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
				<text class="one-row font-cl-9 font-14 mr-20">{{lang.order38}}</text>
				<view class="newlines font-cl-9 font-14">{{lang.order39}}</view>
			</view>
			<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
				<text class="one-row font-cl-9 font-14 mr-20">{{lang.order40}}</text>
				<view class="newlines font-cl-9 font-14" v-text="details.SellMember">云和山的彼端</view>
			</view>
			<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
				<text class="one-row font-cl-9 font-14 mr-20">{{lang.order41}}</text>
				<view class="newlines font-cl-9 font-14">{{details.OrderSn}}<image @click="app._copy(details.OrderSn)" class="ml-10" style="width: 16px;height: 18px;display: inline-block;" src="../../static/img/8cb6845e062449dcef4ce3bd75774b3.png"></image> </view>
			</view>
			<view class="flex-row flex-j-between lh-20 font-cl-6 pb-20">
				<text class="one-row font-cl-9 font-14 mr-20">{{lang.order42}}</text>
				<view class="newlines font-cl-9 font-14" v-text="app._formatDate(details.AddTime)">2019-10-26 10:25:26</view>
			</view>
			<view class="flex-row flex-j-between lh-20 font-cl-6" v-if="details.PayTime!=0">
				<text class="one-row font-cl-9 font-14 mr-20">{{lang.order43}}</text>
				<view class="newlines font-cl-9 font-14" v-text="app._formatDate(details.PayTime)">2019-10-26 10:25:26</view>
			</view>
		</view>
		<view class="mt-15 bc-white pt-20 pb-20 pl-25 pr-25" v-if="details.State==1||details.State==2">
			<view class="font-16 font-cl-6 pb-20 flex-center flex-j-between">{{lang.order44}}</view>
			<view class="flex-row flex-j-between lh-20 font-cl-6 pb-15">
				<text class="one-row font-cl-9 font-14 mr-20">{{lang.order45}}</text>
				<view class="newlines font-cl-9 font-14" v-text="details.BuyMember">2019-10-26 10:25:26</view>
			</view>
			<view class="flex-row flex-j-between lh-20 font-cl-6">
				<text class="one-row font-cl-9 font-14 mr-20">{{lang.order46}}</text>
				<view class="newlines font-cl-9 font-14">{{lang.order47}}</view>
			</view>
			<view class="pt-5 pb-30">
				<view class="add-cont flex-center flex-j-between flex-wrap">
					<view class="pic" v-for="(item,index) in details.Images" :key="index" @click="lookPic">
						<image :src="item"></image>
					</view>
				</view>
			</view>
			<view class="btn-cont" v-if="details.State==1 && details.Operate==2">
				<button class="btn" :disabled="!confirm_status" @click="paySW=true;">{{lang.order48}}</button>
			</view>
			<view class="text-center pt-15 pb-20 font-red font-12" v-if="details.State==1&&details.Operate==2"><text @click="app.showOpen('trade/appeal?data='+JSON.stringify(details))">{{lang.order49}}</text></view>
		</view>
		<view class="text-center pt-15 pb-20 font-red font-12" v-if="details.State==5"><text @click="app.showOpen('trade/appealResult?data='+JSON.stringify(details))">{{lang.order50}}</text></view>
		<view class="mt-15 bc-white pt-20 pb-20 pl-25 pr-25" v-if="details.State==0 && details.Operate==1">
			<view class="font-16 font-cl-6 pb-5 flex-center flex-j-between">{{lang.order51}}<text class="font-12 font-cl-9">{{pic.length}}/6</text></view>
			<view class="pt-25 pb-30">
				<view class="font-14 font-cl-9">{{lang.order52}}</view>
				<view class="add-cont flex-center flex-j-between flex-wrap">
					<view class="pic" @click="uploadPic(index)" v-for="(item,index) in pic" :key="index">
						<image :src="item.url"></image>
					</view>
					<view class="pic" @click="uploadPic()" v-if="pic.length<6">
						<i class="iconfont icon-jia"></i>
					</view>
				</view>
			</view>
			<view class="btn-cont">
				<button class="btn" :disabled="!confirm_status" @click="confirmPay()">{{lang.order53}}</button>
			</view>
		</view>
		<view class="winPopup flex-center flex-j-center" v-if="codeSW">
			<view class="code-content bc-white">
				<image @click="codeSW=false;lookAp(details.Alipay.QrCode)" :src="details.Alipay.QrCode"></image>
				<view class="w-100 text-center font-10 op-5 font-dark-grey">点击图片查看详情，长按保存图片</view>
				<button class="btn" @click="codeSW=false">{{lang.order54}}</button>
			</view>
		</view>
		<view class="winPopup flex-center flex-j-center" v-if="codeSW1">
			<view class="code-content bc-white">
				<tki-qrcode ref="qrcode" :val="details.Address.Account" :size="165" background="#fff" foreground="#000" pdground="#000" :onval="true" :loadMake="true"  :show="true" unit="px"></tki-qrcode>
				<button class="btn" @click="codeSW1=false">{{lang.order54}}</button>
			</view>
		</view>
		<!-- 密码弹窗 -->
		<view class="winPopup flex-center flex-j-center pl-20 pr-20" v-if="paySW">
			<view class="grade-content w-100">
				<view class="grade-popup-close font-white" @click="paySW=false">
					<i class="iconfont icon-ziyuan"></i>
				</view>
				<view class="content bc-white pl-15 pr-15 pb-30 w-100 br-10 pt-30">
					<input class="payIn" type="password" :placeholder="lang.order55" v-model="pass" />
					<view class="text-right mt-10"><text class="font-12 font-dark-main" @click="app.showOpen('login/forgetPay')">{{lang.order56}}</text></view>
					<view class="pt-20">
						<button class="btn" @click="confirmSend()">{{lang.order54}}</button>
					</view>
				</view>
			</view>
		</view>
		<!-- <view class="winPopup">
			<
		</view> -->
	</view>
</template>
<!-- Operate 1我的求购 2我的出售 -->
<script>
	import config from "@/common/js/config.js";
	import upload from "@/common/js/upload.js";
	import tkiQrcode from "@/components/tki-qrcode/tki-qrcode.vue";
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		components: {
			tkiQrcode
		},
		data() {
			return {
				id:"",
				details:"",
				time:"-",
				codeSW:false,
				codeSW1:false,
				confirm_status:true,
				pic:[],
				key:[],
				paySW:false,
				pass:"",
			}
		},
		onLoad(e) {
			var self=this;
			self.id=JSON.parse(e.data).Id;
			self.getdetails();
			
		},
		computed:{
			...mapState(['userInfo','qiniu','lang']),
		},
		methods: {
			Pullout:function(e){
				var num= e.replace(/[^0-9]/ig,"");
				// var num = s.replace(/[^0-9]/ig,""); //字符夹带数字
				uni.makePhoneCall({
				    phoneNumber: num
				});
			},
			lookAp:function(pic){
				let self=this;
				uni.previewImage({
					urls: [pic],
					longPressActions: {
						itemList: [self.lang.order57],
						success: function(data) {
							// console.log('选中了第' + (data.tapIndex + 1) + '个按钮,第' + (data.index + 1) + '张图片');
							if(data.tapIndex==0){
								uni.downloadFile({
									url: pic,
									success: (res) => {
										if (res.statusCode === 200) {
											var tempFilePath=res.tempFilePath
											uni.saveImageToPhotosAlbum({
												filePath: tempFilePath,
												success: function () {
													console.log("成功")
													uni.showToast({title:self.lang.order58,icon:'none',duration: 2000});
												}
											});
										};
									},
								});
							};
						},
						fail: function(err) {
							console.log(err.errMsg);
						}
					}
				});
			},
			lookPic:function(){
				var self=this;
				// uni.previewImage({
				// 	current:self.details.Images,
				// 	urls:self.details.Images[0],
				// });
				uni.previewImage({
					urls: self.details.Images,
					longPressActions: {
						itemList: [self.lang.order57],
						success: function(data) {
							// console.log('选中了第' + (data.tapIndex + 1) + '个按钮,第' + (data.index + 1) + '张图片');
							if(data.tapIndex==0){
								uni.downloadFile({
									url: self.details.Images[(data.index)],
									success: (res) => {
										if (res.statusCode === 200) {
											var tempFilePath=res.tempFilePath
											uni.saveImageToPhotosAlbum({
												filePath: tempFilePath,
												success: function () {
													uni.showToast({title:self.lang.order58,icon:'none',duration: 2000});
												}
											});
										};
									},
								});
							};
						},
						fail: function(err) {
							console.log(err.errMsg);
						}
					}
				});
			},
			confirmPay:function(){
				var self=this;
				if(self.key.length==0){
					return self.app._toast(self.lang.order59);
				};
				uni.showModal({
					content: self.lang.order60,
					confirmText: self.lang.order54,
					cancelText: self.lang.order61,
					success: function (e) {
						if(e.confirm){
							let send={
								Id:self.id,
								Imgs:JSON.stringify(self.key),
							};
							self.confirm_status=false;
							uni.showLoading({title: self.lang.order62});
							let url=config.api + "/ctc-trade-pay";
							uni.request({
								url: url,
								data: send,
								method: "post",
								header: {Authorization: config.getToken()},
								success: res => {
									// console.log(JSON.stringify(res));
									config.api_status(res);
									if(res.data.status==1){
										self.app._toastIcon(self.lang.order63);
										setTimeout(function(){
											self.app.closeOpen('trade/trade');
										},1500);
									}else{
										console.log(JSON.stringify(res));
										self.app._toast(res.data.message);
									};
								},
								fail: (res) => {
									console.log(JSON.stringify(res));
								},
								complete: (res) => {
									console.log(JSON.stringify(res));
									setTimeout(function(){
										uni.hideLoading();
									},2000);
									self.confirm_status=true;
								}
							});
						};
					}
				})
			},
			uploadPic:function(index){
				var self=this;
				upload.upload3(self.qiniu,function(res){
					let str={
						key:res.key,
						url:res.url,
					};
					if(index!==undefined){
						self.pic[index]=str;
						self.key[index]=res.key;
					}else{
						self.pic.push(str);
						self.key.push(res.key);
					};
				},function(res){
					self.app._toast(res);
				});
			},
			confirmSend:function(){
				var self=this;
				if(self.pass.trim().length==0){
					return self.app._toast(self.lang.order64);
				};
				uni.showModal({
					content: self.lang.order65,
					confirmText: self.lang.order54,
					cancelText: self.lang.order61,
					success: function (e) {
						if(e.confirm){
							let send={
								Id:self.id,
								PayPassword:self.pass,
							};
							self.confirm_status=false;
							uni.showLoading({title: self.lang.order62});
							let url=config.api + "/ctc-confirm";
							uni.request({
								url: url,
								data: send,
								method: "post",
								header: {Authorization: config.getToken()},
								success: res => {
									// console.log(JSON.stringify(res));
									config.api_status(res);
									if(res.data.status==1){
										self.app._toastIcon(self.lang.order63);
										setTimeout(function(){
											self.app.closeOpen('trade/trade');
										},2500);
										self.paySW=false;
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
										uni.hideLoading();
									},2000);
									self.confirm_status=true;
								}
							});
						};
					}
				})
			},
			countdown:function(s){
				var self=this;
				s=Number(s);
				var downtime=setInterval(function(){
					s--;
					if(s<0){
						// console.log("停止倒计时"+s);
						clearInterval(downtime);
					};
					self.time=s;
				},1000)
			},
			get_status:function(item){
				let self=this;
				if(item.State==0){
					if(item.Operate==1){
						return self.lang.order66;
					}else{
						return self.lang.order67;
					};
				}else if(item.State==1){
					if(item.Operate==1){
						return self.lang.order68;
					}else{
						return self.lang.order69;
					};
				}else if(item.State==2){
					return self.lang.order70;
				}else if(item.State==3){
					if(item.Operate==1){
						return self.lang.order71;
					}else{
						return self.lang.order72;
					};
				}else if(item.State==4){
					return self.lang.order73;
				}else if(item.State==5){
					return self.lang.order74;
				};
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
							// console.log(self.details);
							self.countdown(self.details.CancleTime);
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
	.top-data{background: url(../../static/img/6febb21a5b9b26e3ad4fa0c85cfc6c7.png) no-repeat;background-size: 100% 100%;}
	.shadow{box-shadow: 0px 0px 4px #E8EAF0;}
	.look{height: 22px;line-height: 22px;border: 1px solid #FA6C00;border-radius: 2px;padding: 0px 8px;color: #FA6C00;font-size: 12px;}
	.add-cont{width: 100%;position: relative;}
	.add-cont::after{width: 30%;content: "";}
	.pic{width: 30%;height: 100px;border-radius: 2px;background-color: #D8D8D8;margin-top: 15px;display: flex;align-items: center;justify-content: center;}
	.pic .iconfont{font-size: 35px;color: #FFFFFF;}
	.pic image{width: 100%;height: 100%;}
	.btn-cont button.btn{background: linear-gradient(bottom,#FA6C00,#FFB500);height: 44px;line-height: 44px;font-size: 14px;}
	.code-content{max-width: 80%;border-radius: 7px;padding: 20px;position: relative;}
	.code-content image{width: 165px;height: 165px;}
	.code-content button.btn{width: 85px;height: 30px;line-height: 30px;border-radius: 16px;background: linear-gradient(bottom,#FA6C00,#FFB500);color: #FFFFFF;font-size: 14px;position: absolute;bottom: -15px;left: calc(50% - 40px);}
</style>
