<template>
	<view class="box pl-25 pr-25 pt-15 pb-15">
		<view class="font-14 font-cl-3 mb-5">{{lang.at1}}</view>
		<view class="w-100 flex-center flex-j-between">
			<view class="pic" @click="uploadAvatar()">
				<image mode="widthFix" :src="auth_z" v-if="auth_z"></image>
				<view class="text-center" v-else>
					<i class="iconfont icon-jia"></i>
					<view class="w-100 font-12 font-cl-3 op-5 text-center pt-10">{{lang.at2}}</view>
				</view>
			</view>
		</view>
		<view class="font-14 font-cl-3 pt-20 pb-10">{{lang.at3}}</view>
		<input class="font-14 font-cl-3" type="text" v-model="name" />
		<view class="font-14 font-cl-3 pt-20 pb-10">{{lang.at4}}</view>
		<input class="font-14 font-cl-3" type="text" v-model="id" />
		<view class="btn-cont flex-center flex-j-between pt-30 pb-30">
			<button class="btn w-100" @click="go_auth('auth2')">{{lang.at5}}</button>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	import upload from "@/common/js/upload.js"
	import {
		mapState,
		mapMutations
	} from 'vuex'
	export default {
		data() {
			return {
				name:"",
				id:"",
				auth_z:"",
				key_z:"",
				auth_f:"",
				key_f:"",
			}
		},
		onLoad() {
			
		},
		computed:{
			...mapState(['userInfo','qiniu','lang']),
		},
		methods: {
			uploadAvatar:function(){
				var self=this;
				upload.upload2(self.qiniu,function(e){
					self.auth_z=e.url;
					self.key_z=e.key;
				},function(res){
					self.app._toast(res);
				});
			},
			go_auth:function(auth){
				var self=this;
				if(self.name.trim().length==0){
					return self.app._toast("请输入姓名！");
				};
				if(self.id.trim().length==0){
					return self.app._toast("请输入身份证号码！");
				};
				if(self.key_z.length==0){
					return self.app._toast("请上传身份证");
				};
				// if(self.key_f.length==0){
				// 	return self.app._toast("请上传背面照片！");
				// };
				let send={
					Name:self.name,
					IdCard:self.id,
					front_image:self.key_z,
					// reverse_image:self.key_f,
				};
				self.app.showOpen('auth/auth1?data='+JSON.stringify(send));
				
				// if(auth=='auth1'){
				// 	// self.app.showOpen('auth/auth1?data='+encodeURIComponent(JSON.stringify(send)));
				// 	self.app.showOpen('auth/auth1?data='+JSON.stringify(send));
				// }else if(auth=='auth2'){
				// 	self.app.showOpen('auth/auth2?data='+JSON.stringify(send));
				// };
			},
		}
	}
</script>

<style>
	page{background-color: #FFFFFF;}
	.box{border-top: 10px solid #F7F7F7;}
	input{width: 100%;height: 44px;background-color: #F9F9F9;border-radius: 4px;padding: 0px 15px;}
	.pic{width: 100%;min-height: 225px;border-radius: 4px;background-color: #F1F0F0;display: flex;align-items: center;justify-content: center;}
	.pic .iconfont{font-size: 35px;color: #FFFFFF;}
	.pic image{width: 100%;}
	.btn-cont{width: 100%;}
	.btn-cont button.btn{width: 48%;height: 40px;line-height: 40px;border-radius: 4px;color: #FFFFFF;font-size: 14px;background: #FD9253;}
	/* .btn-cont button.btn:nth-child(1){background: #519B08;}
	.btn-cont button.btn:nth-child(2){background: #FD9253;} */
</style>
