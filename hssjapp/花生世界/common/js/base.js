module.exports = {
	// shareURL:"https://sapp.dierna.com/Q7vY?code=",
	shareURL:"https://h5.hssjpnt.top/?code=",
	_getCache(name){//获取缓存
		return uni.getStorageSync(name);
	},
	_setCache(name,value){//设置缓存
		uni.setStorageSync(name,value);
	},
	_toast(msg){//自动消失的提示窗
		uni.showToast({
			icon: 'none',
			title: msg,
			duration: 2000
		});
	},
	_toastIcon(msg){ //带有勾的自动消失的提示窗
		uni.showToast({
		    title: msg,  
		    mask: false,  
		    duration: 1500  
		});  
	},
	showOpen(address){//打开新窗口
		var init=true;
		if(init){
			init=false;
			uni.navigateTo({
				url:"/pages/"+address
			});
		}else{
			return;
		};
		setTimeout(function(){
			init=true;
		},3000);
	},
	closeMeOpen(address){//关闭当前页面打开新窗口
		var init=true;
		if(init){
			init=false;
			uni.redirectTo({
				url:"/pages/"+address
			});
		}else{
			return;
		};
		setTimeout(function(){
			init=true;
		},3000);
	},
	closeOpen(address){//关闭所有页面打开新窗口
		var init=true;
		if(init){
			init=false;
			uni.reLaunch({
				url:"/pages/"+address
			});
		}else{
			return;
		};
		setTimeout(function(){
			init=true;
		},3000);
	},
	showOpenTab(address){//跳转到tabBar 
		uni.switchTab({
			url:"/pages/"+address
		});
	},
	goBack(num){//返回上一页
		if(num){
			uni.navigateBack({
				delta: num
			});
		}else{
			uni.navigateBack({
				delta: 1
			});
		};
	},
	_prePage(){ //获取上一个页面
		let pages = getCurrentPages();
		let prePage = pages[pages.length - 2];
		return prePage;
	},
	_toFixed(str1,num){//小数截取
		var str = String(str1);
		function hanZeo(z) {
			var zeo = '';
			for(var i = 0; i < z; i++) {
				zeo += '0'; 
			}
			return zeo;
		};
		var arr = str.split('.');
		if(arr[1]) {
			if(arr[1].length >= num) {
				return arr[0] + '.' + arr[1].slice(0, num);
			} else {
				return arr[0] + '.' + arr[1] + hanZeo(num - arr[1].length);
			}
		} else {
			return str + '.' + hanZeo(num);
		}
	},
	_phoneMethod(cellValue){ //电话号码中间用*代替
		if (Number(cellValue) && String(cellValue).length === 11) {
	        var mobile = String(cellValue);
	        var reg = /^(\d{3})\d{4}(\d{4})$/;
	        return mobile.replace(reg, '$1****$2');
	    } else {
	        return cellValue;
	    }
	},
	checkMobile(sMobile) {//判断手机号码的正则表达式
		if(!(/^1[3|4|5|6|7|8|9][0-9]\d{8}$/.test(sMobile))) {
			return false;
		} else {
			return true;
		};
	},
	_checkPwd(pwd) {//验证密码，必须8-20字母和数字组成
		if(!(/^[0-9A-Za-z]{8,20}$/.test(pwd))) {
			return false;
		} else {
			return true;
		}
	},
	_formatDate(inputTime){ //时间戳格式化
		var date = new Date(parseInt(inputTime * 1000));
		var y = date.getFullYear();
		var m = date.getMonth() + 1;
		m = m < 10 ? ('0' + m) : m;
		var d = date.getDate();
		d = d < 10 ? ('0' + d) : d;
		var h = date.getHours();
		h = h < 10 ? ('0' + h) : h;
		var minute = date.getMinutes();
		var second = date.getSeconds();
		minute = minute < 10 ? ('0' + minute) : minute;
		second = second < 10 ? ('0' + second) : second;
		return y + '-' + m + '-' + d +' ' + h + ':' + minute + ':' + second;
	},
	_accMul(arg1, arg2) { //乘法
		if(arg1 == 0 || !arg1){
			return "0";
		};
		if(arg2 == 0 || !arg2){
			return "0";
		};
		var m = 0,
			s1 = arg1.toString(),
			s2 = arg2.toString();
		try {
			m += s1.split(".")[1].length
		} catch(e) {}
		try {
			m += s2.split(".")[1].length
		} catch(e) {}
		return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m)
	},
	_accDiv(arg1, arg2) { //除法
		if(arg1 == 0 || !arg1){
			return "0";
		};
		if(arg2 == 0 || !arg2){
			return "0";
		};
		var t1=0,t2=0,r1,r2;
		try{t1=arg1.toString().split(".")[1].length}catch(e){}
		try{t2=arg2.toString().split(".")[1].length}catch(e){}
		r1 = Number(arg1.toString().replace(".",""));
		r2 = Number(arg2.toString().replace(".",""));
		return (r1/r2) * Math.pow(10,t2-t1);
	},
	toNonExponential(num) {//处理科学计数的问题
	    if (!num) {
	        return 0;
	    };
	    num = parseFloat(num);
	    if (isNaN(num)) {
	        return 0;
	    };
	    let m = num.toExponential().match(/\d(?:\.(\d*))?e([+-]\d+)/);
	    return num.toFixed(Math.max(0, (m[1] || '').length - m[2]));
	},
	_copy(text) {//复制文字
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
		this._toast("复制成功");
	},
	_paste(){//粘贴
		let address;
		uni.getClipboardData({
			success: function(res) {
				address=res.data;
				console.log(address)
			}
		});
	},
	_networkMethod(){ //网络监听
		uni.getNetworkType({
			success(res) {
				if(res.networkType == 'none'){
					uni.showModal({
						title: "提示",
						content: "您当前处于无网络状态，请链接网络后操作",
						showCancel: false,
						confirmText: "确定"
					})
				}
			}
		});
		uni.onNetworkStatusChange(function (res) {
			if(!res.isConnected){
				uni.showModal({
					title: "提示",
					content: "您当前处于无网络状态，请链接网络后操作",
					showCancel: false,
					confirmText: "确定"
				})
			}
		});
	},
};