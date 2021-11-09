import request from '@/utils/request'

/**
 * 获取七牛的配置
 * @param {*} query
 */
export function list(query) {
  return request({
    url: '/config/list',
    method: 'get',
    params: query
  })
}

/**
 * 更新或添加七牛配置
 * @param {*} data
 */
export function updateAddQiniu(data) {
  return request({
    url: '/config/updateAddQiniu',
    method: 'post',
    data
  })
}
/**
 * 系统维护
 * @param {*} query
 */
export function systemList(query) {
  return request({
    url: '/system/systemList',
    method: 'get',
    params: query
  })
}
/**
 * 系统维护
 * @param {*} data
 */
export function SystemClose(data) {
  return request({
    url: '/system/close',
    method: 'post',
    data
  })
}
/**
 * 获取短信配置表
 */
export function smsList(query) {
  return request({
    url: '/config/smsList',
    method: 'get',
    params: query
  })
}

/**
 * 更新或添加短信配置
 * @param {*} data
 */
export function updateAddSms(data) {
  return request({
    url: '/config/updateAddSms',
    method: 'post',
    data
  })
}

/**
 * 获取短信类型
 * @param {*} data
 */
export function smstype(query) {
  return request({
    url: '/config/smstype',
    method: 'get',
    params: query
  })
}

/**
 * 获取版本更新信息列表
 */
export function appList(query) {
  return request({
    url: '/config/appList',
    method: 'get',
    params: query
  })
}

/**
 * 更新或添加版本配置
 * @param {*} data
 */
export function updateAddAppVersion(data) {
  return request({
    url: '/config/updateAddAppVersion',
    method: 'post',
    data
  })
}

/**
 * 获取系统配置信息
 */
export function settingList(query) {
  return request({
    url: '/config/settingList',
    method: 'get',
    params: query
  })
}

/**
 * 更新系统配置信息
 * @param {*} data
 */
export function settingEdit(data) {
  return request({
    url: '/config/settingEdit',
    method: 'post',
    data
  })
}
/**
 * 分享配置
 */
export function share(query) {
  return request({
    url: '/config/share',
    method: 'get',
    params: query
  })
}

/**
 * 更新或添加分享配置
 * @param {*} data
 */
export function updateShare(data) {
  return request({
    url: '/config/updateShare',
    method: 'post',
    data
  })
}
/**
 * 分享赠送配置
 * @param {*} query
 */
export function GiveSettingList(query) {
  return request({
    url: '/config/GiveSettingList',
    method: 'get',
    params: query
  })
}
/**
 * 分享赠送配置
 * @param {*} data
 */
export function GiveSetting(data) {
  return request({
    url: '/config/GiveSetting',
    method: 'post',
    data
  })
}
/**
 * app下载链接
 */
export function downloadLink(query) {
  return request({
    url: '/config/downloadLink',
    method: 'get',
    params: query
  })
}
/**
 * app下载链接修改
 * @param {*} data
 */
export function downloadLinkEdit(data) {
  return request({
    url: '/config/downloadLinkEdit',
    method: 'post',
    data
  })
}
