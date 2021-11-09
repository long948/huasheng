<template>
	<view class="loader-content">
		<block>
			<view class="loader loader1" v-show="type === 'loader1'">
				<view class="loader1-obj">
					<view class="obj"></view>
					<view class="obj"></view>
					<view class="obj"></view>
					<view class="obj"></view>
					<view class="obj"></view>
					<view class="obj"></view>
					<view class="obj"></view>
					<view class="obj"></view>
				</view>
			</view>
			<view class="loader loader2" v-show="type === 'loader2'">
				<view class="loader2-obj">
					<view class="obj"></view>
					<view class="obj"></view>
					<view class="obj"></view>
					<view class="obj"></view>
				</view>
			</view>
			<view class="loader loader1" v-show="type === 'loader3'">
				<svg width="52px" height="24px" viewBox="0 0 52 24" fill="#4FB95C">
					<rect x="0" y="0" width="4" height="100%"/>
					<rect x="8" y="0" width="4" height="100%"/>
					<rect x="16" y="0" width="4" height="100%"/>
					<rect x="24" y="0" width="4" height="100%"/>
					<rect x="32" y="0" width="4" height="100%"/>
					<rect x="40" y="0" width="4" height="100%"/>
					<rect x="48" y="0" width="4" height="100%"/>
				</svg>
			</view>
		</block>
	</view>
</template>

<script>
	export default {
		name:"loader",
		props:{
			type:{
				type: String,
				default:"none"
			}
		},
		data() {
			return {
				
			};
		}
	}
</script>

<style>
	@import url("loader.css");
</style>
