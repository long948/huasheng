<template>
	<view class="box">
		<view class="w-100 bc-white pt-10 pl-15 pr-15">
			<view class="contInput bt_line font-black">
				<input type="text" :value="lang.bdph13+'：'+userInfo.NickName" disabled="" />
			</view>
			<view class="contInput bt_line">
				<input type="text" :placeholder="lang.bdph14" maxlength="30" v-model="name" />
			</view>
			<!-- <view class="contInput bt_line">
				<input type="text" placeholder="请再次输入昵称" maxlength="30" v-model="name1" />
			</view> -->
		</view>
		<view class="btn-cont pl-15 pr-15 pt-50 pb-50">
			<button class="btn" :disabled="!modify_status" @click="modifyName()">{{lang.bdph15}}</button>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js"
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		data() {
			return {
				name:"",
				name1:"",
				modify_status:true,
			}
		},
		onLoad() {
			var self = this;
			uni.setNavigationBarTitle({
				title:self.lang.userInfo2
			});
		},
		computed:{
			...mapState(['userInfo','qiniu','lang']),
		},
		methods: {
			...mapMutations(["setUserInfo"]),
			modifyName:function(){
				var self=this;
				if(self.name.trim().length==0){
					return self.app._toast(self.lang.bdph14);
				};
				// if(self.name!==self.name1){
				// 	return self.app._toast("两次输入昵称不一致");
				// };
				let url=config.api + "/member-modify-nick";
				let send={
					NickName:self.name,
				};
				self.modify_status=false;
				uni.request({
					url: url,
					data: send,
					method: "post",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							self.setUserInfo();
							self.app._toastIcon(self.lang.bdph16);
						};
					},
					fail: (res) => {
						self.app._toast(self.lang.bdph17);
						console.log(JSON.stringify(res));
					},
					complete: (res) => {
						self.modify_status=true;
						self.name="";
						self.name1="";
					}
				});
			}
		}
	}
</script>

<style>
	page{background-color: #FFFFFF;}
	.contInput{width: 100%;height: 50px;}
	.contInput input{width: 100%;height: 100%;color: #333333;font-size: 14px;padding: 0px 20px;}
	button.btn{background: linear-gradient(bottom,#FA6C00,#FFB500);}
</style>
