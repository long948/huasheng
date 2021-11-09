<template>
	<view class="w-100">
		<!-- <view class="notice-cont">
			<image src="../../static/img/fd54b04f438f2eff271e2d96937bcec.png"></image>
			<view class="notice">
				<swiper class="nowrap" :autoplay="true" :circular="true" :vertical="false" :disable-touch="false" :interval="6000">
					<swiper-item class="nowrap" :class="{'hide':aa}" v-for="(item, index) in notice" :key="index" @click="app.showOpen('index/newsDetails?id='+item.Id)" :item-id="index">
						<view class="w-100 nowrap nt-text font-10" @click="app.showOpen('index/newsDetails?id='+item.Id)" v-text="item.TypeTitle"></view>
					</swiper-item>
				</swiper>
				<uni-notice-bar :scrollable="true" :single="true" :text="notice" :speed="50" v-if="1==3" />
			</view>
			<i class="iconfont icon-you"></i>
		</view> -->
		<view class="w-100 pt-45 pl-15 pr-15" style="position: fixed;z-index: 10;">
			<view class="top-content">
				<view class="w-100 flex-center flex-j-between">
					<view class="user-data font-white">
						<view class="name-cont w-100 flex-center nowrap">
							<!-- <image class="hd" :src="data.user.info.Avatar"  :src="userInfo.Avatar"></image> -->
							<image class="hd" :src="userInfo.Avatar"></image>
							<text class="name nowrap" v-text="data.user.info.NickName">小青的分离</text>
							<view class="msg-cont flex-center" @click="app.showOpen('index/news')">
								<image src="../../static/img/73dda28906f072eb79d2ec5e9bd8fa9.png"></image>
								<text class="num flex-center flex-j-center newlines" v-if="userInfo.notify!=0" v-text="userInfo.notify">1</text>
							</view>
						</view>
						<view class="grade-cont w-100 flex-center flex-j-between nowrap mt-5">
							<text class="font-10" v-text="data.user.level?data.user.level.nickname:'--'">LV.1</text>
							<view class="progress-cont flex-center flex-j-end">
								<view class="val" :style="{'width':+app._accMul(data.user.proportion,100)+'%'}"></view>
								<view class="text op-5 font-10 pl-10 pr-10">{{data.user.current}}/{{data.user.max_current}}斤</view>
							</view>
						</view>
					</view>
					<view class="all-profit">
						<view class="w-100 text-center font-10">仓库剩余花生米</view>
						<view class="w-100 font-12 flex-center flex-j-center font-dark-main nowrap pt-10">
							<image src="../../static/img/9792a1060a1f476387b79c20a6292b8.png"></image>
							{{data.user.balance}}斤
						</view>
					</view>
				</view>
				<view class="notice-cont">
					<image src="../../static/img/fd54b04f438f2eff271e2d96937bcec.png"></image>
					<view class="notice">
						<swiper class="nowrap" :autoplay="true" :circular="true" :vertical="false" :disable-touch="false" :interval="6000">
							<swiper-item class="nowrap" :class="{'hide':aa}" v-for="(item, index) in notice" :key="index" @click="app.showOpen('index/newsDetails?id='+item.Id)" :item-id="index">
								<view class="w-100 nowrap nt-text font-10" @click="app.showOpen('index/newsDetails?id='+item.Id)" v-text="item.TypeTitle"></view>
							</swiper-item>
						</swiper>
						<uni-notice-bar :scrollable="true" :single="true" :text="notice" :speed="50" v-if="1==2" />
					</view>
					<i class="iconfont icon-you"></i>
				</view>
			</view>
		</view>
		<scroll-view class="box" scroll-y="true">
			<view class="farm-content">
				<!-- <view class="w-100 pt-45 pl-15 pr-15">
					<view class="top-content">
						<view class="w-100 flex-center flex-j-between">
							<view class="user-data font-white">
								<view class="name-cont w-100 flex-center nowrap">
									<image class="hd" :src="data.user.info.Avatar"  :src="userInfo.Avatar" v-if="1==3"></image>
									<image class="hd" :src="userInfo.Avatar"></image>
									<text class="name nowrap" v-text="data.user.info.NickName">小青的分离</text>
									<view class="msg-cont flex-center" @click="app.showOpen('index/news')">
										<image src="../../static/img/73dda28906f072eb79d2ec5e9bd8fa9.png"></image>
										<text class="num flex-center flex-j-center newlines" v-if="userInfo.notify!=0" v-text="userInfo.notify">1</text>
									</view>
								</view>
								<view class="grade-cont w-100 flex-center flex-j-between nowrap mt-5">
									<text class="font-10" v-text="data.user.level?data.user.level.nickname:'--'">LV.1</text>
									<view class="progress-cont flex-center flex-j-end">
										<view class="val" :style="{'width':+app._accMul(data.user.proportion,100)+'%'}"></view>
										<view class="text op-5 font-10 pl-10 pr-10">{{data.user.current}}/{{data.user.max_current}}斤</view>
									</view>
								</view>
							</view>
							<view class="all-profit">
								<view class="w-100 text-center font-10">仓库剩余花生米</view>
								<view class="w-100 font-12 flex-center flex-j-center font-dark-main nowrap pt-10">
									<image src="../../static/img/9792a1060a1f476387b79c20a6292b8.png"></image>
									{{data.user.balance}}斤
								</view>
							</view>
						</view>
						<view class="notice-cont">
							<image src="../../static/img/fd54b04f438f2eff271e2d96937bcec.png"></image>
							<view class="notice">
								<swiper class="nowrap" :autoplay="true" :circular="true" :vertical="false" :disable-touch="false" :interval="6000">
									<swiper-item class="nowrap" :class="{'hide':aa}" v-for="(item, index) in notice" :key="index" @click="app.showOpen('index/newsDetails?id='+item.Id)" :item-id="index">
										<view class="w-100 nowrap nt-text font-10" @click="app.showOpen('index/newsDetails?id='+item.Id)" v-text="item.TypeTitle"></view>
									</swiper-item>
								</swiper>
								<uni-notice-bar v-if="1===3" :scrollable="true" :single="true" :text="notice" :speed="50" />
							</view>
							<i class="iconfont icon-you"></i>
						</view>
					</view>
				</view> -->
				<!-- 宠物狗狗 -->
				<view class="my-pet">
					<!-- <image class="pet-sleep" mode="widthFix" src="../../static/img/ba197f5bd4377806e48bdbf9caa4336.png"></image> -->
					<image v-if="data.user.dog" class="pet-sober dog" mode="widthFix" :src="data.user.dog.dog_type.icon"></image>
					<image v-else class="pet-sleep dog" mode="widthFix" src="../../static/img/f23901b799fb4ba06431c48c8ce70d3.gif"></image>
				</view>
				<!-- 逍遥王土地入口 -->
				<view class="newField" @click="app.showOpen('index/newField')">
					<image mode="widthFix" src="../../static/img/d04917c5b9b33cece873be87b9beaf9.png"></image>
				</view>
				<view class="handle-cont">
					<view class="w-100 flex-center flex-j-center">
						<view class="button-cont" @click="dogSW=true;getDogList();">
							<view class="pic">
								<image mode="widthFix" src="../../static/img/26c67bb5bce799c9d472189734322d8.png"></image>
							</view>
							<view class="text text-center font-12">启动防御</view>
						</view>
					</view>
					<!-- <view class="w-100 flex-center flex-j-center mt-30" @click="openAdvert()">
						<view class="button-cont">
							<view class="pic">
								<image mode="widthFix" src="../../static/img/6174526576d0e5ce29792f575e56f82.png"></image>
							</view>
							<view class="text text-center font-12">{{userInfo.isSign?'已签到':'签到'}}</view>
						</view>
					</view> -->
					<view class="w-100 flex-center flex-j-center mt-30">
						<view class="button-cont" @click="ruleSW=true">
							<view class="pic">
								<image mode="widthFix" src="../../static/img/54ad7c5820810581471a2b3b1252ac6.png"></image>
							</view>
							<view class="text text-center font-12">规则</view>
						</view>
					</view>
					<view class="w-100 flex-center flex-j-center mt-30">
						<view class="button-cont button-share" @click="goShare()">
							<view class="pic">
								<image mode="widthFix" src="../../static/img/d313d1f719d7661165f1cb76a660d2d.png"></image>
							</view>
							<view class="text text-center font-12">分享</view>
						</view>
					</view>
				</view>
				<!-- 我的田地 -->
				<view class="my-farm">
					<view class="w-100 fields-list flex-center flex-j-center" v-if="data.sapling[0]">
						<view class="fields-group">
							<image mode="widthFix" class="fields-cont" :src="data.sapling[0].icon"></image>
							<view class="day one-row" v-if="data.sapling[0]"><text>产{{data.sapling[0].release_amount}}  剩{{data.sapling[0].freed}}天</text></view>
						</view>
					</view>
					<view class="w-100 fields-list flex-center flex-j-between pl-15 pr-15" style="margin-top: 0px;">
						<view class="fields-group">
							<image mode="widthFix" v-if="data.sapling[1]" class="fields-cont mr-10" :src="data.sapling[1].icon"></image>
							<view class="day one-row" v-if="data.sapling[1]"><text>产{{data.sapling[1].release_amount}}  剩{{data.sapling[1].freed}}天</text></view>
						</view>
						<view class="fields-group">
							<image mode="widthFix" v-if="data.sapling[2]" class="fields-cont" :src="data.sapling[2].icon"></image>
							<view class="day one-row" v-if="data.sapling[2]"><text>产{{data.sapling[2].release_amount}}  剩{{data.sapling[2].freed}}天</text></view>
						</view>
					</view>
					<view class="w-100 fields-list flex-center flex-j-center" style="margin-top: 0px;">
						<view class="fields-group">
							<image mode="widthFix" v-if="data.sapling[3]" class="fields-cont" :src="data.sapling[3].icon"></image>
							<view class="day one-row" v-if="data.sapling[3]"><text>产{{data.sapling[3].release_amount}}  剩{{data.sapling[3].freed}}天</text></view>
						</view>
					</view>
					<view class="w-100 fields-list flex-center flex-j-center" style="margin-top: 0px;">
						<view class="fields-group">
							<image mode="widthFix" v-if="data.sapling[4]" class="fields-cont mr-10" :src="data.sapling[4].icon"></image>
							<view class="day one-row" v-if="data.sapling[4]"><text>产{{data.sapling[4].release_amount}}  剩{{data.sapling[4].freed}}天</text></view>
						</view>
						<view class="fields-group">
							<image mode="widthFix" v-if="data.sapling[5]" class="fields-cont" :src="data.sapling[5].icon"></image>
							<view class="day one-row" v-if="data.sapling[5]"><text>产{{data.sapling[5].release_amount}}  剩{{data.sapling[5].freed}}天</text></view>
						</view>
					</view>
					<view class="w-100 fields-list flex-center flex-j-center" style="margin-top: 0px;">
						<view class="fields-group">
							<image mode="widthFix" v-if="data.sapling[6]" class="fields-cont" :src="data.sapling[6].icon"></image>
							<view class="day one-row" v-if="data.sapling[6]"><text>产{{data.sapling[6].release_amount}}  剩{{data.sapling[6].freed}}天</text></view>
						</view>
					</view>
					<view class="w-100 fields-list flex-center flex-j-center" style="margin-top: 0px;" v-if="data.sapling[7]">
						<view class="fields-group">
							<image mode="widthFix" class="fields-cont" :src="data.sapling[7].icon"></image>
							<view class="day one-row" v-if="data.sapling[7]"><text>产{{data.sapling[7].release_amount}}  剩{{data.sapling[7].freed}}天</text></view>
						</view>
						<view class="fields-group">
							<image mode="widthFix" style="visibility: hidden;" class="fields-cont ml-10" src="../../static/img/5fb98cb177cf6887eba40d23a8af392.png"></image>
						</view>
					</view>
					<view class="w-100 fields-list flex-center flex-j-center" style="margin-top: 0px;" v-if="data.sapling[8]">
						<view class="fields-group">
							<image mode="widthFix" class="fields-cont" :src="data.sapling[8].icon"></image>
							<view class="day one-row" v-if="data.sapling[8]"><text>产{{data.sapling[8].release_amount}}  剩{{data.sapling[8].freed}}天</text></view>
						</view>
					</view>
				</view>
				<!-- 我的田地 -->
			</view>
			<!-- 我的好友 -->
			<view class="frend-content pl-15 pr-15">
				<view style="width: 100%;position: relative;z-index: 88;">
					<view class="w-100 flex-center flex-j-between nowrap title">
						<view class="flex-center font-14 font-cl-6">
							好友列表<text class="font-10">（{{friendList.length}}位好友）</text>
						</view>
						<view class="font-cl-6 font-10" v-if="friendSW" @click="friendSW=false;getFriend()">返回我的</view>
					</view>
					<scroll-view class="frend" scroll-x="true" scroll-y="true">
						<view class="frend-list">
							<view class="cont-list" v-for="(item,index) in friendList" :key="index">
								<view class="w-100 flex-center flex-j-center"><image :src="item.Avatar?item.Avatar:'../../static/img/headfcs.png'"></image></view>
								<view class="font-10 font-cl-6 pt-5 pb-5 w-100 text-center mt-5" v-text="item.NickName">范鑫鑫</view>
								<view class="w-100 flex-center flex-j-center">
									<view class="steal nowrap" @click="friendID=item.id;getMouseList()">点击偷取</view>
								</view>
							</view>
						</view>
					</scroll-view>
				</view>
			</view>
			<!-- 选择宠物狗子 -->
			<view class="winPopup flex-center flex-j-center pl-20 pr-20" v-if="dogSW">
				<view class="w-100 select-pet-content">
					<view class="w-100 select-pet bc-white br-10">
						<view class="-close font-white" @click="dogSW=false"><i class="iconfont icon-ziyuan"></i></view>
						<view class="title bt_line font-18 font-w-b">选择大黄狗</view>
						<view class="pet-list mt-20">
							<view class="cont-list mb-10" :class="{'active':index==dogIndex}" v-for="(item,index) in dogList" :key="index" @click="dogIndex=index;">
								<view class="store-pic">
									<image mode="widthFix" :src="item.dog_type.icon"></image>
								</view>
								<view class="cont pb-15 pl-10 pr-10">
									<view class="name text-center pb-5 lh-25 nowrap" v-text="item.dog_type.nickname">爱心体验小树苗</view>
									<view class="progress-cont flex-center nowrap font-10">
										<view class="twig-cont mr-5">
											<view :style="{'width': app._accMul(item.proportion,100)+'%'}" class="twig"></view>
										</view>
									</view>
								</view>
							</view>
						</view>
						<view class="flex-center flex-j-center pt-10 pb-25">
							<button class="btn" @click="mouseSW=false;app.showOpenTab('store/store')" v-if="dogList.length==0">暂无狗狗，去购买</button>
							<button class="btn" v-else @click="setDog()">选择确定</button>
						</view>
						<view class="w-100 pb-20 lh-20">
							<view class="w-100 newlines font-14 font-cl-3">
								1.启用大黄狗防御将会减少您的粮食被老鼠偷取成功的几率</br>
								2.越高等级的大黄狗防御能力越大</br>
								3.每次启动防御将减少大黄狗1次防守次数,大黄狗启动防御期间不限防御次数</br>
								4.每只大黄狗防守次数使用完成后将会消失</br>
							</view>
						</view>
					</view>
				</view>
			</view>
			<!-- 选择宠物老鼠 -->
			<view class="winPopup flex-center flex-j-center pl-20 pr-20" v-if="mouseSW">
				<view class="w-100 select-pet-content">
					<view class="w-100 select-pet bc-white br-10">
						<view class="-close font-white" @click="mouseSW=false"><i class="iconfont icon-ziyuan"></i></view>
						<view class="title bt_line font-18 font-w-b">选择小花鼠</view>
						<view class="pet-list mt-20">
							<view class="cont-list mb-10" :class="{'active':mouseIndex==index}" v-for="(item,index) in mouseList" :key="index" @click="mouseIndex=index;">
								<view class="store-pic">
									<image mode="widthFix" :src="item.mouse_type.icon"></image>
								</view>
								<view class="cont pb-15 pl-10 pr-10">
									<view class="name text-center pb-5 lh-25 nowrap" v-text="item.mouse_type.nickname">爱心体验小树苗</view>
									<view class="progress-cont flex-center nowrap font-10">
										<view class="twig-cont mr-5">
											<view :style="{'width': app._accMul(item.proportion,100)+'%'}" class="twig"></view>
										</view>
									</view>
								</view>
							</view>
						</view>
						<view class="flex-center flex-j-center pt-10 pb-25">
							<button class="btn" @click="mouseSW=false;app.showOpenTab('store/store')" v-if="mouseList.length==0">暂无小花鼠，去购买</button>
							<button class="btn" v-else @click="goSteal()">选择确定</button>
						</view>
						<view class="w-100 pb-20 lh-20">
							<view class="w-100 newlines font-14 font-cl-3">
								1.每只花鼠同一时间只能偷取一个人的花田收益</br>
								2.每只花鼠偷取一次过后需要休息一段时间才能再次偷取</br>
								3.每个好友每天只能被同一个人偷取一次</br>
								4.在好友有大黄看守的情况下有可能偷取不到花生米</br>
								5.每次偷取将消耗1次老鼠的偷取次数,次数耗尽则老鼠消失</br>
							</view>
						</view>
					</view>
				</view>
			</view>
			<!-- 规则 -->
			<view class="winPopup flex-center flex-j-center pl-20 pr-20" v-if="ruleSW">
				<view class="grade-content w-100">
					<view class="grade-popup-close font-white" @click="ruleSW=false">
						<i class="iconfont icon-ziyuan"></i>
					</view>
					<view class="content bc-white pl-15 pr-15 pb-30 w-100 br-10">
						<view class="w-100 text-center font-18 bt_line pb-15 pt-15 lh-20 font-grey">{{lang.i15}}</view>
						<view class="w-100 font-12 font-cl-3 lh-20 newlines pt-15">
							<rich-text :nodes="data.other.rule"></rich-text>
						</view>
					</view>
				</view>
			</view>
			<!-- 分享 -->
			<share :show="shareSW" :shareSW.sync="shareSW"></share>
			<!-- 认证中弹窗 -->
			<view class="winPopup" v-if="authSW">
				<authResult :show="authSW" :authSW.sync="authSW"></authResult>
			</view>
			<!-- 升级提示 -->
			<!-- <view class="winPopup" v-if="upgradeSW">
				<upgrade :show="upgradeSW" :data="JSON.stringify(data.user)" :upgradeSW.sync="upgradeSW"></upgrade>
			</view> -->
		</scroll-view>
		<view class="w-100 winPopup flex-center flex-j-center pl-20 pr-20" v-if="ctpop">
			<view class="w-100 bc-white br-4 pt-10 pl-20 pr-20 pb-20 font-black">
				<view class="w-100 text-center pt-15 pb-15 font-c-3 font-16 font-w-b">
					邀请参团
				</view>
				<view class="w-100 font-14 font-c-6 newlines pb-15">
					<!-- 用户{{ctInfo.yqPeoples}}正在邀请您参团，海量奖励等你来拿! -->
					你的好友正在邀请您参团，海量奖励等你来拿!
				</view>
				<view class="w-100 flex-center flex-j-between" style="margin-top: 20px;">
					<view class="yq-btn" @click="ctpop=false">取消</view>
					<view class="yq-btn" @click="goCT()">确定</view>
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
	import share from "@/components/share.vue";
	import reward from "@/components/reward.vue";
	import upgrade from "@/components/upgrade.vue";
	import loader from "@/components/loader/loader.vue";
	import authResult from "@/components/authResult.vue";
	// import advert from "@/common/js/advert.js";
	// import Snotice from "@/components/u-row-notice/u-row-notice.vue"
	import uniNoticeBar from '@/components/uni-notice-bar/uni-notice-bar.vue'
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		components: {
			share,
			reward,
			upgrade,
			authResult,
			loader,
			uniNoticeBar
		},
		data() {
			return {
				aa:false,
				loader:false,
				list:[],
				data:{
					sapling:[],
					user:{
						info:{
							Avatar:"--",
							NickName:"--",
						},
						level:{
							level:"-",
						},
					},
					other:{rule:"--"},
				},
				ruleSW:false,
				shareSW:false,
				authSW:false,
				upgradeSW:false,
				notice:[],
				friendList:[],
				friendAssets:{
					sapling:[],
					user:{
						info:{
							Avatar:"--",
							NickName:"--",
						},
						level:{
							level:"-",
						},
					},
					other:{rule:"--"},
				},
				friendID:"",
				friendSW:false,
				// 耗子
				mouseSW:false,
				mouseList:[],
				mouseIndex:0,
				// 狗子
				dogSW:false,
				dogList:[],
				dogIndex:0,
				Advert:true,
				swiperIndex:0,
				//邀请参团的弹窗
				ctpop:false,
				ctInfo:"",
			}
		},
		onShow() {
			var self=this;
			self.getMyland();
			self.getNotice();
			self.getReward();
			self.setUserInfo();
			self.getFriend();
			if(self.mouseSW){
				self.getMouseList();
			};
			if(self.RewardInfo.is_giveAway==1&&self.userInfo.auth_status==3){
				self.rewardSW=true;
			}else{
				self.rewardSW=false;
			};
			self.getInviteInfo();
		},
		onLoad() {
			
		},
		onPullDownRefresh() {
			var self=this;
			self.getMyland();
			self.getNotice();
			self.getReward();
			self.setUserInfo();
			self.getFriend();
			if(self.mouseSW){
				self.getMouseList();
			};
			if(self.dogSW){
				self.getDogList();
			};
			setTimeout(function () {
				uni.stopPullDownRefresh();
			}, 1000);
		},
		computed:{
			...mapState(['userInfo','assets','RewardInfo','lang']),
		},
		methods: {
			...mapMutations(["setUserInfo","getReward"]),
			changeSwiper:function(e){
				var self=this;
				this.swiperIndex = e.detail.current;
			},
			setNullCP:function(text){
				var osName = plus.os.name;
				if(plus.os.name == "Android") {
					var Context = plus.android.importClass("android.content.Context");
					var main = plus.android.runtimeMainActivity();
					var clip = main.getSystemService(Context.CLIPBOARD_SERVICE);
					plus.android.invoke(clip, "setText", text);
				} else if(osName == "iOS") {
					var UIPasteboard = plus.ios.importClass("UIPasteboard");
					var generalPasteboard = UIPasteboard.generalPasteboard();
					// 设置/获取文本内容:
					generalPasteboard.setValueforPasteboardType(text, "public.utf8-plain-text");
				}
			},
			goCT:function(){
				let self=this;
				self.app.showOpen('Shopping/Shopping?data='+self.ctInfo);
				self.ctpop=false;
			},
			getInviteInfo:function(){
				var self=this;
				uni.getClipboardData({
				    success: function (e) {
						let value;
						try{
							value=e.data;
							let len=value.indexOf("&code=");
							if(value && value.startsWith('฿') && len!=-1){
								let url=config.api + "/shop.found.code";
								let code=value.substr(len+6);
								uni.request({
									url: url,
									data: {
										code:code,
									},
									method: "get",
									header: {Authorization: config.getToken()},
									success: res => {
										console.log(res);
										if(res.data.status==1){
											if(res.data.data){
												res.data.data.yqPeoples=value.user;
												self.ctInfo=JSON.stringify(res.data.data);
												self.ctpop=true;
												self.setNullCP('');
											}
										}else{
											console.log(res);
										};
									},
									fail: (res) => {
										console.log(JSON.stringify(res));
									},
									complete: (res) => {
										
									}
								});
								return;
							}else{
								console.log(value);
							}
						}catch(e){
							console.log(e);
						}
				    }
				});
			},
			openAdvert:function(){
				let self=this;
				if(self.Advert){
					self.Advert=false;
					// advert.gotoRewardVideo(config);
				};
				setTimeout(function(){
					self.Advert=true;
				},2000);
			},
			setDog:function(){
				let self=this;
				self.loader=true;
				let url=config.api + "/user-dog-stand_guard";
				uni.request({
					url: url,
					data: {
						user_dog_id:self.dogList[self.dogIndex].id,
					},
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.dogSW=false;
							self.getMyland();
							self.app._toast(res.data.message);
						}else{
							console.log(res);
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(res);
					},
					complete: (res) => {
						self.loader=false;
					}
				});
			},
			getDogList:function(){
				let self=this;
				self.loader=true;
				let url=config.api + "/user-dog-list";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.dogList=res.data.data;
						}else{
							console.log(res);
							// self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(res);
					},
					complete: (res) => {
						self.loader=false;
					}
				});
			},
			goFriend:function(item){//去好友农场
				let self=this;
				self.friendID=item.id;
			},
			goSteal:function(){//偷取
				let self=this;
				self.getMyland();
				self.loader=true;
				let send={
					mouse_id:self.mouseList[self.mouseIndex].id,
					steal_user_id:self.friendID,
				};
				let url=config.api + "/user-mouse-steal";
				uni.request({
					url: url,
					data: send,
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						self.mouseSW=false;
						if(res.data.status==1){
							if(res.data.data){
								self.data.user.balance=Number(self.data.user.balance) + Number(res.data.data);
							};
							self.app._toast(res.data.message);
							self.getMyland();
						}else{
							self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(res);
					},
					complete: (res) => {
						self.loader=false;
					}
				});
			},
			getMouseList:function(item){
				let self=this;
				self.mouseSW=true;
				self.loader=true;
				let url=config.api + "/user-mouse-list";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.mouseList=res.data.data;
						}else{
							console.log(res);
							// self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(res);
					},
					complete: (res) => {
						self.loader=false;
					}
				});
			},
			getFriend:function(){
				let self=this;
				let url=config.api + "/user-mouse-steal-users";
				uni.request({
					url: url,
					data: {},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						// config.api_status(res);
						if(res.data.status==1){
							self.friendList=res.data.data;
						}else{
							console.log(res);
							// self.app._toast(res.data.message);
						};
					},
					fail: (res) => {
						console.log(res);
					},
					complete: (res) => {}
				});
			},
			getMyland:function(){//获取我的土地
				let self=this;
				let url=config.api + "/sapling-user-home";
				let send={
					type:1
				};
				if(self.friendSW){
					send.user_id=self.friendID;
				};
				// console.log(config.getToken())
				uni.request({
					url: url,
					data: send,
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log("------")
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							if(!self.friendSW){
								self.data=res.data.data;
								if(res.data.data.user.level && res.data.data.user.level.is_pop_up==1){
									self.upgradeSW=true;
								};
							}else{
								self.friendAssets=res.data.data;
							};
						}else{
							console.log("-----");
							console.log(res);
						};
					},
					fail: (res) => {
						console.log(res);
					},
					complete: (res) => {}
				});
			},
			setTabBar:function(){
				var self=this;
				uni.setTabBarItem({
					index: 0,
					text: self.lang.tabBar1,
				});
				uni.setTabBarItem({
					index: 1,
					text: self.lang.tabBar2,
				});
				uni.setTabBarItem({
					index: 2,
					text: self.lang.tabBar3,
				});
				uni.setTabBarItem({
					index: 3,
					text: self.lang.tabBar4,
				});
			},
			goShare:function(){
				var self=this;
				if(self.userInfo.auth_status==1){
					self.authSW=true;
					return;
				};
				if(self.userInfo.auth_status==2){
					self.app.showOpen('index/AuthDetails');
					return;
				};
				if(self.userInfo.auth_status!=3){
					setTimeout(function(){
						self.app.showOpen('auth/auth');
					},500);
					return self.app._toast(self.lang.i19);
				};
				self.shareSW=true;
			},
			getNotice:function(){
				var self=this;
				let url=config.api + "/article-list";
				uni.request({
					url: url,
					data: {
						CallIndex:"notice",
					},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						if(res.data.status==1){
							// let data=[];
							// res.data.data.forEach(function(e){
							// 	data.push(e.TypeTitle)
							// })
							// self.notice=data.toString();
							self.notice=res.data.data;
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
		},
		watch: {
			RewardInfo:function(newValue){
				var self=this;
				if(newValue){
					if(self.RewardInfo.is_giveAway==1&&self.userInfo.auth_status==3){
						self.rewardSW=true;
					}else{
						self.rewardSW=false;
					};
				};
			}
		}
	}
</script>

<style>
	@import url("@/common/css/index.css");
	.yq-btn{
		width: 47%;height: 40px;line-height: 40px;border: 1px solid #FA6C00;color: #FA6C00;
		border-radius: 5px;text-align: center;
	}
	.yq-btn:nth-child(2){
		background-color: #FA6C00;color: #FFFFFF;
	}
	.newField{
		position: absolute;bottom: 15px;right: 100px;
	}
	.newField image{
		width: 60px;height: 60px;
	}
</style>
