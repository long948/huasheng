<template>
	<view class="box">
		<view class="page-close font-white" @click="app.goBack()"><i class="iconfont icon-ziyuan"></i></view>
		<view class="content pl-15 pr-15">
			<view class="page-title w-100 text-center font-18 pt-25 pb-25 bt_line nowrap">
				<!-- 消息详情 -->
				{{ArticleTitle}}
			</view>
			<view class="details w-100 newlines font-black pt-15 pb-15 lh-20" v-html="details">
				<!-- <rich-text :nodes="details"></rich-text> -->
				
			</view>
			<!-- <view class="w-100">
				<view class="font-dark-green font-14 pt-30 pb-30">恭喜您实名认证成功！</view>
				<view class="font-grey font-14 newlines">
					恭喜您通过实名认证，我们认可了你对环保的关注特赠送您“爱心小树苗”一颗，请再接再厉！
				</view>
				<view class="text-center pt-35">
					<image style="width: 116px;" mode="widthFix" src="../../static/img/57a4531f7eff39e93c7c2184c1f18a2.png"></image>
				</view>
				<view class="btn-cont flex-center flex-j-center pt-40">
					<button class="btn">立即领取</button>
				</view>
			</view> -->
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	export default {
		data() {
			return {
				details:"",
				ArticleTitle:"消息详情",
			}
		},
		onLoad(e) {
			var self=this;
			self.getData(e.id);
		},
		methods: {
			getData:function(id){
				var self=this;
				let url=config.api + "/article-list-detail";
				uni.request({
					url: url,
					data: {
						Id:id
					},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							const regex = new RegExp('<img', 'gi');							res.data.data.ArticleDetails = res.data.data.ArticleDetails.replace(regex, `<img style="max-width: 100%;"`);							self.details=res.data.data.ArticleDetails;
							self.ArticleTitle=res.data.data.TypeTitle;
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
	page{
		height: 100vh;
		background: url(../../static/img/1e0cc92565daf015e510d11505f9289.png) no-repeat;
		background-size: 100% 100%;
		overflow-y: auto;
		position: relative;
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
	.page-close{position: fixed;top: 70px;right: 0px;padding-right: 20px;z-index: 10;}
	.page-close .iconfont{font-size: 25px;}
	.content{
		width: 100%;
		height: calc(100vh - 100px);
		position: fixed;
		left: 0px;
		bottom: 0px;
		background-color: #F2F2F2;
		border-radius: 10px 10px 0px 0px;
		overflow-y: scroll;
		z-index: 1;
	}
	.page-title{color: #3C3C3C;position: relative;line-height: 18px;}
	button.btn{width: 200px;}
	.details {
		-webkit-user-select:text;
		-moz-user-select:text;
		-ms-user-select:text;
		user-select:text;
	}
	.details img{width: 100%;}
</style>
