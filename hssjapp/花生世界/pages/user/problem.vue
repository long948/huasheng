<template>
	<view class="box pt-30 pl-10 pr-10">
		<view class="font-14 font-dark-grey mb-10">反馈记录</view>
		<view class="cont-list bc-white flex-center flex-j-between pt-15 pb-15 pl-15 pr-15 mb-10 br-4"  v-for="(item,index) in list" :key="index" @click="getDetails(item)">
			<view class="nowrap font-grey font-14" v-text="item.title">1. 该如何购买PPT?</view>
			<text class="font-green one-row" v-if="item.is_hand">已回复</text>
			<text class="font-red one-row" v-else>未回复</text>
		</view>
		<view class="foot-cont text-center">
			<image @click="addSW=true" src="../../static/img/72b97ffa3f0428921cbfd2d3ed26ad6.png"></image>
			<view class="w-100 text-center pt-15 pb-15 font-14 font-dark-grey">提交问题</view>
		</view>
		<view class="winPopup flex-center flex-j-center pl-15 pr-15 pt-15 pb-15" v-if="addSW">
			<view class="pop-content">
				<image class="-close" @click="addSW=false" src="../../static/img/489dd1e025879d54201098b73604cab.png"></image>
				<view class="content bc-white">
					<view class="title">问题详情</view>
					<view class="font-14 font-dark-grey pt-15 pb-15 font-w-b">标题</view>
					<input class="mb-20" type="text" placeholder="请输入标题" v-model="title" />
					<view class="cont" style="height: 100px;">
						<textarea @input="getval"></textarea>
					</view>
					<view class="btn-cont pb-40 mt-40">
						<button class="btn" @click="add()">提交</button>
					</view>
				</view>
			</view>
		</view>
		<view class="winPopup flex-center flex-j-center pl-15 pr-15 pt-15 pb-15" v-if="detailsSW">
			<view class="pop-content">
				<image class="-close" @click="detailsSW=false" src="../../static/img/489dd1e025879d54201098b73604cab.png"></image>
				<view class="content bc-white">
					<view class="title">问题详情</view>
					<view class="font-14 pt-15 pb-30 font-w-b newlines" style="color: #666666;">
						<text class="font-dark-grey mr-10">标题</text>{{details.title}}
					</view>
					<view class="font-14 font-dark-grey pb-10">问题详情</view>
					<view class="cont mb-20 newlines" style="height: 150px;" v-text="details.details">
						
					</view>
					<view class="font-14 font-dark-grey pb-10">平台回复</view>
					<view class="cont mb-40 newlines" style="height: 150px;" v-text="details.reply">
						
					</view>
					<view></view>
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
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		components: {
			loader,
		},
		data() {
			return {
				loader:false,
				list:[],
				addSW:false,
				detailsSW:false,
				title:"",
				val:"",
				details:"",
			}
		},
		onLoad(e) {
			var self=this;
			self.getRecord();
		},
		computed:{
			...mapState(['userInfo','qiniu','lang']),
		},
		methods: {
			getDetails:function(item){
				var self=this;
				if(item.is_hand){
					self.details=item;
					self.detailsSW=true;
				};
			},
			add:function(){
				var self=this;
				if(self.title==""){
					return self.app._toast("请输入标题");
				};
				if(self.val==""){
					return self.app._toast("请输入内容");
				};
				let send={
					title:self.title,
					details:self.val,
				};
				self.loader=true;
				let url=config.api + "/user-work-submit";
				uni.request({
					url: url,
					data: send,
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.getRecord();
							self.title="";
							self.val="";
							self.addSW=false;
						}else{
							console.log(JSON.stringify(res));
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
			getval:function(e){
				let self=this;
				self.val=e.detail.value;
			},
			getRecord:function(){
				var self=this;
				self.loader=true;
				let url=config.api + "/user-work-list";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.list=res.data.data;
						};
					},
					fail: (res) => {
						console.log(res);
					},
					complete: (res) => {
						self.loader=false;
					}
				});
			}
		}
	}
</script>

<style>
	page{background-color: #F7F7F7;}
	.box{
		padding-bottom: 150px;
	}
	.foot-cont{
		width: 100%;
		position: fixed;
		bottom: 0px;
		left: 0rpx;
		z-index: 1;
	}
	.foot-cont image{
		width: 48px;
		height: 48px;
	}
	.foot-cont{
		padding: 20px 0px;
	}
	.pop-content{
		position: relative;width: 100%;
	}
	.pop-content image.-close{
		position: absolute;
		right: 0px;
		width: 24px;
		height: 24px;
		top: -30px;
	}
	.pop-content .content{
		width: 100%;border-radius: 10px;
		padding: 0px 12px;
		max-height: 90vh;
		overflow-y: auto;
	}
	.pop-content .content .title{
		width: 100%;text-align: center;color: #3C3C3C;font-size: 18px;padding: 17px 0px;
		border-bottom: 1rpx solid #F5F5F5;font-weight: bold;
	}
	.pop-content .content input{
		height: 44px;color: #333333;
		width: 100%;background-color: #F0F0F0;
		padding: 0px 15px;border-radius: 6px;
	}
	.pop-content .content .cont{
		background-color: #F0F0F0;width: 100%;
		padding: 12px;border-radius: 4px;
		overflow-y: auto;color: #333333;font-size: 14px;
	}
	.pop-content .content .cont textarea{
		width: 100%;font-size: 14px;
		height: 100%;color: #333333;
	}
</style>
