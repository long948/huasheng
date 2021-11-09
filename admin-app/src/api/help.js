import request from '@/utils/request'

/**
 * 问答列表
 * @param {*} query
 */
export function helpList(query) {
  return request({
    url: '/help/helpList',
    method: 'get',
    params: query
  })
}

/**
 * 添加问答
 * @param {*} data
 */
export function helpAdd(data) {
  return request({
    url: '/help/helpAdd',
    method: 'post',
    data
  })
}

/**
 * 更新问答
 * @param {*} data
 */
export function helpUpdate(data) {
  return request({
    url: '/help/helpUpdate',
    method: 'post',
    data
  })
}

/**
 * 删除问答
 * @param {*} data
 */
export function helpDelete(id) {
  return request({
    url: '/help/helpDelete',
    method: 'get',
    params: { id }
  })
}

/**
 * 获取问答
 * @param {*} data
 */
export function getHelp(id) {
  return request({
    url: '/help/getHelp',
    method: 'get',
    params: { id }
  })
}

// 会员问答
/**
 * 问答列表
 * @param {*} query
 */
export function memberHelpList(query) {
  return request({
    url: '/help/memberHelpList',
    method: 'get',
    params: query
  })
}

/**
 * 添加问答
 * @param {*} data
 */
export function memberHelpAdd(data) {
  return request({
    url: '/help/memberHelpAdd',
    method: 'post',
    data
  })
}

/**
 * 更新问答
 * @param {*} data
 */
export function memberHelpUpdate(data) {
  return request({
    url: '/help/memberHelpUpdate',
    method: 'post',
    data
  })
}

/**
 * 删除问答
 * @param {*} data
 */
export function memberHelpDelete(id) {
  return request({
    url: '/help/memberHelpDelete',
    method: 'get',
    params: { id }
  })
}

/**
 * 获取问答
 * @param {*} data
 */
export function getMemberHelp(id) {
  return request({
    url: '/help/getMemberHelp',
    method: 'get',
    params: { id }
  })
}

