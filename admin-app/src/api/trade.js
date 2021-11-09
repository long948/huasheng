import request from '@/utils/request'

/**
 * 订单列表
 */
export function list(query) {
  return request({
    url: '/trade/list',
    method: 'get',
    params: query
  })
}

export function appeal(query) {
  return request({
    url: '/ctc/appeal',
    method: 'get',
    params: query
  })
}

export function handleAppeal(data) {
  return request({
    url: '/ctc/appeal/handle',
    method: 'post',
    data
  })
}

export function ctclist(query) {
  return request({
    url: '/ctc/trade',
    method: 'get',
    params: query
  })
}

export function ctcOrder(query) {
  return request({
    url: '/ctc/order',
    method: 'get',
    params: query
  })
}

/**
 * 确认收款
 * @param {*} data
 */
export function confirm(data) {
  return request({
    url: '/trade/confirm',
    method: 'post',
    data
  })
}

/**
 * 订单详情
 */
export function detail(query) {
  return request({
    url: '/trade/detail',
    method: 'get',
    params: query
  })
}

/**
 * 确认支付
 * @param {*} data
 */
export function pay(data) {
  return request({
    url: '/trade/pay',
    method: 'post',
    data
  })
}

/**
 * 取消订单
 * @param {*} data
 */
export function cancle(data) {
  return request({
    url: '/trade/cancle',
    method: 'post',
    data
  })
}

export function ctccancle(data) {
  return request({
    url: '/ctc/cancle',
    method: 'post',
    data
  })
}

export function ctcOrderStop(data) {
  return request({
    url: '/ctc/order/stop',
    method: 'post',
    data
  })
}
/**
 * 交易规则
 * @param {*} data
 */
export function TradeRules(id) {
  return request({
    url: '/ctc/trade_rule',
    method: 'get',
    params: { id }
  })
}

/**
 * 交易规则修改
 * @param {*} data
 */
export function TradeRulesEdit(query) {
  return request({
    url: '/ctc/trade_rule_edit',
    method: 'get',
    params: query
  })
}
/**
 * 交易指导
 * @param {*} data
 */
export function TradeGuidance(id) {
  return request({
    url: '/ctc/trade_guidance',
    method: 'get',
    params: { id }
  })
}

/**
 * 交易指导修改
 * @param {*} data
 */
export function TradeGuidanceEdit(query) {
  return request({
    url: '/ctc/trade_guidance_edit',
    method: 'get',
    params: query
  })
}
/**
 * 森林规则
 * @param {*} data
 */
export function ForestRule(id) {
  return request({
    url: '/ctc/forest_rule',
    method: 'get',
    params: { id }
  })
}

/**
 * 森林规则修改
 * @param {*} data
 */
export function ForestRulEdit(query) {
  return request({
    url: '/ctc/forest_rule/edit',
    method: 'get',
    params: query
  })
}
/**
 * 交易手续费
 * @param {*} data
 */
export function TransactionFee(id) {
  return request({
    url: '/ctc/TransactionFee',
    method: 'get',
    params: { id }
  })
}

/**
 * 交易手续费配置
 * @param {*} data
 */
export function TransactionEdit(query) {
  return request({
    url: '/ctc/TransactionFee/edit',
    method: 'get',
    params: query
  })
}
export function SettingByamount(data) {
  return request({
    url: '/ctc/SettingByAmount',
    method: 'post',
    data
  })
}
