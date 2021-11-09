import config from "@/common/js/config.js"
import base from "@/common/js/base.js"
/*获取存储缓存中的币种*/
const getCoin = function() {
	var coin = uni.getStorageSync("coinList") || [];
	return coin;
}
/*获取全部币种列表*/
const Init=function(success,error){
	let url=config.api + "/coin-list";
	uni.request({
		url: url,
		data: {},
		method: "get",
		header: {Authorization: config.getToken()},
		success: res => {
			// console.log(JSON.stringify(res));
			config.api_status(res);
			if (res.data.status == 1) {
				uni.setStorageSync("coinList", res.data.data);
			}else{
				console.log(JSON.stringify(res));
				if(error){
					error(0, res.data.message);
				};
			};
		},
		fail: (res) => {
			console.log(JSON.stringify(res));
		},
		complete: (res) => {}
	});
}
//获取币种价格
const getPrice=function(success,error){
	let url=config.api.coin + "/Coin/Prices";
	uni.request({
		url: url,
		data: {},
		method: "post",
		header: {Authorization: config.getToken()},
		success: res => {
			// console.log(JSON.stringify(res));
			if (res.data.status == 1) {
				uni.setStorageSync("coinPrices", res.data.data);
				let coinlist=uni.getStorageSync("coinList");
				res.data.data.forEach(function(item){
					coinlist.forEach(function(jtem){
						if(jtem.EnName.toUpperCase()==item.Name.toUpperCase()){
							jtem.Price=item.Price;
						};
					});
				});
				uni.setStorageSync("coinList", coinlist);
				if(success){
					success(coinlist);
				};
			}else{
				// console.log(JSON.stringify(res));
				if(error){
					error(res.data.code, res.data.message);
				};
			};
		},
		fail: (res) => {
			console.log(JSON.stringify(res));
		},
		complete: (res) => {}
	});
}
//获取币种余额
const getBalance=function(success,error){
	let url=config.api + "/coin-balance";
	uni.request({
		url: url,
		data: {},
		method: "get",
		header: {Authorization: config.getToken()},
		success: res => {
			console.log(JSON.stringify(res));
			if (res.data.status == 1) {
				if(success){
					success(res);
				};
			}else{
				console.log(JSON.stringify(res));
				if(error){
					error(res.data.code, res.data.message);
				};
			};
		},
		fail: (res) => {
			console.log(JSON.stringify(res));
		},
		complete: (res) => {}
	});
}
/*获取币种USDT价格*/
const getUsdt = function(name) {
	if (!name) return 0;
	if (name.toUpperCase() == "USDT") {
		return 1;
	} else {
		let list = uni.getStorageSync("coinPrices") || [] ;
		let result = list.filter(function(a) {
			return a.Name.toUpperCase() == name.toUpperCase();
		});
		if (result && result.length > 0) {
			return result[0].Price;
		};
		return 0;
	};
}
/*获取币种的人民币价格*/
const getCny = function(name) {
	if (!name) return 0;
	if(name.toUpperCase() == "USDT"){
		let list = uni.getStorageSync("coinList") || [] ;
		let result = list.filter(function(a) {
			return a.EnName.toUpperCase() == "USDT".toUpperCase();
		});
		if (result && result.length > 0) {
			return result[0].Price;
		};
		return 7;
	}else{
		return base._accMul( getCny("USDT"),getUsdt(name) );
	};
}
/*获取单个币种的余额*/
const getMoney = function(name){
	if (!name) {
		return 0.00;
	};
	let list =uni.getStorageSync("allCoin") || [] ;
	let data=list.filter(function(item){
		return item.en_name.toLowerCase() == name.toLowerCase()
	});
	if(data.length!=0){
		if(data[0].other){
			return data[0].other.Money;
		}else{
			return 0.00;
		};
	}else{
		return 0.00;
	};
}
const getlist=function(){
	return new Promise(function(resolve,reject){
		uni.request({
			url: config.api + "/coin-list",
			data: {},
			method: "get",
			header: {Authorization: config.getToken()},
			success: res => {
				// console.log(JSON.stringify(res));
				if (res.data.status == 1) {
					resolve(res.data.data);
				}else{
					console.log(res);
				};
			},
			fail: (res) => {
				console.log(res);
				reject(res);
			},
			complete: (res) => {}
		});
	})
}
export default {
	// getCoin,
	// Init,
	// getPrice,
	// getBalance,
	// getUsdt,
	// getCny,
	getlist,
}
