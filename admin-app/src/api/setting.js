import request from '@/utils/request'

// 等级配置
export function inviteList(query) {
  return request({
    url: '/setting/invite',
    method: 'get',
    params: query
  })
}

export function editInvite(data) {
  return request({
    url: '/setting/invite/edit',
    method: 'post',
    data
  })
}

// 其他设置
export function other(query) {
  return request({
    url: '/ctc/other',
    method: 'get',
    params: query
  })
}
// ctc设置
export function ctc(query) {
  return request({
    url: '/ctc/setting',
    method: 'get',
    params: query
  })
}

export function server(query) {
  return request({
    url: '/bannerNotice/server',
    method: 'get',
    params: query
  })
}

// 其他设置
export function editOther(data) {
  return request({
    url: '/ctc/other/edit',
    method: 'post',
    data
  })
}

export function editCTC(data) {
  return request({
    url: '/ctc/setting/edit',
    method: 'post',
    data
  })
}

export function editServer(data) {
  return request({
    url: '/bannerNotice/ServerUpdate',
    method: 'post',
    data
  })
}

// 预约设置
export function plan(query) {
  return request({
    url: '/setting/plan',
    method: 'get',
    params: query
  })
}

export function ctccoin(query) {
  return request({
    url: '/ctc/ctccoin',
    method: 'get',
    params: query
  })
}

// 修改预约设置
export function editPlan(data) {
  return request({
    url: '/setting/plan/edit',
    method: 'post',
    data
  })
}

export function AddCtcCoin(data) {
  return request({
    url: '/ctc/ctccoin/add',
    method: 'post',
    data
  })
}

export function editCtcCoin(data) {
  return request({
    url: '/ctc/ctccoin/edit',
    method: 'post',
    data
  })
}
