import request from '@/utils/request'

// 汇率列表
export function extendList(query) {
  return request({
    url: 'coin/extendList',
    method: 'get',
    params: query
  })
}

// 汇率设置
export function extendEdit(data) {
  return request({
    url: '/coin/extendEdit',
    method: 'post',
    data
  })
}

/**
 * 获取展示币种列表
 * @param {*} query
 */
export function getCoinList(query) {
  return request({
    url: 'coin/getCoinList',
    method: 'get',
    params: query
  })
}

/**
 * 币种列表
 * @param {*} query
 */
export function coinList(query) {
  return request({
    url: '/coin/coinList',
    method: 'get',
    params: query
  })
}
/**
 * 内部转账记录列表
 * @param {*} query
 */
export function TransferRecord(query) {
  return request({
    url: '/coin/TransferRecord',
    method: 'get',
    params: query
  })
}
/**
 * 添加问答
 * @param {*} data
 */
export function coinAdd(data) {
  return request({
    url: '/coin/coinAdd',
    method: 'post',
    data
  })
}

/**
 * 更新币种
 * @param {*} data
 */
export function coinUpdate(data) {
  return request({
    url: '/coin/coinUpdate',
    method: 'post',
    data
  })
}

/**
 * 获取币种协议
 * @param {*} data
 */
export function getProtocol() {
  return request({
    url: '/coin/getProtocol',
    method: 'get',
    params: { }
  })
}

/**
 * 获取一条币种
 * @param {*} data
 */
export function getCoin(id) {
  return request({
    url: '/coin/getCoin',
    method: 'get',
    params: { id }
  })
}

/**
 * 转入记录
 * @param {*} data
 */
export function rechargeList(query) {
  return request({
    url: '/coin/rechargeList',
    method: 'get',
    params: query
  })
}

/**
 * 转出记录
 * @param {*} data
 */
export function withdrawList(query) {
  return request({
    url: '/coin/withdrawList',
    method: 'get',
    params: query
  })
}

/**
 * 转出记录
 * @param {*} data
 */
export function waitProcess(data) {
  return request({
    url: '/coin/waitProcess',
    method: 'post',
    data
  })
}

/**
 * 获取一条记录
 */
export function getWithdrawCoin(query) {
  return request({
    url: '/coin/getWithdrawCoin',
    method: 'get',
    params: query
  })
}

/**
 * 获取资金流水
 */
export function financingList(query) {
  return request({
    url: '/coin/financingList',
    method: 'get',
    params: query
  })
}

/**
 * 获取资金变动类型
 */
export function financingMoldList(query) {
  return request({
    url: '/coin/financingMoldList',
    method: 'get',
    params: query
  })
}

/**
 * 获取IA币转入记录
 */
export function IARechargeChainLog(query) {
  return request({
    url: '/coin/IARechargeChainLog',
    method: 'get',
    params: query
  })
}

/**
 * 批量通过
 */
export function MultiplePass(data) {
  return request({
    url: '/coin/MultiplePass',
    method: 'post',
    data
  })
}

// 批量驳回
export function MultipleReject(data) {
  return request({
    url: '/coin/MultipleReject',
    method: 'post',
    data
  })
}

