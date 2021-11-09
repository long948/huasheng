import request from '@/utils/request'
/**
 * 花田列表
 * @param {*} query
 */
export function saplingList(query) {
  return request({
    url: '/shop/flower',
    method: 'get',
    params: query
  })
}
/**
 * 修改花田
 * @param {*} data
 */
export function saplingEdit(data) {
  return request({
    url: '/shop/flowerEdit',
    method: 'post',
    data
  })
}
