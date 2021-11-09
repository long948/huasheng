<?php

//抢购路由
$router->get('/product/rush',[
    'as' => 'RushList', 'uses' => 'RushController@List'
]);

$router->get('/rush/detail',[
    'as' => 'RushDetail', 'uses' => 'RushController@Detail'
]);

$router->post('/rush/edit',[
    'as' => 'RushEdit', 'uses' => 'RushController@Edit'
]);

$router->post('/rush/add',[
    'as' => 'RushEdit', 'uses' => 'RushController@Add'
]);

$router->get('/product/memberRush',[
    'as' => 'MemberRush', 'uses' => 'RushController@MemberRush'
]);

$router->post('/rush/success',[
    'as' => 'RushSuccess', 'uses' => 'RushController@SetSuccess'
]);






//产品分类路由
$router->get('/product/list',[
    'as' => 'ProductList', 'uses' => 'ProductController@List'
]);

$router->get('/product/detail',[
    'as' => 'ProductDetail', 'uses' => 'ProductController@Detail'
]);

$router->post('/product/edit',[
    'as' => 'ProductEdit', 'uses' => 'ProductController@Edit'
]);

$router->post('/product/add',[
    'as' => 'ProductAdd', 'uses' => 'ProductController@Add'
]);

