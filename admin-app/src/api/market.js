import request from '@/utils/request'

export function goodsList(query) {
  return request({
    url: '/market/goods_list',
    method: 'get',
    params: query
  })
}

/**
 * 保存商品
 */
export function goodsSave(data, formName) {
  var url = formName === 'add' ? '/market/goods_add' : '/market/goods_edit'
  return request({
    url: url,
    method: 'post',
    data: data
  })
}
/**
 * 上架商品
 * @param {*} data
 */
export function upShelf(data) {
  return request({
    url: '/market/up_shelf',
    method: 'post',
    data
  })
}
/**
 * 下架商品
 * @param {*} data
 */
export function downShelf(data) {
  return request({
    url: '/market/down_shelf',
    method: 'post',
    data
  })
}
export function Pg(query) {
  return request({
    url: '/market/pg_goods',
    method: 'get',
    params: query
  })
}

export function getGoodsList(query) {
  return request({
    url: '/market/getGoodsList',
    method: 'get',
    params: query
  })
}
/**
 * 拼购商品保存
 */
export function PgSave(data, formName) {
  var url = formName === 'add' ? '/market/pg_add' : '/market/pg_edit'
  return request({
    url: url,
    method: 'post',
    data: data
  })
}
export function activity(query) {
  return request({
    url: '/market/activity',
    method: 'get',
    params: query
  })
}
/**
 * 获取活动商品
 * @param {*} query
 */
export function checkGoods(query) {
  return request({
    url: '/market/checkGoods',
    method: 'get',
    params: query
  })
}
/**
 * 检查所选日期是否存在活动
 * @param {*} data
 */
export function checkTime(data) {
  return request({
    url: '/market/checkTime',
    method: 'post',
    data
  })
}

/**
 * 添加拼购活动
 * @param {*} data
 */
export function activityAdd(data) {
  return request({
    url: '/market/activity_add',
    method: 'post',
    data
  })
}
/**
 * 获取活动中的商品
 * @param {*} data
 */
export function checkPgGoods(data) {
  return request({
    url: '/market/checkPgGoods',
    method: 'post',
    data
  })
}
/**
 * 给某个活动添加商品
 * @param {*} data
 */
export function activityGoodsAdd(data) {
  return request({
    url: '/market/activity_goods_add',
    method: 'post',
    data
  })
}
/**
 * 删除某个活动中的商品
 * @param {*} data
 */
export function activityGoodsDel(data) {
  return request({
    url: '/market/activity_goods_del',
    method: 'post',
    data
  })
}
/**
 * 开团记录
 * @param {*} query
 */
export function teamFound(query) {
  return request({
    url: '/market/team_found',
    method: 'get',
    params: query
  })
}
/**
 * 参团记录
 * @param {*} query
 */
export function teamFollow(query) {
  return request({
    url: '/market/team_follow',
    method: 'get',
    params: query
  })
}
/**
 * 中奖记录
 * @param {*} query
 */
export function teamLottery(query) {
  return request({
    url: '/market/team_lottery',
    method: 'get',
    params: query
  })
}
/**
 * 订单设置
 * @param {*} query
 */
export function getOrderSetting(query) {
  return request({
    url: '/market/order_setting',
    method: 'get',
    params: query
  })
}
/**
 * 保存订单设置
 * @param {*} data
 */
export function updateOrderSetting(data) {
  return request({
    url: '/market/order_setting_save',
    method: 'post',
    data
  })
}
