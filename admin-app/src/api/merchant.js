import request from '@/utils/request'

/**
 * 产品列表
 * @param {*} query
 */
export function list(query) {
  return request({
    url: '/merchant/list',
    method: 'get',
    params: query
  })
}

/**
 * 商户详情
 * @param {} data
 */
export function detail(query) {
  return request({
    url: '/merchant/detail',
    method: 'get',
    params: query
  })
}

/**
 * 添加产品
 * @param {*} data
 */
export function add(data) {
  return request({
    url: '/merchant/add',
    method: 'post',
    data
  })
}

/**
 * 修改产品
 * @param {*} data
 */
export function edit(data) {
  return request({
    url: '/merchant/edit',
    method: 'post',
    data
  })
}

