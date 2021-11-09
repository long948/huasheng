const socket = {
	actions: {},
	connect: function() {
		console.log("连接成功");
	},
	error: function() {
		console.error("连接出错错误");
	},
	error: function() {
		console.error("连接成功");
	}
};

/**
 * Socket通知消息
 * @param {String} action
 * @param {Function} fun
 */
const on = function(action, fun) {
	action = action || "";
	
	//socket.send("{action:subscript,data:'ctc',id:1}")
	
	if (action && fun) {
		socket.actions[action] = {
			"fun": fun,
		};
	}
}

/**
 * 初始化Sokcet
 * @param {String} 服务地址,ws://,wss://
 * @param {Function} 连接成功
 * @param {Function} 连接错误
 * @param {Function} 连接关闭
 */
const init = function(server, connect, error, close) {
	if (server.indexOf("ws") != 0) {
		throw "请填正确的服务器地址";
		return;
	}
	var self = this;
	uni.connectSocket({
		url: server
	});

	connect = connect || socket.connect;
	error = error || socket.error;
	close = close || socket.close;

	uni.onSocketOpen(function(res) {
		connect();
	});
	uni.onSocketClose(function(e) {
		close(e)
	})
	uni.onSocketError(function(e) {
		error(e)
		setTimeout(function(){
			uni.connectSocket({
				url: server
			});
		},500);
	});
	uni.onSocketMessage(function(event) {
		try {
			var data =JSON.parse(event.data) ;
			if (data.action) { 
				if (socket.actions[data.action]) {
					socket.actions[data.action].fun(data.data);
				}
			}
		} catch (e) {
			console.error(e);
		}
	})
}

/**
 * 向服务器推送消息
 * @param {String} action
 * @param {String} data
 */
const emit = function(action, data) {
	uni.sendSocketMessage({
		data: JSON.stringify({
			action: action,
			data: data
		})
	})
}
export default {
	on,
	emit,
	init
}
