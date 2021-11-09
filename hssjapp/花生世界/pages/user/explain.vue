<template>
	<view class="box pt-25 pb-25 pl-15 pr-15">
		<view class="newlines font-grey lh-25">
			<rich-text :nodes="details"></rich-text>
		</view>
	</view>
</template>

<script>
	import config from "@/common/js/config.js";
	export default {
		data() {
			return {
				details:"",
			}
		},
		onLoad(e) {
			var self=this;
			self.getData(e.name);
		},
		methods: {
			getData:function(name){
				var self=this;
				let url=config.api + "/article-list";
				uni.request({
					url: url,
					data: {
						CallIndex:name
					},
					method: "get",
					header: {Authorization: config.getToken()},
					success: res => {
						// console.log(JSON.stringify(res));
						config.api_status(res);
						if(res.data.status==1){
							uni.setNavigationBarTitle({							　　title:res.data.data[0].TypeTitle							});							self.details=res.data.data[0].ArticleDetails;
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
	page{background-color: #FFFFFF;}
	.box{border-top: 10px solid #F7F7F7;}
</style>
