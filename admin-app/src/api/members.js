import request from '@/utils/request'

/**
 * 会员列表
 */
export function equityDividend(query) {
  return request({
    url: '/members/equityDividend',
    method: 'get',
    params: query
  })
}

/**
 * 会员列表
 */
export function membersList(query) {
  return request({
    url: '/members/list',
    method: 'get',
    params: query
  })
}

/**
 * 设置等级
 */
export function membersLevel(data) {
  return request({
    url: '/members/level',
    method: 'post',
    data
  })
}

/**
 * 查看我的下级
 * @param {*} data
 */
export function subList(query) {
  return request({
    url: '/members/subList',
    method: 'get',
    params: query
  })
}

export function memberBill(query) {
  return request({
    url: '/members/bill',
    method: 'get',
    params: query
  })
}

export function fake(query) {
  return request({
    url: '/members/fake',
    method: 'get',
    params: query
  })
}

export function fakeAdd(data) {
  return request({
    url: '/members/fakeAdd',
    method: 'post',
    data
  })
}

/**
 * 查看我的持币
 * @param {*} data
 */
export function holdCoin(mid) {
  return request({
    url: '/members/holdCoin',
    method: 'get',
    params: {
      mid
    }
  })
}

/**
 * 修改币种余额
 * @param {*} data
 */
export function memberCoinUpdate(data) {
  return request({
    url: '/members/memberCoinUpdate',
    method: 'post',
    data
  })
}

export function memberCtcSetting(data) {
  return request({
    url: '/members/ctcsetting',
    method: 'post',
    data
  })
}

/**
 * 修改锁定余额
 * @param {*} data
 */
export function memberCoinLockMoney(data) {
  return request({
    url: '/members/memberCoinLockMoney',
    method: 'post',
    data
  })
}

/**
 * 禁用启用用户账号
 * @param {*} data
 */
export function membersStatus(query) {
  return request({
    url: '/members/membersStatus',
    method: 'get',
    params: query
  })
}

/**
 * 获取我的持币具体某一条
 * @param {*} data
 */
export function getCoinId(cid) {
  return request({
    url: '/members/getCoinId',
    method: 'get',
    params: {
      cid
    }
  })
}
/**
 * 获取资金流水
 * @param {*} data
 */
export function capitalMovements(query) {
  return request({
    url: '/members/capitalMovements',
    method: 'get',
    params: query
  })
}

/**
 * 获取资金流水
 * @param {*} data
 */
export function memberAddressList(query) {
  return request({
    url: '/members/memberAddressList',
    method: 'get',
    params: query
  })
}

/**
 * 更改会员VIP状态
 * @param {*} data
 */
export function memberVip(query) {
  return request({
    url: '/members/memberVip',
    method: 'get',
    params: query
  })
}

/**
 * 更改会员VIP状态
 * @param {*} data
 */
export function addCoin(query) {
  return request({
    url: '/members/addCoin',
    method: 'get',
    params: query
  })
}
/**
 * 修改用户备注码
 * @param {*} data
 */
export function memberRemark(data) {
  return request({
    url: '/members/memberRemark',
    method: 'post',
    data
  })
}

/**
 * 实名认证列表
 * @param {*} data
 */
export function membersAuth(query) {
  return request({
    url: '/members/auth',
    method: 'get',
    params: query
  })
}

/**
 * 实名认证驳回
 * @param {*} data
 */
export function AuthReject(data) {
  return request({
    url: '/auth/reject',
    method: 'post',
    data
  })
}

/**
 * 实名认证通过
 * @param {*} data
 */
export function AuthPass(data) {
  return request({
    url: '/auth/pass',
    method: 'post',
    data
  })
}
/**
 * 用户算力
 * @param {*} query
 */
export function UserComputingPower(query) {
  return request({
    url: '/members/computing_power',
    method: 'get',
    params: query
  })
}
/**
 * 分红总汇
 * @param {*} query
 */
export function share_reward_record(query) {
  return request({
    url: '/members/share_reward_record',
    method: 'get',
    params: query
  })
}
/**
 * 用户拥有的老鼠
 * @param {*} query
 */
export function user_sapling_package(query) {
  return request({
    url: '/shop/user_sapling_package',
    method: 'get',
    params: query
  })
}
/**
 * 删除
 * @param {*} data
 */
export function user_sapling_packageDel(data) {
  return request({
    url: '/shop/user_sapling_packageDel',
    method: 'post',
    data
  })
}
/**
 * 用户等级
 * @param {*} data
 */
export function user_level(query) {
  return request({
    url: '/members/user_levelList',
    method: 'get',
    params: query
  })
}
/**
 * 会员等级审核
 * @param {*} data
 */
export function levelEdit(data) {
  return request({
    url: '/members/levelEdit',
    method: 'post',
    data
  })
}
/**
 * 会员分红记录
 * @param {*} data
 */
export function user_dividendList(query) {
  return request({
    url: '/members/user_dividendList',
    method: 'get',
    params: query
  })
}
/**
 * 设为合伙人
 * @param {*} data
 */
export function Partner(data) {
  return request({
    url: '/members/Partner',
    method: 'post',
    data
  })
}
/**
 * 禁用团队
 * @param {*} data
 */
export function TeamDisable(data) {
  return request({
    url: '/members/team_disable',
    method: 'post',
    data
  })
}
/**
 * 会员等级规则
 * @param {*} query
 */
export function SettingUserLevel(query) {
  return request({
    url: 'members/user_level',
    method: 'get',
    params: query
  })
}
/**
 * 会员等级规则修改
 * @param {*} data
 */
export function EditUserLevel(data) {
  return request({
    url: '/members/user_levelEdit',
    method: 'post',
    data
  })
}
/**
 * 会员资料修改
 * @param {*} data
 */
export function MembersEdit(data) {
  return request({
    url: '/members/MembersEdit',
    method: 'post',
    data
  })
}
/**
 * 交易白名单
 * @param {*} data
 */
export function TransactionWhitelist(query) {
  return request({
    url: '/members/whitelist',
    method: 'get',
    params: query
  })
}
/**
 * 添加白名单用户
 * @param {*} data
 */
export function whiteAdd(data) {
  return request({
    url: '/members/whiteAdd',
    method: 'post',
    data
  })
}
/**
 * 移除白名单用户
 * @param {*} data
 */
export function whiteDel(data) {
  return request({
    url: '/members/whiteDel',
    method: 'post',
    data
  })
}
/**
 * 后台赠送/操作记录
 * @param {*} data
 */
export function AdminOperationRecord(query) {
  return request({
    url: '/members/Admin_operation_record',
    method: 'get',
    params: query
  })
}
/**
 * 后台赠送树苗记录
 * @param {*} data
 */
export function GiveSapling(query) {
  return request({
    url: '/members/give_sapling',
    method: 'get',
    params: query
  })
}
/**
 * 后台赠送树苗
 * @param {*} data
 */
export function GiveSaplings(data) {
  return request({
    url: '/members/give_saplings',
    method: 'post',
    data
  })
}
/**
 * 会员资产排序
 * @param {*} data
 */
export function UserAmount(query) {
  return request({
    url: '/members/user_amount',
    method: 'get',
    params: query
  })
}
/**
 * 油卡电话充值订单
 * @param {*} query
 */
export function EcologyOrderList(query) {
  return request({
    url: '/members/ecology_order_list',
    method: 'get',
    params: query
  })
}
/**
 * 后台赠送树苗
 * @param {*} data
 */
export function EcologyOrderCheck(data) {
  return request({
    url: '/members/ecology_order_check',
    method: 'post',
    data
  })
}
/**
 * 常规等级规则
 * @param {*} query
 */
export function RegularGrade(query) {
  return request({
    url: '/members/RegularGrade',
    method: 'get',
    params: query
  })
}
/**
 * c常规等级配置
 * @param {*} data
 */
export function RegularGradeEdit(data) {
  return request({
    url: '/members/RegularGradeEdit',
    method: 'post',
    data
  })
}
/**
 * 签到记录
 * @param {*} query
 */
export function signList(query) {
  return request({
    url: '/members/signList',
    method: 'get',
    params: query
  })
}
