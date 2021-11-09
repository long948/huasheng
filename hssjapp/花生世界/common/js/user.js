import config from "@/common/js/config.js"

// 登录
const login=function(data,success,error){
	// if(data.code.length==0){
	// 	return error(0, "请输入图形验证码！");
	// };
	if(!(data.mobile) || data.mobile.length!=11){
		return error(0, "请输入正确的手机号！");
	};
	if(data.pass.trim().length<8 || data.pass.trim().length>20){
		return error(0, "请输入8-20位数的密码！");
	};
	let url=config.api + "/member-login";
	let send={
		Phone:data.mobile,
		Password:data.pass,
		ClientId:data.ClientId,
		captcha:data.code,
		rand:data.rand,
	};
	// console.log(JSON.stringify(send));
	uni.request({
		url: url,
		data: send,
		method: "post",
		success: res => {
			// console.log(res);
			if(success){
				success(res);
			};
		},
		fail: (res) => {
			console.log(JSON.stringify(res));
			if(error){
				error(0, "登录失败!");
			};
		},
		complete: (res) => {}
	});
}
// 注册
const register=function(data,success,error){
	if(!(data.mobile) || data.mobile.length!=11){
		return error(0, "请输入正确的手机号！");
	};
	if(data.pass.trim().length<8 || data.pass.trim().length>20){
		return error(0, "请输入8-20位数的登录密码！");
	};
	if(data.pass1!=data.pass){
		return error(0, "两次登录密码输入不一致！");
	};
	// if(data.pay.trim().length<8 || data.pay.trim().length>20){
	// 	return error(0, "请输入8-20位数的交易密码！");
	// };
	// if(data.pay1!=data.pay){
	// 	return error(0, "两次交易密码输入不一致！");
	// };
	if(data.code.trim().length!=6){
		return error(0, "请输入6位数的验证码！");
	};
	// if(data.invite.trim().length==0){
	// 	return error(0, "请输入正确的邀请码！");
	// };
	let url=config.api + "/member-register";
	let send={
		Phone:data.mobile,
		Password:data.pass,
		RepeatPassword:data.pass1,
		// PayPassword:data.pay,
		// RepeatPayPassword:data.pay1,
		AuthCode:data.code,
		InviteCode:data.invite,
		ClientId:data.ClientId,
	};
	// console.log(JSON.stringify(send));
	uni.request({
		url: url,
		data: send,
		method: "post",
		success: res => {
			if(success){
				success(res);
			};
		},
		fail: (res) => {
			console.log(JSON.stringify(res));
			if(error){
				error(0, "注册失败!");
			};
		},
		complete: (res) => {}
	});
}
// 忘记登录密码
const forgetLogin=function(data,success,error){
	if(!(data.mobile) || data.mobile.length!=11){
		return error(0, "请输入正确的手机号！");
	};
	if(data.pass.trim().length<8 || data.pass.trim().length>20){
		return error(0, "请输入8-20位数的登录密码！");
	};
	if(data.pass1!=data.pass){
		return error(0, "两次密码输入不一致！");
	};
	if(data.code.trim().length!=6){
		return error(0, "请输入6位数的验证码！");
	};
	let url=config.api + "/member-forget-password";
	let send={
		Phone:data.mobile,
		AuthCode:data.code,
		NewPassword :data.pass,
		RepeatPassword:data.pass1,
	};
	// console.log(JSON.stringify(send));
	uni.request({
		url: url,
		data: send,
		method: "post",
		success: res => {
			if(success){
				success(res);
			};
		},
		fail: (res) => {
			console.log(JSON.stringify(res));
			if(error){
				error(0, "重置密码失败!");
			};
		},
		complete: (res) => {}
	});
}
// 修改登录密码
const modifyLogin=function(data,success,error){
	if(data.old.trim().length==0){
		return error(0, "请输入旧密码！");
	};
	if(data.pass.trim().length<8 || data.pass.trim().length>20){
		return error(0, "请输入8-20位的密码！");
	};
	if(data.pass1!=data.pass){
		return error(0, "两次密码输入不一致！");
	};
	if(data.code.trim().length!=6){
		return error(0, "请输入6位数的验证码！");
	};
	let url=config.api + "/member-modify-password";
	let send={
		OldPassword:data.old,
		Password:data.pass,
		RepeatPassword:data.pass1,
		AuthCode:data.code
	};
	// console.log(JSON.stringify(send));
	uni.request({
		url: url,
		data: send,
		method: "post",
		header: {Authorization: config.getToken()},
		success: res => {
			if(success){
				success(res);
			};
		},
		fail: (res) => {
			console.log(JSON.stringify(res));
			if(error){
				error(res.data.status, "密码修改失败!");
			};
		},
		complete: (res) => {}
	});
}
// 修改交易密码
const modifyPay=function(data,success,error){
	if(data.old.trim().length==0){
		return error(0, "请输入旧密码！");
	};
	if(data.pass.trim().length<8 || data.pass.trim().length>20){
		return error(0, "请输入8-20位的密码！");
	};
	if(data.pass1!=data.pass){
		return error(0, "两次密码输入不一致！");
	};
	if(data.code.trim().length!=6){
		return error(0, "请输入6位数的验证码！");
	};
	let url=config.api + "/member-modify-paypassword";
	let send={
		OldPayPassword:data.old,
		PayPassword:data.pass,
		RepeatPayPassword:data.pass1,
		AuthCode:data.code
	};
	// console.log(JSON.stringify(send));
	uni.request({
		url: url,
		data: send,
		method: "post",
		header: {Authorization: config.getToken()},
		success: res => {
			if(success){
				success(res);
			};
		},
		fail: (res) => {
			console.log(JSON.stringify(res));
			if(error){
				error(res.data.status, "密码修改失败!");
			};
		},
		complete: (res) => {}
	});
}
// 忘记支付密码
const forgetPay=function(data,success,error){
	if(!(data.mobile) || data.mobile.length!=11){
		return error(0, "请输入正确的手机号！");
	};
	if(data.pass.trim().length<8 || data.pass.trim().length>20){
		return error(0, "请输入8-20位数的密码！");
	};
	if(data.pass1!=data.pass){
		return error(0, "两次密码输入不一致！");
	};
	if(data.code.trim().length!=6){
		return error(0, "请输入6位数的验证码！");
	};
	let url=config.api + "/member-forget-paypassword";
	let send={
		Phone:data.mobile,
		AuthCode:data.code,
		NewPayPassword :data.pass,
		RepeatPayPassword:data.pass1,
	};
	// console.log(JSON.stringify(send));
	uni.request({
		url: url,
		data: send,
		method: "post",
		success: res => {
			if(success){
				success(res);
			};
		},
		fail: (res) => {
			console.log(JSON.stringify(res));
			if(error){
				error(0, "重置密码失败!");
			};
		},
		complete: (res) => {}
	});
}
// 设置交易密码
const setPay=function(data,success,error){
	if(data.pass.trim().length<8 || data.pass.trim().length>20){
		return error(0, "请输入8-20位的密码！");
	};
	if(data.pass1!=data.pass){
		return error(0, "两次密码输入不一致！");
	};
	if(data.code.trim().length!=6){
		return error(0, "请输入6位数的验证码！");
	};
	let url=config.api + "/member-set-paypassword";
	let send={
		PayPassword:data.pass,
		RepeatPayPassword:data.pass1,
		AuthCode:data.code
	};
	// console.log(JSON.stringify(send));
	uni.request({
		url: url,
		data: send,
		method: "post",
		header: {Authorization: config.getToken()},
		success: res => {
			if(success){
				success(res);
			};
		},
		fail: (res) => {
			console.log(JSON.stringify(res));
			if(error){
				error(res.data.status, "密码设置失败!");
			};
		},
		complete: (res) => {}
	});
}
const user_Info=function(success,error){
	let url=config.api_service + "/get.user.info";
	// uni.showLoading({title: '加载中'});
	uni.request({
		url: url,
		data: {},
		method: "get",
		header: {Authorization: config.getToken()},
		success: res => {
			// uni.hideLoading();
			if (res.data.code == 200) {
				if(!res.data.data.relation.level_name){
					res.data.data.relation.level_name = '注册用户';
				};
				if(!res.data.data.avatar){
					res.data.data.avatar="../../static/img/ffa5e294541bd80ca4f122b1bca701e.png";
				};
				uni.setStorageSync("userInfo",res.data.data);
				if(success){
					success(res);
				};
			}else{
				if(error){
					error("获取用户信息失败");
				};
			};
			
		},
		fail: (res) => {
			console.log(JSON.stringify(res));
		},
		complete: (res) => {}
	});
}
const _checkPwd = function(pwd) {
	if(!(/^(?![0-9]*$)[a-zA-Z0-9\W]{8,20}$/.test(pwd))) {
		return false;
	} else {
		return true;
	};
};
export default {
	login,
	register,
	forgetLogin,
	forgetPay,
	modifyLogin,
	modifyPay,
	setPay,
	_checkPwd
}
