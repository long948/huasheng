<?php
//配置路由

//等级配置列表
$router->get('/setting/invite', [
    'as' => 'SettingInvite', 'uses' => 'SettingController@InviteList',
]);

$router->post('/setting/invite/edit', [
    'as' => 'SettingInviteEdit', 'uses' => 'SettingController@InviteEdit',
]);

$router->get('/setting/other', [
    'as' => 'SettingOther', 'uses' => 'SettingController@Other',
]);

$router->get('/setting/ctc', [
    'as' => 'SettingCTC', 'uses' => 'SettingController@CTC',
]);

$router->get('/setting/ctccoin', [
    'as' => 'SettingCTCCoin', 'uses' => 'SettingController@CTCCoin',
]);

$router->post('/setting/ctccoin/add', [
    'as' => 'SettingCTCCoinAdd', 'uses' => 'SettingController@CTCCoinAdd',
]);

$router->post('/setting/ctccoin/edit', [
    'as' => 'SettingCTCCoinEdit', 'uses' => 'SettingController@CTCCoinEdit',
]);

$router->post('/setting/other/edit', [
    'as' => 'SettingOtherEdit', 'uses' => 'SettingController@OtherEdit',
]);

$router->post('/setting/ctc/edit', [
    'as' => 'SettingOtherCTC', 'uses' => 'SettingController@CTCEdit',
]);

$router->get('/setting/plan', [
    'as' => 'SettingPlan', 'uses' => 'SettingController@Plan',
]);

$router->post('/setting/plan/edit', [
    'as' => 'SettingPlanEdit', 'uses' => 'SettingController@PlanEdit',
]);


