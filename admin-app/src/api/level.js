import request from '@/utils/request'

/**
 * 产品列表
 * @param {*} query
 */
export function list(query) {
  return request({
    url: '/level/list',
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
    url: '/level/add',
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
    url: '/level/edit',
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
    url: '/level/detail',
    method: 'get',
    params: query
  })
}

