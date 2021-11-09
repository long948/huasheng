<?php

//订单路由
$router->get('/trade/list',[
    'as' => 'TradeList', 'uses' => 'TradeController@List'
]);

$router->get('/ctc/trade',[
    'as' => 'CTCList', 'uses' => 'TradeController@CTCList'
]);

$router->get('/ctc/order',[
    'as' => 'CTCOrder', 'uses' => 'TradeController@CTCOrder'
]);

$router->post('/trade/pay',[
    'as' => 'TradePay', 'uses' => 'TradeController@Pay'
]);

$router->post('/trade/confirm',[
    'as' => 'TradeConfirm', 'uses' => 'TradeController@Confirm'
]);

$router->post('/trade/cancle',[
    'as' => 'TradeCancle', 'uses' => 'TradeController@Cancle'
]);

$router->post('/ctc/appeal/handle',[
    'as' => 'AppealHandle', 'uses' => 'TradeController@AppealHandle'
]);

$router->post('/ctc/cancle',[
    'as' => 'CTCCancle', 'uses' => 'TradeController@CTCCancle'
]);

$router->post('/ctc/order/stop',[
    'as' => 'StopOrder', 'uses' => 'TradeController@StopOrder'
]);

$router->get('/trade/detail',[
    'as' => 'TradeDetail', 'uses' => 'TradeController@Detail'
]);

$router->get('/ctc/appeal',[
    'as' => 'CTCAppeal', 'uses' => 'TradeController@Appeal'
]);
//交易规则
$router->get('/ctc/trade_rule',[
    'as' => 'TradeRules', 'uses' => 'TradeController@TradeRules'
]);
//修改交易规则
$router->get('/ctc/trade_rule_edit', [
    'as' => 'TradeRulesEdit', 'uses' => 'TradeController@TradeRulesEdit'
]);
//交易指导
$router->get('/ctc/trade_guidance',[
    'as' => 'TradeRules', 'uses' => 'TradeController@TradeGuidance'
]);
//修改交易指导
$router->get('/ctc/trade_guidance_edit', [
    'as' => 'TradeRulesEdit', 'uses' => 'TradeController@TradeGuidanceEdit'
]);
//ctc设置
$router->get('/ctc/setting', [
    'as' => 'SettingCTC', 'uses' => 'SettingController@CTC',
]);
$router->post('/ctc/setting/edit', [
    'as' => 'SettingOtherCTC', 'uses' => 'SettingController@CTCEdit',
]);
//ctc币种设置
$router->get('/ctc/ctccoin', [
    'as' => 'SettingCTCCoin', 'uses' => 'SettingController@CTCCoin',
]);

$router->post('/ctc/ctccoin/add', [
    'as' => 'SettingCTCCoinAdd', 'uses' => 'SettingController@CTCCoinAdd',
]);

$router->post('/ctc/ctccoin/edit', [
    'as' => 'SettingCTCCoinEdit', 'uses' => 'SettingController@CTCCoinEdit',
]);
//其他设置
$router->post('/ctc/other/edit', [
    'as' => 'SettingOtherEdit', 'uses' => 'SettingController@OtherEdit',
]);
$router->get('/ctc/other', [
    'as' => 'SettingOther', 'uses' => 'SettingController@Other',
]);

//森林规则
$router->get('/ctc/forest_rule',[
    'as' => 'ForestRule', 'uses' => 'TradeController@ForestRule'
]);
$router->get('/ctc/forest_rule/edit',[
    'as' => 'ForestRulEdit', 'uses' => 'TradeController@ForestRulEdit'
]);
////交易手续费
//$router->get('/ctc/TransactionFee',[
//    'as' => 'TransactionFee', 'uses' => 'TradeController@TransactionFee'
//]);
//$router->get('/ctc/TransactionFee/edit',[
//    'as' => 'TransactionFeeEdit', 'uses' => 'TradeController@TransactionFeeEdit'
//]);
//今日求购量
$router->post('/ctc/SettingByAmount',[
    'as' => 'SettingByAmount', 'uses' => 'TradeController@SettingByAmount'
]);
