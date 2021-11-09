import config from "@/common/js/config.js"

// 发送注册验证码
const registerSMS=function(data,success,error){
	if(!(data.mobile) || data.mobile.length!=11){
		return error(0, "请输入正确的手机号！");
	};
	let url=config.api + "/sms-register-code";
	let send={
		Phone:data.mobile,
		ticket:data.ticket,
		randstr:data.randstr,
	};
	console.log(JSON.stringify(send));
	uni.request({
		url: url,
		data: send,
		method: "post",
		success: res => {
			console.log(res);
			if(success){
				success(res);
			};
		},
		fail: (res) => {
			console.log(JSON.stringify(res));
			if(error){
				error(0, "发送失败!");
			};
		},
		complete: (res) => {}
	});
};
// 发送忘记登录验证码
const forgetSMS=function(data,success,error){
	if(!(data.mobile) || data.mobile.length!=11){
		return error(0, "请输入正确的手机号！");
	};
	let url=config.api + "/sms-modify-pass";
	let send={
		Phone:data.mobile,
		ticket:data.ticket,		randstr:data.randstr,
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
				error(0, "发送失败!");
			};
		},
		complete: (res) => {}
	});
};
// 发送忘记登录验证码
const forgetPaySMS=function(data,success,error){
	if(!(data.mobile) || data.mobile.length!=11){
		return error(0, "请输入正确的手机号！");
	};
	let url=config.api + "/sms-modify-paypass";
	let send={
		Phone:data.mobile,
		ticket:data.ticket,
		randstr:data.randstr,
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
				error(0, "发送失败!");
			};
		},
		complete: (res) => {}
	});
};
// 发送验证码(修改密码)
const modifySMS=function(data,success,error){
	// if(!(data.mobile) || data.mobile.length!=11){
	// 	return error(0, "请输入正确的手机号！");
	// };
	let url=config.api + "/sms-vcode";
	let send={
		// Phone:data.mobile,
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
				error(0, "发送失败!");
			};
		},
		complete: (res) => {}
	});
};
// 发送验证码(修改密码)
const setPaySMS=function(data,success,error){
	// if(!(data.mobile) || data.mobile.length!=11){
	// 	return error(0, "请输入正确的手机号！");
	// };
	let url=config.api + "/sms-setpaypass-code";
	let send={
		// Phone:data.mobile,
		ticket:data.ticket,
		randstr:data.randstr,
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
				error(0, "发送失败!");
			};
		},
		complete: (res) => {}
	});
};
export default {
	registerSMS,
	forgetSMS,
	forgetPaySMS,
	modifySMS,
	setPaySMS,
}
