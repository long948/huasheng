import request from '@/utils/request'

/**
 * 获取banner列表
 * @param {*} query
 */
export function bannerList(query) {
  return request({
    url: '/bannerNotice/bannerList',
    method: 'get',
    params: query
  })
}

export function WechatList(query) {
  return request({
    url: '/bannerNotice/wechatGroup',
    method: 'get',
    params: query
  })
}

export function AddWechat(data) {
  return request({
    url: '/bannerNotice/AddWechat',
    method: 'post',
    data
  })
}

export function EditWechat(data) {
  return request({
    url: '/bannerNotice/editWechat',
    method: 'post',
    data
  })
}
/**
 * 保存
 */
export function bannerSave(data, formName) {
  var url = formName === 'add' ? '/bannerNotice/bannerAdd' : '/bannerNotice/bannerUpdate'
  return request({
    url: url,
    method: 'post',
    data: data
  })
}

/**
 * 删除banner
 * @param {*} data
 */
export function bannerDelete(data) {
  return request({
    url: '/bannerNotice/bannerDelete',
    method: 'post',
    data
  })
}
/**
 * 获取公告列表
 * @param {*} query
 */
export function noticeList(query) {
  return request({
    url: '/bannerNotice/noticeList',
    method: 'get',
    params: query
  })
}

/**
 * 获取快讯列表
 * @param {*} query
 */
export function newsList(query) {
  return request({
    url: '/bannerNotice/News',
    method: 'get',
    params: query
  })
}

/**
 * 获取快讯列表
 * @param {*} query
 */
export function qaList(query) {
  return request({
    url: '/bannerNotice/qa',
    method: 'get',
    params: query
  })
}

/**
 * 编辑公告
 * @param {*} query
 */
export function noticeUpdate(data) {
  return request({
    url: '/bannerNotice/noticeUpdate',
    method: 'post',
    data
  })
}

/**
 * 编辑常见问题
 * @param {*} query
 */
export function qaUpdate(data) {
  return request({
    url: '/bannerNotice/qaUpdate',
    method: 'post',
    data
  })
}

/**
 * 编辑快讯
 * @param {*} query
 */
export function newsUpdate(data) {
  return request({
    url: '/bannerNotice/newsUpdate',
    method: 'post',
    data
  })
}

/**
 * 删除公告
 * @param {*} data
 */
export function noticeDelete(id) {
  return request({
    url: '/bannerNotice/noticeDelete',
    method: 'get',
    params: { id }
  })
}

/**
 * 删除快讯
 * @param {*} data
 */
export function newsDelete(id) {
  return request({
    url: '/bannerNotice/newsDelete',
    method: 'get',
    params: { id }
  })
}

/**
 * 删除常见问题
 * @param {*} data
 */
export function qaDelete(id) {
  return request({
    url: '/bannerNotice/qaDelete',
    method: 'get',
    params: { id }
  })
}

/**
 * 添加公告
 * @param {*} query
 */
export function noticeAdd(data) {
  return request({
    url: '/bannerNotice/noticeAdd',
    method: 'post',
    data
  })
}

// 添加常见问题
export function qaAdd(data) {
  return request({
    url: '/bannerNotice/qaAdd',
    method: 'post',
    data
  })
}

/**
 * 添加快讯
 * @param {*} data
 */
export function newsAdd(data) {
  return request({
    url: '/bannerNotice/newsAdd',
    method: 'post',
    data
  })
}

/**
 * 获取一篇公告
 * @param {*} data
 */
export function getNotice(id) {
  return request({
    url: '/bannerNotice/getNotice',
    method: 'get',
    params: { id }
  })
}

/**
 * 获取调常见问题
 * @param {*} data
 */
export function getQa(id) {
  return request({
    url: '/bannerNotice/getQa',
    method: 'get',
    params: { id }
  })
}

/**
 * 获取一篇快讯
 * @param {*} data
 */
export function getNews(id) {
  return request({
    url: '/bannerNotice/getNews',
    method: 'get',
    params: { id }
  })
}

/**
 * 关于我们
 * @param {*} data
 */
export function AboutUs(id) {
  return request({
    url: '/bannerNotice/AboutUs',
    method: 'get',
    params: { id }
  })
}

/**
 * 关于我们修改
 * @param {*} data
 */
export function AboutUsEdit(query) {
  return request({
    url: '/bannerNotice/AboutUsEdit',
    method: 'get',
    params: query
  })
}

/**
 * 用户协议
 * @param {*} data
 */
export function MemberDoc(id) {
  return request({
    url: '/bannerNotice/MemberDoc',
    method: 'get',
    params: { id }
  })
}

/**
 * 用户协议修改
 * @param {*} data
 */
export function MemberDocEdit(data) {
  return request({
    url: '/bannerNotice/MemberDocEdit',
    method: 'post',
    data
  })
}

export function PayDocEdit(data) {
  return request({
    url: '/bannerNotice/PayDocEdit',
    method: 'post',
    data
  })
}
/**
 * 获取公告列表
 * @param {*} query
 */
export function Notice(query) {
  return request({
    url: '/bannerNotice/notice',
    method: 'get',
    params: query
  })
}
/**
 * 公告修改
 * @param {*} query
 */
export function NoticeEdit(data) {
  return request({
    url: '/bannerNotice/NoticeEdit',
    method: 'post',
    data
  })
}
/**
 * 获取一篇公告
 * @param {*} data
 */
export function getNotices(id) {
  return request({
    url: '/bannerNotice/getNotices',
    method: 'get',
    params: { id }
  })
}
/**
 * 添加公告
 * @param {*} query
 */
export function NoticesAdd(data) {
  return request({
    url: '/bannerNotice/NoticesAdd',
    method: 'post',
    data
  })
}
/**
 * 删除公告
 * @param {*} data
 */
export function NoticesDel(id) {
  return request({
    url: '/bannerNotice/NoticesDel',
    method: 'get',
    params: { id }
  })
}
/**
 * 商学院文章列表
 * @param {*} query
 */
export function schoolList(query) {
  return request({
    url: '/bannerNotice/schoolList',
    method: 'get',
    params: query
  })
}
/**
 * 商学院文章编辑
 */
export function schoolSave(data, formName) {
  var url = formName === 'add' ? '/bannerNotice/schoolAdd' : '/bannerNotice/schoolUpdate'
  return request({
    url: url,
    method: 'post',
    data: data
  })
}

/**
 * 删除商学院文章
 * @param {*} data
 */
export function schoolDelete(data) {
  return request({
    url: '/bannerNotice/schoolDelete',
    method: 'post',
    data
  })
}
/**
 * 用户反馈
 * @param {*} query
 */
export function UserFeedback(query) {
  return request({
    url: '/bannerNotice/user_feedback',
    method: 'get',
    params: query
  })
}
/**
 * 删除商学院文章
 * @param {*} data
 */
export function UserFeedbackAnswer(data) {
  return request({
    url: '/bannerNotice/user_feedback_answer',
    method: 'post',
    data
  })
}
/**
 * 拼购规则
 * @param {*} query
 */
export function pgRule(query) {
  return request({
    url: '/bannerNotice/pg_rule',
    method: 'get',
    params: query
  })
}
/**
 * 拼购规则修改
 * @param {*} data
 */
export function pgRuleEdit(data) {
  return request({
    url: '/bannerNotice/pgRuleEdit',
    method: 'post',
    data
  })
}
