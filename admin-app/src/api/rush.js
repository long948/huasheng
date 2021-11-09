import request from '@/utils/request'

/**
 * 产品列表
 * @param {*} query
 */
export function list(query) {
  return request({
    url: '/product/rush',
    method: 'get',
    params: query
  })
}

export function memberRush(query) {
  return request({
    url: '/product/memberRush',
    method: 'get',
    params: query
  })
}

export function RushSuccess(data) {
  return request({
    url: '/rush/success',
    method: 'post',
    data
  })
}

/**
 * 添加产品
 * @param {*} data
 */
export function add(data) {
  return request({
    url: '/rush/add',
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
    url: '/rush/edit',
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
    url: '/rush/detail',
    method: 'get',
    params: query
  })
}

