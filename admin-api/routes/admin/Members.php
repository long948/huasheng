<?php

//会员路由

//会员列表
$router->get('/members/list', [
    'as' => 'membersList', 'uses' => 'MembersController@membersList',
]);
//修改会员资料
$router->post('/members/MembersEdit', [
    'as' => 'MembersEdit', 'uses' => 'MembersController@MembersEdit',
]);
$router->get('/members/auth', [
    'as' => 'membersAuth', 'uses' => 'MembersController@AuthList',
]);

//我的下级会员
$router->get('/members/subList', [
    'as' => 'subList', 'uses' => 'MembersController@subList',
]);

$router->post('/members/level', [
    'as' => 'subList', 'uses' => 'MembersController@MemberLevel',
]);

$router->get('/members/bill', [
    'as' => 'MemberBill', 'uses' => 'MembersController@MemberBills',
]);

//$router->get('/members/fake', [
//    'as' => 'MemberFake', 'uses' => 'MembersController@MemberFake',
//]);
//
//$router->post('/members/fakeAdd', [
//    'as' => 'MemberFakeAdd', 'uses' => 'MembersController@MemberFakeAdd',
//]);

//$router->post('/members/ctcsetting', [
//    'as' => 'MemberCTCSetting', 'uses' => 'MembersController@CtcSetting',
//]);



//查看我的持币
$router->get('/members/holdCoin', [
    'as' => 'holdCoin', 'uses' => 'MembersController@holdCoin',
]);

//修改币种余额
$router->post('/members/memberCoinUpdate', [
    'as' => 'memberCoinUpdate', 'uses' => 'MembersController@memberCoinUpdate',
]);

//修改锁定余额
$router->post('/members/memberCoinLockMoney', [
    'as' => 'memberCoinLockMoney', 'uses' => 'MembersController@memberCoinLockMoney',
]);

//禁用启用用户账号
$router->get('/members/membersStatus', [
    'as' => 'membersStatus', 'uses' => 'MembersController@membersStatus',
]);

//根据id获取我的持币
$router->get('/members/getCoinId', [
    'as' => 'getCoinId', 'uses' => 'MembersController@getCoinId',
]);

//获取资金流水记录
$router->get('/members/capitalMovements', [
    'as' => 'capitalMovements', 'uses' => 'MembersController@capitalMovements',
]);

//获取用户收货地址
$router->get('/members/memberAddressList', [
    'as' => 'memberAddressList', 'uses' => 'MembersController@memberAddressList',
]);

//设置会员的VIP状态
$router->get('/members/memberVip', [
    'as' => 'memberVip', 'uses' => 'MembersController@memberVip',
]);

//为memberCoin表增加一个币种信息
$router->get('/members/addCoin', [
    'as' => 'addCoin', 'uses' => 'MembersController@addCoin',
]);


//修改用户交易备注码
$router->post('/members/memberRemark', [
    'as' => 'memberRemark', 'uses' => 'MembersController@memberRemark',
]);

//实名认证列表
$router->post('/auth/list', [
    'as' => 'memberAuthList', 'uses' => 'MembersController@AuthList',
]);

//实名认证通过
$router->post('/auth/pass', [
    'as' => 'memberAuthPass', 'uses' => 'MembersController@AuthPass',
]);

//实名认证驳回
$router->post('/auth/reject', [
    'as' => 'memberAuthReject', 'uses' => 'MembersController@AuthReject',
]);
//用户算力
$router->get('/members/computing_power', [
    'as' => 'computing_power', 'uses' => 'MembersController@UserComputingPower',
]);
//分享奖励
$router->get('/members/share_reward_record',[
    'as' => 'share_reward_record', 'uses' => 'MembersController@share_reward_record'
]);
//用户开通的机器人
$router->get('/shop/user_sapling_package',[
    'as' => 'share_reward_record', 'uses' => 'MembersController@user_sapling_package'
]);
$router->post('/shop/user_sapling_packageDel',[
    'as' => 'user_sapling_packageDel', 'uses' => 'MembersController@user_sapling_packageDel'
]);
//用户等级
$router->get('/members/user_levelList',[
    'as' => 'user_levelList', 'uses' => 'MembersController@miner_user_level'
]);

//等级审核
$router->post('/members/levelEdit',[
    'as' => 'levelEdit', 'uses' => 'MembersController@levelEdit'
]);
//用户分红记录
$router->get('/members/user_dividendList',[
    'as' => 'user_dividendList', 'uses' => 'MembersController@user_dividendList'
]);
//合伙人
$router->post('/members/Partner',[
    'as' => 'Partner', 'uses' => 'MembersController@Partner'
]);
//团队禁用
$router->post('/members/team_disable',[
    'as' => 'TeamDisable', 'uses' => 'MembersController@TeamDisable'
]);
//会员等级
$router->get('/members/user_level',[
    'as' => 'UserLevel', 'uses' => 'MembersController@UserLevel'
]);
//会员等级规则
$router->post('/members/user_levelEdit',[
    'as' => 'UserLevelEdit', 'uses' => 'MembersController@UserLevelEdit'
]);
//交易白名
$router->get('/members/whitelist',[
    'as' => 'whitelist', 'uses' => 'MembersController@TransactionWhitelist'
]);
//添加白名单用户
$router->post('/members/whiteAdd',[
    'as' => 'whiteAdd', 'uses' => 'MembersController@whiteAdd'
]);
//移除白名单
$router->post('/members/whiteDel',[
    'as' => 'whiteDel', 'uses' => 'MembersController@whiteDel'
]);
//后台充值记录
$router->get('/members/Admin_operation_record',[
    'as' => 'AdminOperationRecord', 'uses' => 'MembersController@AdminOperationRecord'
]);
//管理员赠送树苗记录
$router->get('/members/give_sapling',[
    'as' => 'GiveSapling', 'uses' => 'MembersController@GiveSaplingList'
]);
//管理员赠送树苗
$router->post('/members/give_saplings',[
    'as' => 'GiveSaplings', 'uses' => 'MembersController@GiveSapling'
]);
//用户花田释放记录
$router->get('/members/user_sapling_release',[
    'as' => 'user_sapling_release', 'uses' => 'MinerController@user_sapling_release'
]);
//用户拥有的花田
$router->get('/shop/user_sapling',[
    'as' => 'user_sapling', 'uses' => 'MinerController@miner_user_sapling'
]);
//禁用用户花田
$router->post('/shop/user_saplingEdit',[
    'as' => 'user_saplingEdit', 'uses' => 'MinerController@user_saplingEdit'
]);
//禁用用户树苗
$router->get('/members/user_amount',[
    'as' => 'UserAmount', 'uses' => 'MembersController@UserAmount'
]);
//油卡电话充值订单
$router->get('/members/ecology_order_list',[
    'as' => 'EcologyOrderList', 'uses' => 'MembersController@EcologyOrderList'
]);
//油卡电话充值订单审核
$router->post('/members/ecology_order_check',[
    'as' => 'EcologyOrderCheck', 'uses' => 'MembersController@EcologyOrderCheck'
]);
//会员常规等级
$router->get('/members/RegularGrade', [
    'as' => 'RegularGrade', 'uses' => 'MembersController@RegularGrade'
]);
//会员常规等级配置
$router->post('/members/RegularGradeEdit', [
    'as' => 'RegularGradeEdit', 'uses' => 'MembersController@RegularGradeEdit'
]);

//分红股权列表
$router->get('/members/equityDividend', [
    'as' => 'equityDividend', 'uses' => 'equityDividendController@equityDividend'
]);
//会员签到记录
$router->get('/members/signList', [
    'as' => 'signList', 'uses' => 'MembersController@signList'
]);
