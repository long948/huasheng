import request from '@/utils/request'
/**
 * 商店树苗
 * @param {*} query
 */
export function miner_level(query) {
  return request({
    url: '/miner/miner_level',
    method: 'get',
    params: query
  })
}
export function SettingMinerSapling(data) {
  return request({
    url: '/miner/SettingMinerSapling',
    method: 'post',
    data
  })
}
export function detail(query) {
  return request({
    url: '/miner/miner_level_detail',
    method: 'get',
    params: query
  })
}
/**
 * 修改环保等级
 * @param {*} data
 */
export function edit(data) {
  return request({
    url: '/miner/miner_levelEdit',
    method: 'post',
    data
  })
}
/**
 * 添加环保等级
 * @param {*} data
 */
export function add(data) {
  return request({
    url: '/miner/miner_levelAdd',
    method: 'post',
    data
  })
}
/**
 * 树苗类型
 * @param {*} query
 */
export function saplingType(query) {
  return request({
    url: '/miner/miner_saplingType',
    method: 'get',
    params: query
  })
}
/**
 * 老鼠
 * @param {*} query
 */
export function saplingPackage(query) {
  return request({
    url: '/shop/sapling_package',
    method: 'get',
    params: query
  })
}
/**
 * 删除老鼠
 * @param {*} data
 */
export function saplingPackageDel(data) {
  return request({
    url: '/miner/sapling_packageDel',
    method: 'post',
    data
  })
}
/**
 * 编辑老鼠
 */
export function saplingPackageSave(data, formName) {
  var url = formName === 'edit' ? '/shop/sapling_packageEdit' : '/shop/sapling_packageAdd'
  return request({
    url: url,
    method: 'post',
    data: data
  })
}
/**
 * 分享奖励规则
 * @param {*} query
 */
export function share_reward(query) {
  return request({
    url: '/miner/sapling_share_reward',
    method: 'get',
    params: query
  })
}
/**
 * 编辑分享奖励规则
 */
export function miner_levelSave(data, formName) {
  var url = formName === 'edit' ? '/miner/sapling_share_rewardEdit' : '/miner/sapling_share_rewardAdd'
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
export function sapling_share_rewardDel(data) {
  return request({
    url: '/miner/sapling_share_rewardDel',
    method: 'post',
    data
  })
}
/**
 * 用户树苗释放记录
 * @param {*} query
 */
export function sapling_release(query) {
  return request({
    url: '/members/user_sapling_release',
    method: 'get',
    params: query
  })
}
/**
 * 用户拥有的花田
 * @param {*} query
 */
export function user_sapling(query) {
  return request({
    url: '/shop/user_sapling',
    method: 'get',
    params: query
  })
}
/**
 * 禁用用户花田
 * @param {*} data
 */
export function user_saplingEdit(data) {
  return request({
    url: '/shop/user_saplingEdit',
    method: 'post',
    data
  })
}

/**
 * 用户树苗收益可领取表
 * @param {*} query
 */
export function user_sapling_receive(query) {
  return request({
    url: '/miner/user_sapling_receive',
    method: 'get',
    params: query
  })
}
/**
 * 分红
 * @param {*} query
 */
export function miner_dividend(query) {
  return request({
    url: '/miner/miner_dividend',
    method: 'get',
    params: query
  })
}
/**
 * 编辑分享奖励规则
 */
export function miner_dividendSave(data, formName) {
  var url = formName === 'edit' ? '/miner/miner_dividendEdit' : '/miner/miner_dividendAdd'
  return request({
    url: url,
    method: 'post',
    data: data
  })
}
/**
 * 狗
 */
export function DogList(query) {
  return request({
    url: '/shop/dog_list',
    method: 'get',
    params: query
  })
}
/**
 * 编辑狗
 */
export function DogListSave(data, formName) {
  var url = formName === 'edit' ? '/shop/dog_edit' : '/shop/dog_edit'
  return request({
    url: url,
    method: 'post',
    data: data
  })
}
/**
 * 会员拥有的狗
 */
export function UserDogList(query) {
  return request({
    url: '/shop/user_dog_list',
    method: 'get',
    params: query
  })
}
/**
 * 删除
 * @param {*} data
 */
export function UserDogDel(data) {
  return request({
    url: '/shop/user_dog_del',
    method: 'post',
    data
  })
}
