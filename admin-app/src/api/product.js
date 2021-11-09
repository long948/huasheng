import request from '@/utils/request'

/**
 * 产品列表
 * @param {*} query
 */
export function list(query) {
  return request({
    url: '/product/list',
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
    url: '/product/add',
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
    url: '/product/edit',
    method: 'post',
    data
  })
}

/**
 * 矿机详情
 * @param {} data
 */
export function detail(query) {
  return request({
    url: '/product/detail',
    method: 'get',
    params: query
  })
}

