// 上传
const upload1 = function(data,success,error){
	uni.chooseImage({
		count: 1,
		success: function(res) {
			const tempFilePaths = res.tempFilePaths;
			const url=tempFilePaths[0];
			var task = plus.uploader.createUpload('https://up-z'+data.region+'.qiniup.com', {
				method: "post"
			},function(res, code) {
				// console.log(JSON.stringify(res));
				if (code == 200) {
					let data=JSON.parse(res.responseText);
					data.url=url;
					success(data);
				} else {
					console.log(JSON.stringify(res));
					error("上传失败");
				};
			});
			var uid = Math.floor(Math.random() * 100000000 + 10000000).toString();
			var type = uid + url.substring(url.lastIndexOf("."), url.length);
			task.addData("key", type);
			task.addData("token", data.token);
			task.addFile(url, {
				"key": "file",
				"name": uid + "." + type
			});
			task.start();
		},
		error: function(res) {
			console.log(res);
		}
	});
};
const upload2 = function(data,success,error){
	uni.chooseImage({
		count: 1,
		sourceType:["camera"],
		success: function(res) {
			const tempFilePaths = res.tempFilePaths;
			uni.uploadFile({
				url:'https://up-z'+data.region+'.qiniup.com', //仅为示例，非真实的接口地址
				filePath: tempFilePaths[0],
				name: 'file',
				formData: {
					'token': data.token
				},
				success: (res) => {
					// console.log(JSON.stringify(res));
					if(res.statusCode == 200){
						let data = res.data;
						if (typeof data == "string") {
							data = JSON.parse(data);
							data.url=tempFilePaths[0];
							success(data);
						};
					}else{
						error("上传失败");
					};
				},
				fail: (res) => {
					console.log(res);
					error("上传失败");
				},
			});
		},
		fail:function(res){
			// console.log(res);
		}
	});
};
const upload3 = function(data,success,error){
	uni.chooseImage({
		count: 1,
		// sourceType:["camera"],
		success: function(res) {
			const tempFilePaths = res.tempFilePaths;
			uni.uploadFile({
				url:'https://up-z'+data.region+'.qiniup.com', //仅为示例，非真实的接口地址
				filePath: tempFilePaths[0],
				name: 'file',
				formData: {
					'token': data.token
				},
				success: (res) => {
					// console.log(JSON.stringify(res));
					if(res.statusCode == 200){
						let data = res.data;
						if (typeof data == "string") {
							data = JSON.parse(data);
							data.url=tempFilePaths[0];
							success(data);
						};
					}else{
						error("上传失败");
					};
				},
				fail: (res) => {
					console.log(res);
					error("上传失败");
				},
			});
		},
		fail:function(res){
			// console.log(res);
		}
	});
};
export default {
	upload1,
	upload2,
	upload3,
};