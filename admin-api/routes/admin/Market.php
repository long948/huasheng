<?php
//商品列表
$router->get('/market/goods_list',[
    'as' => 'goodsList', 'uses' => 'MarketController@goodsList'
]);
$router->post('/market/goods_add',[
    'as' => 'GoodsAdd','uses' => 'MarketController@GoodsAdd'
]);
$router->post('/market/goods_edit',[
    'as' => 'GoodsEdit','uses' => 'MarketController@GoodsEdit'
]);
$router->post('/market/up_shelf',[
    'as' => 'GoodsEdit','uses' => 'MarketController@upShelf'
]);
$router->post('/market/down_shelf',[
    'as' => 'GoodsEdit','uses' => 'MarketController@downShelf'
]);
//拼购商品列表
$router->get('/market/pg_goods',[
    'as' => 'PgGoods', 'uses' => 'MarketController@PgGoods'
]);
$router->get('/market/getGoodsList',[
    'as' => 'getGoodsList', 'uses' => 'MarketController@getGoodsList'
]);
$router->post('/market/pg_add',[
    'as' => 'PgAdd', 'uses' => 'MarketController@PgAdd'
]);
$router->post('/market/pg_edit',[
    'as' => 'PgEdit', 'uses' => 'MarketController@PgEdit'
]);
//拼购活动列表
$router->get('/market/activity',[
    'as' => 'activity', 'uses' => 'MarketController@activity'
]);
$router->get('/market/checkGoods',[
    'as' => 'checkGoods', 'uses' => 'MarketController@checkGoods'
]);
$router->post('/market/checkTime',[
    'as' => 'checkTime', 'uses' => 'MarketController@checkTime'
]);
$router->post('/market/activity_add',[
    'as' => 'activityAdd', 'uses' => 'MarketController@activityAdd'
]);
$router->post('/market/checkPgGoods',[
    'as' => 'checkPgGoods', 'uses' => 'MarketController@checkPgGoods'
]);
$router->post('/market/activity_goods_add',[
    'as' => 'activityGoodsAdd', 'uses' => 'MarketController@activityGoodsAdd'
]);
$router->post('/market/activity_goods_del',[
    'as' => 'activityGoodsDel', 'uses' => 'MarketController@activityGoodsDel'
]);
//开团记录
$router->get('/market/team_found',[
    'as' => 'teamFound', 'uses' => 'MarketController@teamFound'
]);
//参团记录
$router->get('/market/team_follow',[
    'as' => 'teamFollow', 'uses' => 'MarketController@teamFollow'
]);
//中奖记录
$router->get('/market/team_lottery',[
    'as' => 'teamLottery', 'uses' => 'MarketController@teamLottery'
]);
//订单设置
$router->get('/market/order_setting',[
    'as' => 'OrderSetting', 'uses' => 'MarketController@orderSetting'
]);
$router->post('/market/order_setting_save',[
    'as' => 'updateOrderSetting', 'uses' => 'MarketController@updateOrderSetting'
]);
