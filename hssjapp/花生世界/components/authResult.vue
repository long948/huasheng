<template>
	<view class="box1 flex-center flex-j-center flex-end pl-15 pr-15 flex-wrap" v-if="show">
		<view class="w-100 text-right"><view class="-close font-white" @click="closepage()"><i class="iconfont icon-ziyuan"></i></view></view>
		<view class="content pl-15 pr-15">
			<view class="page-title1 w-100 text-center font-18 pt-25 pb-25 bt_line">
				实名认证
			</view>
			<view class="font-main font-14 pt-30">恭喜您实名认证提交成功！</view>
			<view class="w-100">
				<view class="pt-25">
					<view class="font-grey lh-20 font-14 newlines">
						尊敬的用户你的实名认证已经提交成功，请耐心等待审核结果
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		name:"authResult",
		props:{
			show:{
				type:Boolean
			},
			data:{
				type:String
			}
		},
		data() {
			return {
				
			}
		},
		methods: {
			closepage:function(){
				this.$emit('update:authSW', false)
			}
		}
	}
</script>

<style>
	page{
		width: 100%;
		height: 100vh;
		background: url(@/static/img/1e0cc92565daf015e510d11505f9289.png) no-repeat;
		background-size: 100% 100%;
		overflow-y: auto;
		z-index: 600;
	}
	page::after{
		position: absolute;
		top: 0px;
		left: 0px;
		right: 0px;
		bottom: 0px;
		content: "";
		background-color: rgba(4,4,4,0.4);
	}
	.box1{
		height: 100vh;
		width: 100%;
		display: flex;
		align-items: center !important;
		align-content: center !important;
		justify-content: center !important;
		padding-bottom: 0px;
		padding-top: 40px;
		margin: 0px !important;
	}
	/* .page-close{position: fixed;top: 70px;right: 0px;padding-right: 20px;z-index: 10;} */
	.-close{position: relative;top:0px;bottom: 0px;right: 0px;}
	.-close .iconfont{font-size: 25px;}
	.content{
		width: 100%;
		max-height: calc(100vh - 200px);
		background-color: #F2F2F2;
		border-radius: 10px 10px 10px 10px;
		overflow-y: scroll;
		z-index: 1;
		margin: 0px;
		padding-bottom: 20px;
		top: 0px !important;
	}
	.page-title1{color: #3C3C3C;position: relative;line-height: 18px;}
	button.btn{width: 200px;}
	.details img{width: 100%;}
</style>
