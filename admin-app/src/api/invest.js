import request from '@/utils/request'

/**
 * 产品列表
 * @param {*} query
 */
export function list(query) {
  return request({
    url: '/product/invest',
    method: 'get',
    params: query
  })
}
