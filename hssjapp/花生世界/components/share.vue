<template>
	<view>
		<view class="winPopup flex-row flex-end" v-if="show" @click="closepage()">
			<view class="share-content flex-row flex-end">
				<image mode="widthFix" class="bc" src="@/static/img/1797f9f2370ccaed3168b676cc4f76a.png"></image>
				<view class="page-close font-white" @click="closepage()">
					<i class="iconfont icon-ziyuan"></i>
				</view>
				<view class="share-cont pb-30">
					<view class="font-main font-30 text-center font-w-b pb-5">{{userInfo.InviteCode}}</view>
					<view class="flex-center flex-j-center">
						<!-- <tki-qrcode ref="qrcode" :val="'https://register.hssjpnt.top?code='+userInfo.InviteCode" :size="166" background="#fff" foreground="#000" pdground="#000" :onval="true" :loadMake="true"  :show="true" unit="px"></tki-qrcode> -->
						<canvas canvas-id="qrcode" style="width: 166px;height: 166px;" />
					</view>
					<view class="flex-center flex-j-center pt-30 pb-50">
						<button class="btn" @click="closepage();app.showOpen('share/share')">立即邀请</button>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import tkiQrcode from "@/components/tki-qrcode/tki-qrcode.vue"
	// import uQRCode from '@/common/js/uqrcode.js'
	import uQRCode from "../common/js/uqrcode.js"
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		components: {
			tkiQrcode
		},
		name:"share",
		props:{
			show:{
				type:Boolean
			}
		},
		watch:{
			show:function(e){
				var self=this;
				uQRCode.make({
					canvasId: 'qrcode',
					// text: 'https://register.hssjpnt.top?code='+self.userInfo.InviteCode,
					text: self.app.shareURL+self.userInfo.InviteCode,
					size: 166,
					margin: 5
				}).then(res => {
					// console.log(self.app.shareURL+self.userInfo.InviteCode);
					// console.log(res)
				}).finally(() => {
					uni.hideLoading()
				})
				uni.hideLoading()
			}
		},
		data() {
			return {
				
			}
		},
		created() {
			
		},
		computed:{
			...mapState(['userInfo']),
		},
		methods: {
			closepage:function(){
				this.$emit('update:shareSW', false)
			}
		}
	}
</script>

<style>

</style>
