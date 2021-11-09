<?php

//环保等级
$router->get('/miner/miner_level',[
    'as' => 'miner_level', 'uses' => 'MinerController@List'
]);

$router->post('/miner/miner_levelEdit',[
    'as' => 'miner_levelEdit', 'uses' => 'MinerController@miner_levelEdit'
]);

$router->post('/miner/miner_levelDel',[
    'as' => 'miner_levelDel', 'uses' => 'MinerController@miner_levelDel'
]);

$router->post('/miner/miner_levelAdd',[
    'as' => 'miner_levelAdd', 'uses' => 'MinerController@miner_levelAdd'
]);
$router->get('/miner/miner_level_detail',[
    'as' => 'miner_level_detail', 'uses' => 'MinerController@Detail'
]);

//商店树苗
$router->get('/shop/flower',[
    'as' => 'flower', 'uses' => 'MinerController@miner_saplingList'
]);
//商店树苗配置
$router->post('/miner/SettingMinerSapling',[
    'as' => 'SettingMinerSapling', 'uses' => 'MinerController@SettingMinerSapling'
]);
//商店树苗修改
$router->post('/shop/flowerEdit',[
    'as' => 'flowerEdit', 'uses' => 'MinerController@MinerSaplingEdit'
]);
//树苗类型
$router->get('/miner/miner_saplingType',[
    'as' => 'miner_saplingType', 'uses' => 'MinerController@miner_saplingType'
]);




//老鼠
$router->get('/shop/sapling_package',[
    'as' => 'sapling_package', 'uses' => 'MinerController@sapling_Package'
]);
$router->post('/shop/sapling_packageDel',[
    'as' => 'sapling_packageDel', 'uses' => 'MinerController@sapling_packageDel'
]);
$router->post('/shop/sapling_packageAdd',[
    'as' => 'sapling_packageAdd', 'uses' => 'MinerController@sapling_packageAdd'
]);
$router->post('/shop/sapling_packageEdit',[
    'as' => 'sapling_packageEdit', 'uses' => 'MinerController@sapling_packageEdit'
]);
//大黄
$router->get('/shop/dog_list',[
    'as' => 'DogList', 'uses' => 'MinerController@DogList'
]);
$router->post('/shop/dog_edit',[
    'as' => 'DogEdit', 'uses' => 'MinerController@DogEdit'
]);
//会员拥有的狗
$router->get('/shop/user_dog_list',[
    'as' => 'UserDogList', 'uses' => 'MinerController@UserDogList'
]);
//删除会员拥有的狗
$router->post('/shop/user_dog_del',[
    'as' => 'UserDogDel', 'uses' => 'MinerController@UserDogDel'
]);





//分享奖励规则
$router->get('/miner/sapling_share_reward',[
    'as' => 'share_reward', 'uses' => 'MinerController@share_reward'
]);
$router->post('/miner/sapling_share_rewardEdit',[
    'as' => 'sapling_share_rewardEdit', 'uses' => 'MinerController@share_rewardEdit'
]);
$router->post('/miner/sapling_share_rewardAdd',[
    'as' => 'sapling_share_rewardAdd', 'uses' => 'MinerController@share_rewardAdd'
]);
$router->post('/miner/sapling_share_rewardDel',[
    'as' => 'sapling_share_rewardDel', 'uses' => 'MinerController@share_rewardDel'
]);
$router->post('/miner/share_rewardDisable',[
    'as' => 'share_rewardDisable', 'uses' => 'MinerController@share_rewardDisable'
]);
//用户树苗收益可领取表
$router->get('/miner/user_sapling_receive',[
    'as' => 'user_sapling_receive', 'uses' => 'MinerController@user_sapling_receive'
]);
//分红总汇
$router->get('/miner/miner_dividend',[
    'as' => 'miner_dividend', 'uses' => 'MinerController@miner_dividend'
]);
$router->post('/miner/miner_dividendEdit',[
    'as' => 'miner_dividendEdit', 'uses' => 'MinerController@miner_dividendEdit'
]);
$router->post('/miner/miner_dividendAdd',[
    'as' => 'miner_dividendAdd', 'uses' => 'MinerController@miner_dividendAdd'
]);
