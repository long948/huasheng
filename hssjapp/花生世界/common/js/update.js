import config from "@/common/js/config.js"

const check = function(change,type) {
	change =change||function(){};
	plus.runtime.getProperty(plus.runtime.appid, function(info) {
		let url=config.api + "/get.update";
		let send={
			version:info.version,
			device:plus.os.name,
			version_type:'RC',
		};
		// console.log(send);
		uni.request({
			url: url,
			data: send,
			method: "get",
			success: res => {
				// console.log(JSON.stringify(res));
				if (res.data.status == 1) {
					if (res.data.data.update) {
						var buttons = ["更新"];
						plus.nativeUI.confirm(" ", function(ev) {
							if (ev.index == 0) {
								install(res.data.data, change);
							};
						}, "发现新版,立即更新", buttons);
					};
				}else{
					console.log(JSON.stringify(res));
				};
			},
			fail: (res) => {
				if(res.errMsg == 'request:fail timeout'){
					console.log("请求超时了");
				};
				console.log(JSON.stringify(res));
			},
			complete: (res) => {}
		});
	});
}
const install = function(item, change) {
	plus.nativeUI.showWaiting("更新中...");
	let url = item.url;
	if(plus.os.name.toLowerCase() == "ios") {
		if(item.data.NeedInstall) {
			plus.runtime.openURL(url, function() {
				uni.showToast({
					icon: 'none',
					title: "启动外部浏览器错误",
					duration:2000
				});
			});
			return;
		}
	}
	var dtask = plus.downloader.createDownload(url, {},function(d, status) {
		if(status == 200) {
			plus.nativeUI.closeWaiting();
			setTimeout(function() {
				var path = d.filename; //下载apk
				plus.runtime.install(path, {
					force: true
				}, function(res) {
					plus.nativeUI.alert("更新成功,将重启", function() {
						plus.runtime.restart();
					});
				}, function(ttt) {
					// console.log(ttt);
					uni.showToast({
						icon: 'none',
						title: ttt.message,
						duration: 2000
					});
				});
			}, 100);
		} else {
			plus.nativeUI.alert('资源包下载失败:' + status);
		}
	});

	dtask.addEventListener("statechanged", function(download, status) {
		if(change) {
			if(download.downloadedSize < download.totalSize) {
				let perfect = (download.downloadedSize * 100) / download.totalSize;
				change(perfect);
			} else {
				change(100);
			}
		}
	});
	dtask.start();
};

export default{
	check
}