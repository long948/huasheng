<template>
	<view class="box flex-center flex-j-center flex-end pl-15 pr-15 flex-wrap" v-if="show">
		<view class="w-100 text-right"><view class="-close font-white" @click="closepage()"><i class="iconfont icon-ziyuan"></i></view></view>
		<view class="content pl-15 pr-15">
			<view class="page-title w-100 text-center font-18 pt-25 pb-25 bt_line">
				恭喜升级
			</view>
			<view class="w-100">
				<view class="font-main font-14 pt-30 pb-5">恭喜您升级！</view>
				<view class="font-grey font-14 newlines">
					恭喜您升级到{{JSON.parse(data).level.nickname}}
					<text v-if="JSON.parse(data).level.level==1">，请联系客服开启分红！</text>
				</view>
				<view class="text-center pt-25">
					<image style="width: 50px;" mode="widthFix" :src="JSON.parse(data).level.url"></image>
				</view>
				<view class="btn-cont flex-center flex-j-center pt-15" v-if="JSON.parse(data).level.level==1">
					<button class="btn" @click="getQQ()">联系客服</button>
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
		name:"upgrade",
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
			getQQ:function(){
				var self=this;
				let url=config.api + "/qq";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							var qq=res.data.data;
							// let url="mqqwpa://im/chat?chat_type=wpa&uin="+qq; 
							let url="mqqwpa://im/chat?chat_type=wpa&uin="+qq+"&version=1&src_type=web&web_src=oicqzone.com";
							// let url="tencent://Message/?Uin="+qq+"&websiteName=www.qq.com&Menu=yes"
							plus.runtime.openURL(url,function (res) {
								plus.nativeUI.alert("本机没有安装QQ，无法启动");  
							});
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
			closepage:function(){
				this.$emit('update:upgradeSW', false)
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
	.box{
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
	.page-title{color: #3C3C3C;position: relative;line-height: 18px;}
	button.btn{width: 200px;}
	.details img{width: 100%;}
</style>
