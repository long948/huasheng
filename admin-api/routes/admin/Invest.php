<?php

//订单路由
$router->get('/product/invest',[
    'as' => 'InvestList', 'uses' => 'InvestController@List'
]);

