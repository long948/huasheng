import request from '@/utils/request'

export function getRoute() {
  return request({
    url: '/index',
    method: 'get',
    params: {}
  })
}
