<?php
//banner
$router->get('/bannerNotice/bannerList', [
    'as' => 'bannerList', 'uses' => 'BannerNoticeController@bannerList'
]);

$router->get('/bannerNotice/wechatGroup', [
    'as' => 'WechatGroup', 'uses' => 'BannerNoticeController@WechatGroup'
]);

$router->post('/bannerNotice/bannerUpdate', [
    'as' => 'bannerUpdate', 'uses' => 'BannerNoticeController@bannerUpdate'
]);

$router->post('/bannerNotice/bannerDelete', [
    'as' => 'bannerDelete', 'uses' => 'BannerNoticeController@bannerDelete'
]);

$router->get('/bannerNotice/server', [
    'as' => 'bannerServer', 'uses' => 'BannerNoticeController@Server'
]);

$router->post('/bannerNotice/ServerUpdate', [
    'as' => 'bannerServerUpdate', 'uses' => 'BannerNoticeController@ServerUpdate'
]);

$router->get('/bannerNotice/getBanner', [
    'as' => 'getBanner', 'uses' => 'BannerNoticeController@getBanner'
]);

$router->post('/bannerNotice/bannerAdd', [
    'as' => 'bannerAdd', 'uses' => 'BannerNoticeController@bannerAdd'
]);

$router->post('/bannerNotice/AddWechat', [
    'as' => 'AddWechat', 'uses' => 'BannerNoticeController@AddWechat'
]);

$router->post('/bannerNotice/editWechat', [
    'as' => 'EditWechat', 'uses' => 'BannerNoticeController@EditWechat'
]);

$router->post('/bannerNotice/newsAdd', [
    'as' => 'NewsAdd', 'uses' => 'BannerNoticeController@NewsAdd'
]);




//公告路由
$router->get('/bannerNotice/noticeList', [
    'as' => 'NoticeList', 'uses' => 'BannerNoticeController@noticeList'
]);

$router->get('/bannerNotice/qa', [
    'as' => 'QaList', 'uses' => 'BannerNoticeController@QaList'
]);

$router->post('/bannerNotice/noticeUpdate', [
    'as' => 'noticeUpdate', 'uses' => 'BannerNoticeController@noticeUpdate'
]);

$router->post('/bannerNotice/newsUpdate', [
    'as' => 'NewsUpdate', 'uses' => 'BannerNoticeController@NewsUpdate'
]);

$router->post('/bannerNotice/qaUpdate', [
    'as' => 'QaUpdate', 'uses' => 'BannerNoticeController@QaUpdate'
]);


$router->get('/bannerNotice/noticeDelete', [
    'as' => 'noticeDelete', 'uses' => 'BannerNoticeController@noticeDelete'
]);

$router->get('/bannerNotice/qaDelete', [
    'as' => 'qaDelete', 'uses' => 'BannerNoticeController@QaDelete'
]);



$router->get('/bannerNotice/newsDelete', [
    'as' => 'NewsDelete', 'uses' => 'BannerNoticeController@NewsDelete'
]);

$router->get('/bannerNotice/getNotice', [
    'as' => 'NoticeDelete', 'uses' => 'BannerNoticeController@getNotice'
]);

$router->get('/bannerNotice/getQa', [
    'as' => 'GetQA', 'uses' => 'BannerNoticeController@GetQA'
]);


$router->get('/bannerNotice/getNews', [
    'as' => 'GetNews', 'uses' => 'BannerNoticeController@GetNews'
]);



$router->post('/bannerNotice/noticeAdd', [
    'as' => 'NoticeAdd', 'uses' => 'BannerNoticeController@noticeAdd'
]);

$router->post('/bannerNotice/qaAdd', [
    'as' => 'qaAdd', 'uses' => 'BannerNoticeController@QaAdd'
]);

$router->get('/bannerNotice/AboutUs', [
    'as' => 'AboutUs', 'uses' => 'BannerNoticeController@AboutUs'
]);

$router->get('/bannerNotice/AboutUsEdit', [
    'as' => 'AboutUsEdit', 'uses' => 'BannerNoticeController@AboutUsEdit'
]);

$router->get('/bannerNotice/MemberDoc', [
    'as' => 'MemberDoc', 'uses' => 'BannerNoticeController@MemberDoc'
]);

$router->post('/bannerNotice/MemberDocEdit', [
    'as' => 'MemberDocEdit', 'uses' => 'BannerNoticeController@MemberDocEdit'
]);

$router->post('/bannerNotice/PayDocEdit', [
    'as' => 'PayDocEdit', 'uses' => 'BannerNoticeController@PayDocEdit'
]);

$router->get('/bannerNotice/News', [
    'as' => 'NewsList', 'uses' => 'BannerNoticeController@NewList'
]);

//公告
$router->get('/bannerNotice/notice',[
    'as' => 'Notice', 'uses' => 'BannerNoticeController@Notice'
]);
$router->post('/bannerNotice/NoticeEdit',[
    'as' => 'NoticeEdit', 'uses' => 'BannerNoticeController@NoticeEdit'
]);
$router->get('/bannerNotice/getNotices', [
    'as' => 'getNotices', 'uses' => 'BannerNoticeController@getNotices'
]);
$router->post('/bannerNotice/NoticesAdd', [
    'as' => 'NoticesAdd', 'uses' => 'BannerNoticeController@NoticesAdd'
]);
$router->get('/bannerNotice/NoticesDel', [
    'as' => 'NoticesDel', 'uses' => 'BannerNoticeController@NoticesDel'
]);
//商学院
$router->get('/bannerNotice/schoolList',[
    'as' => 'schoolList', 'uses' => 'BannerNoticeController@schoolList'
]);
$router->post('/bannerNotice/schoolUpdate',[
    'as' => 'schoolUpdate', 'uses' => 'BannerNoticeController@schoolUpdate'
]);
$router->post('/bannerNotice/schoolAdd', [
    'as' => 'schoolAdd', 'uses' => 'BannerNoticeController@schoolAdd'
]);
$router->post('/bannerNotice/schoolDelete', [
    'as' => 'schoolDelete', 'uses' => 'BannerNoticeController@schoolDelete'
]);
//用户反馈
$router->get('/bannerNotice/user_feedback',[
    'as' => 'UserFeedback', 'uses' => 'BannerNoticeController@UserFeedback'
]);
$router->post('/bannerNotice/user_feedback_answer', [
    'as' => 'schoolDelete', 'uses' => 'BannerNoticeController@userFeedbackAnswer'
]);
//拼购规则
$router->get('/bannerNotice/pg_rule',[
    'as' => 'pgRule', 'uses' => 'BannerNoticeController@pgRule'
]);
$router->post('/bannerNotice/pgRuleEdit', [
    'as' => 'pgRuleEdit', 'uses' => 'BannerNoticeController@pgRuleEdit'
]);
