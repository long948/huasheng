<template>
	<view class="box">
		<view class="w-100" style="white-space: nowrap;width: 100%;">
			<radio-group @change="onGroupChange">
				<view class="share-wrap">
					<view class="share-item" v-for="(item,index) in hbImgs" :key="index">
						<label class="check"><radio :value="index" :checked="index == current"/></label>
						<image :src="item" @tap="previewImage(index)"></image>
					</view>
				</view>
			</radio-group>
		</view>
		<view class="flex-center flex-j-center flex-wrap pt-30 pb-30">
			<button class="btn w-100" @click="save()">{{lang.td9}}</button>
			<!-- <button class="btn w-100 mt-20" @click="app._copy('https://register.hssjpnt.top?code='+userInfo.InviteCode);">复制分享链接</button> -->
			<button class="btn w-100 mt-20" @click="app._copy(app.shareURL+userInfo.InviteCode);">复制分享链接</button>
		</view>
		<block v-if="userInfo.InviteCode">
			<!-- <tki-qrcode ref="qrcode"
				:val="'https://register.hssjpnt.top?code='+ 
				userInfo.InviteCode" :size="300" background="#fff" 
				foreground="#000" pdground="#000" :onval="true" 
				:loadMake="true" :showLoading="false"  :show="false" 
				unit="upx" @result="resultQrCode">
			</tki-qrcode> -->
		</block>
		<canvas canvas-id="qrcode" style="width: 200px;height: 200px;opacity: 0;" />
		<block v-for="(item,index) in bgImgs" :key="index">
			<canvas :canvas-id="'canvas' + index" class="canvas" @error="imageError"></canvas>
		</block>
		<view style="border: 1px solid red;">/{{hbImgs}}/</view>
		<image class="hide-img" :src="item.image" v-for="(item,index) in bgImgs" :key="index" @error="imageError"></image>
	</view>
</template>

<script>
	import config from "@/common/js/config.js"
	import tkiQrcode from "@/components/tki-qrcode/tki-qrcode.vue"
	import SimpleCanvas from '@/common/js/SimpleCanvas.js';
	import uQRCode from "../../common/js/uqrcode.js"
	
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
				shareInfo: {},
				qrCodeImageSrc: '',
				bgImgs: [],
				text: '',
				hbImgs: [],
				current: 0,
			}
		},
		onLoad() {
			var self=this;
			uni.showNavigationBarLoading();
			if( uni.getStorageSync("posters_background") ){
				self.bgImgs = uni.getStorageSync("posters_background");
			};
			if( uni.getStorageSync("posters_copywriting") ){
				self.text = uni.getStorageSync("posters_copywriting");
			};
			self.getInfo();
			
			setTimeout(function(){
				var a=uQRCode.make({
					canvasId: 'qrcode',
					// text: 'https://register.hssjpnt.top?code='+self.userInfo.InviteCode,
					text: self.app.shareURL+self.userInfo.InviteCode,
					size: 200,
					margin: 5
				}).then(res => {
					self.resultQrCode(res)
					// console.log(res)
				}).finally(() => {
					uni.hideLoading()
				})
			},500);
			// self.resultQrCode()
		},
		computed:{
			...mapState(['userInfo','lang']),
		},
		methods: {
			imageError:function(e){
				// console.log(e);
				uni.hideNavigationBarLoading();
				this.app._toast("网络繁忙！");
				console.log('image发生error事件，携带值为' + e.detail.errMsg);
				setTimeout(function(){
					uni.hideNavigationBarLoading();
				},500);
				return;
			},
			previewImage(index){
				console.log(index)
				uni.previewImage({
					current: index,
					urls: this.hbImgs
				});
			},
			onGroupChange(e) {
				console.log("=");
				this.current = e.target.value;
			},
			resultQrCode(qrcode) {
				// console.log(qrcode)
				this.qrCodeImageSrc = qrcode;
				this.bgImgs.forEach((item,index) => {
					this.getImage(item,index);
				});
			},
			getImage(item,index){
				let self = this;
				try{
					let phoneData = uni.getSystemInfoSync();
					let phoneH = phoneData.windowHeight;
					let phoneW = phoneData.windowWidth;
					let canvas = new SimpleCanvas({
						scale: 1,
						canvasId: 'canvas' + index
					}); 
					let top = 0, left = 0;
					if(item.module == 1){
						top = (phoneH / 2 - 70);
						left = ((phoneW - 150) / 2);
					}else{
						top = (phoneH - 260);
						left = 60;
					};
					canvas.createArtboard({// 创建画板
						id: 'share',
						backgroundColor: '#ffffff',
						width: phoneW,
						height: phoneH
					})
					.drawImage({
					    id: 'bg',
					    path: item.image,
						top: 0,
					    left: 0,
					    width: phoneW,
						height: phoneH
					})
					.createCircleRectangle({
					    id: 'ercodeWrap',
					    backgroundColor: '#ffffff',
					    width: 140,
					    height: 140,
						borderRadius: 12,
						left: left,
						top: top
					})
					.drawImage({
					    id: 'ercode',
					    path: self.qrCodeImageSrc?self.qrCodeImageSrc:"",
					    width: 125,
						height: 125,
						referLayer: {
							id: 'ercodeWrap',
							top: -132,
							left: -132
						}
					})
					.createCircleRectangle({
					    id: 'textWrap',
					    backgroundColor: 'rgba(0,0,0,0.3)',
					    width: 170,
					    height: 25,
						borderRadius: 12,
						left: 10,
						top: (phoneH - 50)
					})
					.drawCircleImage({
						id: 'avatar',
						path: '/static/img/bacc1bf65f6c0e8ed8c29f22df3743b.png',
						left: 12,
						d: 20,
						referLayer: {
							id: 'textWrap',
							top: -23
						}
					})
					.drawWrapText({
						id: 'text',
						fontSize: 12,
						text: '来自'+ self.userInfo.NickName +'的推荐',
						lineHeight: 12,
						color: '#ffffff',
						// 位置参照
						referLayer: {
						  id: 'avatar',
						  left: 5,
						  top: -17
						}
					})
					.draw(() => {
						console.log("---");
						uni.canvasToTempFilePath({
							// destWidth:340,
							// destHeight:450,
							destWidth:375,
							destHeight:668,
							canvasId: 'canvas' + index,
							success: (success) => {
								// console.log(success);
								// console.log("success");
								self.hbImgs.push(success.tempFilePath);
								if(self.hbImgs.length == self.bgImgs.length){
									uni.hideNavigationBarLoading();
								}
								console.log(this.bgImgs)
							},
							fail() {
								console.log(this.bgImgs)
							}
						})
					});
				}catch(e){
					console.log(e);
					//TODO handle the exception
				}
			},
			getInfo(){
				var self=this;
				uni.request({
					url: config.api + "/get.share",
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(res);
						if (res.data.status == 1) {
							// res.data.data.background="https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=1819216937,2118754409&fm=26&gp=0.jpg"
							// res.data.data.posters_background=[{
							// 	image:"https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=1819216937,2118754409&fm=26&gp=0.jpg",
							// 	module:2
							// }]
							self.bgImgs = res.data.data.posters_background;
							uni.setStorageSync("posters_background",res.data.data.posters_background);
							self.text = res.data.data.posters_copywriting;
							uni.setStorageSync("posters_copywriting",res.data.data.posters_copywriting);
						}else{
							console.log(JSON.stringify(res));
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {}
				});
			},
			save(){
				var self=this;
				// uni.saveImageToPhotosAlbum({  //保存图片
				// 	filePath: this.hbImgs[this.current],  
				// 	success: (suc) => {
				// 		console.log("图片保存成功");
				// 		self.app._toastIcon('图片保存成功');
				// 	},
				// 	fail: () => {
				// 		console.log("图片保存失败");
				// 		self.app._toast('图片保存失败');
				// 	}
				// })
				uni.saveImageToPhotosAlbum({
					filePath: this.hbImgs[this.current],
					success: function () {
						console.log("图片保存成功");
						self.app._toastIcon(self.lang.td10);
					},
					fail: () => {
						console.log("图片保存失败");
						self.app._toast(self.lang.td11);
					}
				});
			}
		}
	}
</script>

<style>
	page{background-color: #FFFFFF;}
	.canvas{position: fixed;top: 0;left: 0;width: 100%;height: 100%;z-index: 2;opacity: 0;}
	.box{display: flex;flex-direction: column;padding: 16px;}
	scroll-view{width: 100%;height: 680upx;overflow-x: scroll;}
	/* .share-wrap{height: 680upx;width: 900upx;overflow-x: scroll !important;white-space: nowrap;} */
	.share-wrap{height: 680upx;width: 100%;overflow-x: scroll !important;white-space: nowrap;display: block;}
	.share-item{display: inline-block !important;width: 400upx;height: 660upx;position: relative;border-radius: 16upx;overflow: hidden;margin-right: 32upx;}
	.share-item image{width: 100%;height: 100%;position: relative;z-index: 5;}
	.share-item .check{position: absolute;top: 10upx;right: 10upx;z-index: 9;width: 160upx;height: 160upx;text-align: right;}
	.qrcode{width: 160upx;height: 160upx;position: absolute;z-index: 9;bottom: 140upx;left: 50%;margin-left: -80upx;border: 10upx solid rgba(0,0,0,0.3);}
	.qrcode image{width: 100%;height: 100%;}
	.userinfo{position: absolute;z-index: 9;bottom: 20upx;left: 20upx;background:rgba(0,0,0,0.3);border-radius: 80upx;color: #FFFFFF;font-size: 22upx;padding: 4upx 12upx 4upx 2upx;}
	.userinfo image{width: 30upx;height: 30upx;}
	button.btn{position: relative;z-index: 10;}
	.hide-img{
		width: 1px;height: 1px;
	}
</style>
