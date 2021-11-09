const call = function() {

}
var wv = null;
/** 初始化拖动窗口
 * @param {Object} 回调函数
 */
const init = function(callback) {
	callback = callback || call;
	wv = plus.webview.getWebviewById("webview-captcha");
	if (!wv) {
		wv = plus.webview.create("/hybrid/html/local.html", "webview-captcha", {
			plusrequire: "none", //禁止远程网页使用plus的API，有些使用mui制作的网页可能会监听plus.key，造成关闭页面混乱，可以通过这种方式禁止
			'uni-app': 'none', //不加载uni-app渲染层框架，避免样式冲突
			top: 0, //放置在titleNView下方。如果还想在webview上方加个地址栏的什么的，可以继续降低TOP值	
			'animationOptimization': 'none',
			'popGesture': 'hide',
			'userSelect': 'true',
			'background': 'transparent',
			"softinputMode": "adjustResize",
			'render': 'always',
			'plusrequire': 'ahead'
		})
		wv.evalJS("init('2077072127')");
	}
	let close = function() {
		var json = JSON.parse(plus.storage.getItem("cache:captcha"));
		try {
			callback(json);
		} catch (e) {
			//TODO handle the exception
		}
	}
	wv.removeEventListener("close", close);
	wv.removeEventListener("hide", close);
	wv.addEventListener("close", close);
	wv.addEventListener("hide", close);

}

const hide = function() {
	if (wv) {
		if (wv.isVisible()) {
			wv.hide();
			return true;
		}
	}
	return false;
}

const show = function() {
	if (wv) { 
		wv.show();
	}
}
export default {
	init,
	show,
	hide
}
