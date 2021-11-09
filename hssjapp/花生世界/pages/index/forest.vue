<template>
	<view class="box pt-40">
		<view class="heade-title">
			<i class="iconfont icon-zuo font-white" @click="app.goBack()"></i>
			<view class="w-100 text-center font-16 font-white">{{lang.sl1}}</view>
		</view>
		<view class="w-100 pl-15 pr-15">
			<view class="cont-list w-100" v-for="(item,index) in treeList" :key="index">
				<view class="name-cont flex-row flex-end nowrap font-10 font-white w-100 mb-10">
					<text class="font-white font-18 mr-10" v-text="item.nickname">柏树</text>
					{{lang.sl2}}<text class="num font-white font-18" v-text="item.have_hold">3</text>/{{item.max_hold}}{{lang.sl3}}
				</view>
				<view class="tree-list">
					<scroll-view scroll-x="true">
						<view class="tree-cont" v-for="(ktem,kndex) in item.details" :key="kndex">
							<view class="mark" v-if="ktem.is_gave_away==1">{{lang.sl4}}</view>
							<view class="num-cont w-100 flex-center nowrap font-8 font-white">
								<text class="font-grey font-8">{{lang.sl5}}</text>
								<text class="val font-12">{{app._toFixed(ktem.release_amount,2)}}/</text>{{app._toFixed(ktem.total_amount,2)}} PT
							</view>
							<view class="progress">
								<view class="val" :style="{'width': app._accMul(ktem.proportion,100)+'%'}"></view>
							</view>
							<view class="tree">
								<image :style="ktem.style" mode="widthFix" :src="ktem.url"></image>
							</view>
							<view class="btn-cont flex-center flex-j-between">
								<button @click="goHandle(ktem)" v-if="ktem.is_watering == '0' ">{{lang.sl6}}</button>
								<button @click="goHandle(ktem)" v-else-if="ktem.is_receive == '0' && ktem.is_watering != '0' ">{{lang.sl7}}</button>
								<button v-else disabled>{{lang.sl8}}</button>
								<button @click="app.showOpen('index/treeDetails?data='+JSON.stringify(ktem))">{{lang.sl9}}</button>
							</view>
						</view>
					</scroll-view>
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
		data() {
			return {
				treeList:[],
			}
		},
		onLoad() {
			var self = this;
			self.getMyTree();
		},
		computed:{
			...mapState(['lang']),
		},
		methods: {
			goHandle:function(item){
				var self=this;
				var page=self.app._prePage();
				page.$vm.getTreeDetails(item);
				setTimeout(function(){
					self.app.goBack();
				},100)
			},
			getMyTree:function(){
				var self=this;
				let url=config.api + "/sapling-user-list";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						var initlist=self.initTree();
						if(res.data.status==1){
							res.data.data.forEach(function(item){
								initlist.forEach(function(jtem){
									if(item.nickname == jtem.name){ 
										item.details.forEach(function(ktem){
											ktem.url=jtem.imgArr[ Number(ktem.status)-1 ];
											ktem.hd=jtem.hd;
											// ktem.style=jtem.style;
											if(ktem.status==1){
												ktem.style="width:15%";
											}else if(ktem.status==2){
												ktem.style="width:25%";
											}else if(ktem.status==3){
												ktem.style="width:35%";
											}else if(ktem.status==4){
												ktem.style="width:45%";
											}else if(ktem.status==5){
												ktem.style="width:50%";
											}else if(ktem.status==6){
												ktem.style="width:60%";
											};
										});
									};
								})
							});
							self.treeList=res.data.data;
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
			initTree:function(){
				var self=this;
				var list = [{
					hd: "../../static/img/headfcs.png",
					imgArr:[
						"../../static/img/tree/fcs0.png",
						"../../static/img/tree/fcs1.png",
						"../../static/img/tree/fcs2.png",
						"../../static/img/tree/fcs3.png",
						"../../static/img/tree/fcs4.png",
						"../../static/img/tree/fcs5.png",
						"../../static/img/tree/fcs6.png",
					],
					style:"width:73px",
					name: "发财树",
				}, {
					hd: "../../static/img/headbs.png",
					imgArr:[
						"../../static/img/tree/bs0.png",
						"../../static/img/tree/bs1.png",
						"../../static/img/tree/bs2.png",
						"../../static/img/tree/bs3.png",
						"../../static/img/tree/bs4.png",
						"../../static/img/tree/bs5.png",
						"../../static/img/tree/bs6.png",
					],
					style:"width:73px",
					name: "柏树",
				}, {
					hd: "../../static/img/headhs.png",
					imgArr:[
						"../../static/img/tree/hs0.png",
						"../../static/img/tree/hs1.png",
						"../../static/img/tree/hs2.png",
						"../../static/img/tree/hs3.png",
						"../../static/img/tree/hs4.png",
						"../../static/img/tree/hs5.png",
						"../../static/img/tree/hs6.png",
					],
					style:"width:73px",
					name: "槐树",
				}, {
					hd: "../../static/img/headls.png",
					imgArr:[
						"../../static/img/tree/ls0.png",
						"../../static/img/tree/ls1.png",
						"../../static/img/tree/ls2.png",
						"../../static/img/tree/ls3.png",
						"../../static/img/tree/ls4.png",
						"../../static/img/tree/ls5.png",
						"../../static/img/tree/ls6.png",
					],
					style:"width:73px",
					name: "柳树",
				}, {
					hd: "../../static/img/head0.png",
					imgArr:[
						"../../static/img/tree/s0.png",
						"../../static/img/tree/s1.png",
						"../../static/img/tree/s2.png",
						"../../static/img/tree/s3.png",
						"../../static/img/tree/s4.png",
						"../../static/img/tree/s5.png",
						"../../static/img/tree/s6.png",
					],
					style:"width:73px",
					name: "爱心体验小树苗",
				}, {
					hd: "../../static/img/headss.png",
					imgArr:[
						"../../static/img/tree/ss0.png",
						"../../static/img/tree/ss1.png",
						"../../static/img/tree/ss2.png",
						"../../static/img/tree/ss3.png",
						"../../static/img/tree/ss4.png",
						"../../static/img/tree/ss5.png",
						"../../static/img/tree/ss6.png",
					],
					style:"width:73px",
					name: "松树",
				}, {
					hd: "../../static/img/headys.png",
					imgArr:[
						"../../static/img/tree/ys0.png",
						"../../static/img/tree/ys1.png",
						"../../static/img/tree/ys2.png",
						"../../static/img/tree/ys3.png",
						"../../static/img/tree/ys4.png",
						"../../static/img/tree/ys5.png",
						"../../static/img/tree/ys6.png",
					],
					style:"width:73px",
					name: "杨树",
				}];
				return list;
			},
		}
	}
</script>

<style>
	page{
		height: 100vh;
		background: url(../../static/img/1e0cc92565daf015e510d11505f9289.png) no-repeat;
		background-size: 100% 100%;
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
	.box{
		position: relative;
		z-index: 1;
		height: 100vh;
		overflow-y: scroll;
	}
	.heade-title{
		width: 100%;
		position: relative;
	}
	.heade-title .iconfont{
		position: absolute;
		left: 0px;
		bottom: 0px;
		top: 0px;
		padding-left: 20px;
		padding-right: 20px;
		font-size: 22px;
	}
	.cont-list{
		width: 100%;
		padding-top: 36px;
	}
	.cont-list .name-cont{
		line-height: 25px;
	}
	.cont-list .name-cont .num{
		font-style: italic;
		margin-left: 5px;
		margin-right: 5px;
	}
	.cont-list .tree-list{
		width: 100%;
		height: 185px;
	}
	.cont-list .tree-list scroll-view{
		width: 100%;
		height: 100%;
		white-space: nowrap;
	}
	.cont-list .tree-cont{
		width: 136px;
		height: 100%;
		background: url(../../static/img/129f55bae436af7e11435703cdedbc2.png) no-repeat;
		background-size: 100% 100%;
		position: relative;
		margin-right: 16px;
		display: inline-block;
		padding: 4px;
		padding-top: 15px;
	}
	.cont-list .tree-cont:last-child{
		margin-right: 0px;
	}
	.cont-list .tree-cont .btn-cont{
		width: 100%;
		position: absolute;
		left: 0px;
		bottom: 0px;
		padding: 4px 4px;
		display: inline-flex;
	}
	.cont-list .tree-cont .btn-cont button{
		width: 60px;
		height: 21px;
		line-height: 21px;
		color: #A0340A;
		font-size: 12px;
		background: linear-gradient(#F8E4BA,#F8D792,#FD9253);
		border-radius: 15px;
	}
	.cont-list .tree-cont .btn-cont button[disabled]{
		background: #C5C5C5 !important;
	}
	.cont-list .tree-cont .num-cont{
		width: 100%;
		line-height: 18px;
	}
	.cont-list .tree-cont .num-cont .val{
		margin: 0px 2px;
		margin-bottom: 3px;
	}
	.cont-list .tree-cont .mark{
		position: absolute;
		top: 4px;
		right: 4px;
		display: inline-flex;
		align-items: center;
		align-content: center;
		justify-content: center;
		height: 21px;
		width: 21px;
		border-radius: 50%;
		background: linear-gradient(#F8E4BA,#F8D792,#FD9253);
		font-size: 10px;
		color: #A0340A;
	}
	.cont-list .tree-cont .progress{
		width: 100%;
		height: 9px;
		background-color: #62A9FF;
		border-radius: 4px;
		display: flex;
	}
	.cont-list .tree-cont .progress	.val{
		height: 100%;
		border-radius: 4px;
		display: inline-block;
		background: linear-gradient(#BEE860,#63AF19,#519B08);
	}
	.cont-list .tree-cont .tree{
		width: 100%;
		position: absolute;
		bottom: 25px;
		left: 0px;
		height: 118px;
		display: inline-flex;
		align-items: flex-end;
		justify-content: center;
	}
	.cont-list .tree-cont .tree image{
		max-height: 100% !important;
	}
	
	
	
	
</style>
