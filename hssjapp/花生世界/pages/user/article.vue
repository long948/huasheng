<template>
	<view class="box pt-15 pb-15 pl-15 pr-15">
		<view class="search-cont flex-center br-4 pl-15 pr-15 mb-15">
			<image src="../../static/img/e48838fbc9a36fd25bf1a937ff3271a.png"></image>
			<input class="font-14" type="text" :placeholder="lang.userInfo31" @confirm="init" v-model="val" />
		</view>
		<view class="cont-list br-4 bc-white pt-15 pb-15 pl-15 pr-15 mb-10" v-for="(item,index) in list" :key="index" @click="app.showOpen('index/newsDetails?id='+item.Id)">
			<view class="title flex-center nowrap font-14 font-grey">
				<image src="../../static/img/091f17703eb3c54d826e333a446e596.png" v-if="item.IsStick==1"></image>
				{{item.ArticleTitle}}
			</view>
			<view class="font-12 font-grey op-6 nowrap mt-5" v-text="item.ArticleSubtitle">一文带你玩转空军一号一文带你玩转空军一号...</view>
		</view>
		<uni-load-more :status="loadingType"></uni-load-more>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		components: {
			uniLoadMore
		},
		data() {
			return {
				paeg:1,
				count:20,
				list:[],
				loadingType: 'more',
				val:"",
			}
		},
		computed:{
			...mapState(['userInfo','qiniu','lang']),
		},
		onLoad() {
			var self = this;
			uni.setNavigationBarTitle({
				title:self.lang.i3
			});
			self.getList();
		},
		onReachBottom(){
			var self = this;
			self.getList();
		},
		computed:{
			...mapState(['userInfo','qiniu','lang']),
		},
		methods: {
			init:function(){
				var self=this;
				self.loadingType="more";
				self.paeg=1;
				self.list=[];
				self.getList();
			},
			getList:function(){
				var self=this;
				if(self.loadingType === 'nomore'){
					return;
				}else{
					self.loadingType = 'loading';
				};
				let send={
					CallIndex:'school',
					page:self.paeg,
					count:self.count,
					keyword:self.val,
				};
				let url=config.api + "/article-list";
				uni.request({
					url: url,
					data: send,
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							for(var i=0;i<res.data.data.length;i++){
								var item=res.data.data[i];
								self.list.push(item);
							};
							if(res.data.data.length<self.count){
								self.loadingType = 'nomore';
							}else{
								self.loadingType = 'more';
							};
							self.page++;
						};
					},
					fail: (res) => {
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						setTimeout(function(){
							self.val="";
						},500);
					}
				});
			}
		}
	}
</script>

<style>
	page{background-color: #F7F7F7;}
	.search-cont{width: 100%;height: 36px;background-color: #E8E8E8;}
	.search-cont image{width: 14px;height: 14px;display: inline-block;}
	.search-cont input{width: calc(100% - 20px);height: 100%;padding: 0px 15px;color: #333333;}
	.cont-list .title{width: 100%;}
	.cont-list .title image{width: 18px;height: 16px;display: inline-block;margin-right: 10px;}

</style>
