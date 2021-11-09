<?php

//币种管理路由

//币种管理路由



//汇率设置
$router->post('/coin/extendEdit', [
    'as' => 'extendEdit', 'uses' => 'CoinController@extendEdit',
]);

$router->get('/coin/getCoinList', [
    'as' => 'getCoinList', 'uses' => 'CoinController@getCoinList',
]);
$router->get('/coin/coinList', [
    'as' => 'coinList', 'uses' => 'CoinController@coinList',
]);

$router->post('/coin/coinAdd', [
    'as' => 'coinAdd', 'uses' => 'CoinController@coinAdd',
]);

$router->post('/coin/coinUpdate', [
    'as' => 'coinUpdate', 'uses' => 'CoinController@coinUpdate',
]);

$router->get('/coin/getCoin', [
    'as' => 'getCoin', 'uses' => 'CoinController@getCoin',
]);

$router->get('/coin/getProtocol', [
    'as' => 'getProtocol', 'uses' => 'CoinController@getProtocol',
]);

//转入记录
$router->get('/coin/rechargeList', [
    'as' => 'rechargeList', 'uses' => 'RechargeController@rechargeList',
]);

//转出记录
$router->get('/coin/withdrawList', [
    'as' => 'withdrawList', 'uses' => 'WithdrawController@withdrawList',
]);

$router->get('/coin/getWithdrawCoin', [
    'as' => 'getWithdrawCoin', 'uses' => 'WithdrawController@getWithdrawCoin',
]);

$router->post('/coin/waitProcess', [
    'as' => 'waitProcess', 'uses' => 'WithdrawController@waitProcess',
]);

//h5资金流水
$router->get('/coin/financingList', [
    'as' => 'financingList', 'uses' => 'CoinController@financingList',
]);
//h5资金流水
$router->get('/coin/financingMoldList', [
    'as' => 'financingMoldList', 'uses' => 'CoinController@financingMoldList',
]);

//批量通过审核
$router->post('/coin/MultiplePass', [
    'as' => 'MultiplePass', 'uses' => 'CoinController@MultiplePass',
]);

//批量驳回
$router->post('/coin/MultipleReject', [
    'as' => 'MultipleReject', 'uses' => 'CoinController@MultipleReject',
]);


//内部转账记录
$router->get('/coin/TransferRecord',[
    'as' => 'TransferRecord', 'uses' => 'CoinController@TransferRecord'
]);


