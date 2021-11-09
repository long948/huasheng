<template>
	<view class="box">
		<image mode="widthFix" src="../../static/start/start.gif"></image>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				
			}
		},
		onLoad() {
			plus.navigator.closeSplashscreen();
			setTimeout(function(){
				uni.switchTab({
					url:"/pages/index/index"
				});
			},5000);
		},
		methods: {
			
		}
	}
</script>

<style>
	page{
		background-color: #000000;
	}
	.box{
		width: 100%;
		height: 100vh;
		overflow: hidden;
		display: flex;
		align-items: center;
		align-content: center;
		justify-content: center;
	}
	image{
		width: 100%;
		max-height: 100%;
	}
</style>
