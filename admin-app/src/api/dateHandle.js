import request from '@/utils/request'

// 添加或修改商品分类
export function qiniuToken(query) {
  return request({
    url: 'qiniu-token',
    method: 'get',
    params: query
  })
}
export function qiuNiuUpToken() {
  return request({
    url: '/upload/getToken',
    method: 'get'
  })
}
// 上传文件
export function createFile(url, formdata) {
  return request({
    url: url,
    method: 'post',
    data: formdata
  })
}

export function dateHandle(str, t) {
  if (!t) return ''
  var d = new Date()
  d.setTime(t * 1000)

  var _m = d.getMonth() + 1
  var _d = d.getDate()
  var _H = d.getHours()
  var _i = d.getMinutes()
  var _s = d.getSeconds()

  var format = {
    'Y': d.getFullYear(), // 年
    'm': _m.toString().length === 1 ? '0' + _m : _m, // 月
    'd': _d.toString().length === 1 ? '0' + _d : _d, // 日
    'H': _H.toString().length === 1 ? '0' + _H : _H, // 时
    'i': _i.toString().length === 1 ? '0' + _i : _i, // 分
    's': _s.toString().length === 1 ? '0' + _s : _s // 秒
  }

  for (var i in format) {
    str = str.replace(new RegExp(i), format[i])
  }

  return str
}
