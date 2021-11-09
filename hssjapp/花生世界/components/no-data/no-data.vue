<template>
	<view class="no-data-content flex-center flex-j-center flex-wrap" v-show="show">
		<image mode="widthFix" src="@/static/img/88926a149f4e2f82dc1402482fcd72a.png"></image>
		<view class="text-center font-15 w-100 pt-15 font-light-gray" v-text="text">暂无数据</view>
	</view>
</template>

<script>
	export default {
		name:"no-data",
		props:{
			show:{
				type: Boolean,
				default: false
			},
			text: {
				type: String,
				default: '暂无数据'
			},
		},
		data() {
			return {
				
			};
		}
	}
</script>

<style>
	.no-data-content{width: 100%;height: 50vh;}
	.no-data-content image{width: 120px;}
</style>
