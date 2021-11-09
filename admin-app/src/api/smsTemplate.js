import request from '@/utils/request'

/**
 * 列表
 * @param {*} query
 */
export function smsTemplateList(query) {
  return request({
    url: '/config/smstemplateList',
    method: 'get',
    params: query
  })
}

/**
 * 保存
 */
export function smsTemplateSave(data, formName) {
  var url = formName === 'add' ? '/config/smstemplateAdd' : '/config/smstemplateEdit'
  return request({
    url: url,
    method: 'post',
    data: data
  })
}

/**
 * 删除
 * @param {*} data
 */
export function smsTemplateDelete(data) {
  return request({
    url: '/config/smstemplateDelete',
    method: 'post',
    data
  })
}
