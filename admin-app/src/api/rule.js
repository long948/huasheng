import request from '@/utils/request'

export function getRule(data) {
  return request({
    url: '/rule/list',
    method: 'get',
    params: data
  })
}

export function editRule(data) {
  return request({
    url: '/rule/edit',
    method: 'post',
    data
  })
}

export function addRule(data) {
  return request({
    url: '/rule/add',
    method: 'post',
    data
  })
}

export function getGroup(data) {
  return request({
    url: '/rule/group',
    method: 'get',
    params: data
  })
}

export function delRule(data) {
  return request({
    url: '/rule/del',
    method: 'post',
    data
  })
}

export function addGroup(data) {
  return request({
    url: '/rule/group/add',
    method: 'post',
    data
  })
}

export function editGroup(data) {
  return request({
    url: '/rule/group/edit',
    method: 'post',
    data
  })
}

export function delGroup(data) {
  return request({
    url: '/rule/group/del',
    method: 'post',
    data
  })
}
// export function ClearCache(data) {
//   return request({
//     url: '/config/ClearCache',
//     method: 'post',
//     data
//   })
// }
