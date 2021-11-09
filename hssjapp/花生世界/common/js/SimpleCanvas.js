"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
function addLayer(layer) {
    if (layer.id) {
        toScale(layer, 1 / this.scale);
        this.layers.push(layer);
        if (this[layer.id] === undefined) {
            this[layer.id] = layer;
        }
        else {
            //console.warn('建议id格式为#xxx,以免跟现有属性重复', layer);
        }
    }
    else {
        console.warn('建议给图该图层加上id', layer);
    }
}
function toScale(layer, scale) {
    if (layer) {
        Object.keys(layer).forEach(function (key) {
            if (Object.prototype.toString.call(layer[key]) === '[object Number]') {
                layer[key] = layer[key] * scale;
            }
        });
    }
}
function relativePosition(layer) {
    if (layer.referLayer) {
        var referLayer = this[layer.referLayer.id];
        if (referLayer) {
            var _a = layer.referLayer, top = _a.top, left = _a.left;
            top === undefined ||
                (layer.top = referLayer.top + referLayer.height + top);
            left === undefined ||
                (layer.left = referLayer.left + referLayer.width + left);
        }
        else {
            console.warn("\u6CA1\u6709\u5B9A\u4E49[ " + layer.referLayer.id + " ]\u8FD9\u4E2Aid", layer);
        }
    }
    toScale(layer, this.scale);
    delete layer.referLayer;
    return layer;
}
function getStrLength(str) {
    if (str === void 0) { str = ''; }
    var len = 0;
    for (var i = 0; i < str.length; i += 1) {
        var c = str.charCodeAt(i);
        if ((c >= 0x0001 && c <= 0x007e) || (c >= 0xff60 && c <= 0xff9f)) {
            len += 1;
        }
        else {
            len += 2;
        }
    }
    return Math.ceil(len / 2);
}
var SimpleCanvas = (function () {
    function SimpleCanvas(_a) {
        var _b = _a.scale, scale = _b === void 0 ? 1 : _b, canvasId = _a.canvasId;
        this.scale = 1;
        this.layers = [];
        var ctx = uni.createCanvasContext(canvasId);
        this.scale = scale;
        this.ctx = ctx;
        this.canvasId = canvasId;
    }
    SimpleCanvas.prototype.getAutoCanvasHeight = function () {
        return Math.max.apply(Math, this.layers.map(function (layer) {
            var _a = layer.top, top = _a === void 0 ? 0 : _a, _b = layer.height, height = _b === void 0 ? 0 : _b, type = layer.type;
            if (type === 'artboard') {
                return 0;
            }
            return top + height;
        }));
    };
    //创建画板
	SimpleCanvas.prototype.createArtboard = function (layer) {
		var ctx = this.ctx;
        var _a = layer.backgroundColor, backgroundColor = _a === void 0 ? '#cccccc' : _a, width = layer.width, height = layer.height;
        ctx.setFillStyle(backgroundColor);
        ctx.fillRect(0, 0, width, height);
        ctx.fill();
        layer.type = 'artboard';
        addLayer.call(this, layer);
        return this;
    };
    //绘制直角矩形
	SimpleCanvas.prototype.createRectangle = function (layer) {
        var _a = relativePosition.call(this, layer), left = _a.left, top = _a.top, _b = _a.backgroundColor, backgroundColor = _b === void 0 ? '#cccccc' : _b, width = _a.width, height = _a.height;
        var ctx = this.ctx;
        ctx.setFillStyle(backgroundColor);
        ctx.fillRect(left, top, width, height);
        layer.type = 'rectangle';
        addLayer.call(this, layer);
        return this;
    };
	//绘制圆角矩形
	SimpleCanvas.prototype.createCircleRectangle = function (layer) {
	    var _a = relativePosition.call(this, layer), left = _a.left, top = _a.top, _b = _a.backgroundColor, backgroundColor = _b === void 0 ? '#cccccc' : _b, width = _a.width, height = _a.height, borderRadius = _a.borderRadius;
	    var ctx = this.ctx;
		var x = left , y = top , r = borderRadius;
		ctx.save();
		ctx.beginPath();
	    ctx.setFillStyle('transparent');
		 // 左上角
		ctx.arc(x + r, y + r, r, Math.PI, Math.PI * 1.5)
		// border-top
		ctx.moveTo(x + r, y);
		ctx.lineTo(x + width - r, y);
		ctx.lineTo(x + width, y + r);
		// 右上角
		ctx.arc(x + width - r, y + r, r, Math.PI * 1.5, Math.PI * 2);
		// border-right
		ctx.lineTo(x + width, y + height - r);
		ctx.lineTo(x + width - r, y + height);
		// 右下角
		 ctx.arc(x + width - r, y + height - r, r, 0, Math.PI * 0.5);
		// border-bottom
		ctx.lineTo(x + r, y + height);
		ctx.lineTo(x, y + height - r);
		// 左下角
		ctx.arc(x + r, y + height - r, r, Math.PI * 0.5, Math.PI);
		// border-left
		ctx.lineTo(x, y + r)
		ctx.lineTo(x + r, y)
		ctx.fill();
		ctx.closePath();
		// 剪切
		ctx.clip();
		ctx.setFillStyle(backgroundColor);
	    ctx.fillRect(left, top, width, height);
		ctx.restore();
	    layer.type = 'circleRectangle';
	    addLayer.call(this, layer);
	    return this;
	};
    //绘制图片
	SimpleCanvas.prototype.drawImage = function (layer) {
        var _a = relativePosition.call(this, layer), left = _a.left, top = _a.top, path = _a.path, width = _a.width, height = _a.height;
        var ctx = this.ctx;
        ctx.drawImage(path, left, top, width, height);
        layer.type = 'image';
        addLayer.call(this, layer);
        return this;
    };
    //绘制圆形图片
	SimpleCanvas.prototype.drawCircleImage = function (layer) {
        var _a = relativePosition.call(this, layer), left = _a.left, top = _a.top, path = _a.path, _b = _a.d, d = _b === void 0 ? 0 : _b;
        var ctx = this.ctx;
        ctx.save();
        ctx.beginPath();
        var r = d / 2;
        var cx = left + r;
        var cy = top + r;
        ctx.arc(cx, cy, r, 0, Math.PI * 2, false);
        ctx.clip();
		ctx.stroke();
        ctx.drawImage(path, left, top, d, d);
        ctx.restore();
        layer.type = 'circleImage';
        layer.height = layer.d;
        layer.width = layer.d;
        addLayer.call(this, layer);
        return this;
    };
    //绘制文本
	SimpleCanvas.prototype.drawWrapText = function (layer) {
        var _a = relativePosition.call(this, layer), _b = _a.left, left = _b === void 0 ? 0 : _b, _c = _a.top, top = _c === void 0 ? 0 : _c, _d = _a.text, text = _d === void 0 ? '' : _d, _e = _a.fontSize, fontSize = _e === void 0 ? 12 : _e, _f = _a.width, width = _f === void 0 ? 300 : _f, _g = _a.lineHeight, lineHeight = _g === void 0 ? 1 : _g, _h = _a.color, color = _h === void 0 ? '#333333' : _h , _t = _a.textAlign, textAlign = _t === void 0 ? 'left' : _t, _j = _a.fontWeight, fontWeight = _j === void 0 ? 'normal' : _j, _k = _a.fontStyle, fontStyle = _k === void 0 ? 'normal' : _k;
        var ctx = this.ctx;
        var chr = text.split('');
        var temp = '';
        var row = [];
		ctx.font = `${fontStyle} ${fontWeight} ${fontSize}px sans-serif`;	
		ctx.setTextAlign(textAlign);
        ctx.setFontSize(fontSize);
		let meWidth = 15;
        for (var a = 0; a < chr.length; a++) {
            if (meWidth < width) {
                temp += chr[a];
				meWidth += 15;
            } else {
                a -= 1;
                row.push(temp);
                temp = '';
				meWidth = 0;
            }
        }
        row.push(temp);
        var textTop;
        for (var b = 0; b < row.length; b += 1) {
            textTop = top + (fontSize + (b * fontSize + b * lineHeight));
            ctx.setFillStyle(color);
            ctx.setFontSize(fontSize);
            ctx.fillText(row[b], left, textTop);
        }
        layer.type = 'wrapText';
        layer.height = textTop - top;
        addLayer.call(this, layer);
        return this;
    };
    SimpleCanvas.prototype.draw = function (complete) {
        var ctx = this.ctx;
        ctx.draw(false, function () {
            complete && complete();
        });
    };
    SimpleCanvas.textHeight = function (_a) {
        var _b = _a.text, text = _b === void 0 ? '' : _b, _c = _a.width, width = _c === void 0 ? 0 : _c, _d = _a.lineHeight, lineHeight = _d === void 0 ? 0 : _d, _e = _a.fontSize, fontSize = _e === void 0 ? 12 : _e, _f = _a.scale, scale = _f === void 0 ? 1 : _f;
        var textLength = getStrLength(text);
        var textWidth = textLength * fontSize;
        var textRowNum = Math.ceil(textWidth / width);
        return textRowNum * (lineHeight + fontSize) * scale - lineHeight;
    };
    return SimpleCanvas;
}());
exports.default = SimpleCanvas;
