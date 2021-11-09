import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/admin/list',
    method: 'get',
    params: query
  })
}

export function delAdmin(data) {
  return request({
    url: '/admin/del',
    method: 'post',
    data
  })
}

export function addAdmin(data) {
  return request({
    url: '/admin/addAdmin',
    method: 'post',
    data
  })
}

export function updateAdmin(data) {
  return request({
    url: '/admin/updateAdmin',
    method: 'post',
    data
  })
}

export function ruleList(query) {
  return request({
    url: '/admin/ruleList',
    method: 'get',
    params: query
  })
}

export function getAdmin(query) {
  console.log(query)
  return request({
    url: '/admin/getAdmin',
    method: 'get',
    params: query
  })
}

// 更换谷歌验证码秘钥
export function adminGuge(query) {
  return request({
    url: '/admin/adminGuge',
    method: 'get',
    params: query
  })
}

// 获取操作日志
export function adminLogList(query) {
  return request({
    url: '/admin/adminLogList',
    method: 'get',
    params: query
  })
}
