<?php
//七牛配置
$router->get('/config/list',[
    'as' => 'list', 'uses' => 'ConfigController@list'
]);
$router->get('/upload/getToken',[
    'as' => 'getToken', 'uses' => 'QiniuController@upload'
]);
$router->post('/config/updateAddQiniu',[
    'as' => 'updateAddQiniu','uses' => 'ConfigController@updateAddQiniu'
]);
//短信配置
$router->get('/config/smsList',[
    'as' => 'smsList', 'uses' => 'ConfigController@smsList'
]);
$router->post('/config/updateAddSms',[
    'as' => 'updateAddSms','uses' => 'ConfigController@updateAddSms'
]);
$router->get('/config/smstype',[
    'as' => 'smstype', 'uses' => 'ConfigController@smstype'
]);
//版本配置
$router->get('/config/appList',[
    'as' => 'appList','uses' => 'ConfigController@appList'
]);
$router->post('/config/updateAddAppVersion',[
    'as' => 'updateAddAppVersion','uses' => 'ConfigController@updateAddAppVersion'
]);

//SacConfig 系统配置列表
$router->get('/config/configList',[
    'as' => 'configList','uses' => 'ConfigController@configList'
]);

//SacConfig 系统配置更新
$router->post('/config/configEdit',[
    'as' => 'configEdit','uses' => 'ConfigController@configEdit'
]);

//系统配置
$router->get('/config/settingList',[
    'as' => 'settingList','uses' => 'ConfigController@settingList'
]);


//SacConfig 系统配置更新
$router->post('/config/settingEdit',[
    'as' => 'settingEdit','uses' => 'ConfigController@settingEdit'
]);
// 短信模板配置
$router->get('/config/smstemplateList',[
    'as' => 'smstemplateList', 'uses' => 'SmsTemplateController@lists'
]);

$router->post('/config/smstemplateAdd', [
    'as' => 'smstemplateAdd', 'uses' => 'SmsTemplateController@add'
]);

$router->post('/config/smstemplateEdit', [
    'as' => 'smstemplateEdit', 'uses' => 'SmsTemplateController@edit'
]);

$router->post('/config/smstemplateDelete', [
    'as' => 'smstemplateDelete', 'uses' => 'SmsTemplateController@delete'
]);
//系统维护
$router->get('/system/systemList',[
    'as' => 'systemList','uses' => 'ConfigController@systemList'
]);
$router->post('/system/close',[
    'as' => 'SystemClose', 'uses' => 'ConfigController@SystemClose'
]);
//share配置
$router->get('/config/share',[
    'as' => 'share','uses' => 'ConfigController@share'
]);
$router->post('/config/updateShare',[
    'as' => 'updateShare','uses' => 'ConfigController@updateShare'
]);
//分享赠送配置
$router->get('/config/GiveSettingList',[
    'as' => 'GiveSettingList','uses' => 'ConfigController@GiveSettingList'
]);
$router->post('/config/GiveSetting',[
    'as' => 'GiveSetting', 'uses' => 'ConfigController@GiveSetting'
]);
//清除系统缓存
$router->post('/config/ClearCache',[
    'as' => 'ClearCache', 'uses' => 'ConfigController@ClearCache'
]);
//下载链接
$router->get('/config/downloadLink',[
    'as' => 'downloadLink','uses' => 'ConfigController@downloadLink'
]);
$router->post('/config/downloadLinkEdit',[
    'as' => 'downloadLinkEdit', 'uses' => 'ConfigController@downloadLinkEdit'
]);
