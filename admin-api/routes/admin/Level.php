<?php

//产品分类路由
$router->get('/level/list',[
    'as' => 'LevelList', 'uses' => 'LevelController@List'
]);

$router->get('/level/detail',[
    'as' => 'LevelDetail', 'uses' => 'LevelController@Detail'
]);

$router->post('/level/edit',[
    'as' => 'LevelEdit', 'uses' => 'LevelController@Edit'
]);

$router->post('/level/add',[
    'as' => 'LevelAdd', 'uses' => 'LevelController@Add'
]);

