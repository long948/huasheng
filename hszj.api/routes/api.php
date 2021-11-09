<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/', function () {
    return 'Hallo Word!';
});

//用户
Route::post('member-register', 'MemberController@Register');
Route::post('member-login', 'MemberController@Login');
Route::post('member-forget-password', 'MemberController@ForgetPassword');
Route::post('member-forget-paypassword', 'MemberController@ForgetPayPassword');
//sms
Route::post('sms-register-code', 'SmsController@RegisterCode')->middleware('captcha');

Route::post('sms-modify-pass', 'SmsController@ModifyPassCode')->middleware('captcha');;
Route::post('sms-modify-paypass', 'SmsController@ModifyPayPassCode')->middleware('captcha');;

// 文章列表
Route::get('article-list', 'ArticleListController@lists');
Route::get('article-list-detail', 'ArticleListController@detail');

Route::get('member-doc', 'IndexController@Doc');
Route::get('plat-server', 'IndexController@Server');

Route::get('get.update', 'IndexController@update');
Route::get('common-question', 'IndexController@Question');

Route::get('wechat-group', 'MemberController@WechatGroup');

Route::get('get.ctc.info', 'CtcController@getCtcInfo');
Route::get('get.share', 'MemberController@share');
Route::get('qq', 'IndexController@qq');
Route::get('captcha', 'IndexController@captcha');
Route::get('get.download', 'IndexController@download');

Route::get('get.shop.index', 'Shop\ShopTimeSlotActivityController@timeSlotActivityGood'); //拼购首页

Route::get('get.shop.category', 'Shop\ShopGoodCategoryController@category'); //分类
Route::get('get.shop.rule', 'Shop\ShopGoodController@shopRule'); //购物规则

Route::get('get.app.info', 'System\SystemController@appAndShareInfo');//分享和下载相关链接
Route::middleware('token')->group(function () {

    Route::get('get.shop.details', 'Shop\ShopGoodController@details'); //拼购详情
    Route::post('shop.open.activity', 'Shop\ShopTeamFoundController@openActivity'); //开团
    Route::post('shop.join.activity', 'Shop\ShopTeamFoundController@ginsengFound'); //参团
    Route::get('shop.found.code', 'Shop\ShopTeamFoundController@getFoundByCode'); //团口令

    Route::post('shop.activity.pay', 'Shop\ShopPayController@pay'); //支付
    Route::get('shop.my.found', 'Shop\ShopTeamFollowController@follows'); //参团记录
    Route::get('shop.my.found.details', 'Shop\ShopTeamFollowController@details'); //参团记录详情
    Route::get('shop.found.collages', 'Shop\ShopTeamFollowController@collages'); //邀请好友

    //获取解冻金额
    Route::get('get.frozen.amount', 'System\SystemController@frozenAmount');
    //解冻
    Route::post('user.frozen', 'System\SystemController@userFrozen');

    Route::get('get.exchange.fee', 'CoinController@exchangeFee');

    Route::get('notice-list', 'IndexController@NoticeList');
    Route::get('notice-detail', 'IndexController@NoticeDetail');
    Route::get('banner-list', 'IndexController@BannerList');
    Route::get('qiniu-upload', 'IndexController@QiniuUpload');
    Route::get('plat-setting', 'IndexController@Setting');

    //用户
    Route::post('member-modify-password', 'MemberController@ModifyPassword');
    Route::post('member-modify-paypassword', 'MemberController@ModifyPayPassword');
    Route::post('member-modify-nick', 'MemberController@ModifyNickName');
    Route::post('member-modify-avatar', 'MemberController@ModifyAvatar');
    //赠送账户记录
    Route::get('member-giveaway-list', 'MemberController@userGiveAwayBill');

    Route::get('group', 'MemberController@Group');
    Route::get('member-info', 'MemberController@Info');
    Route::get('invite-list', 'MemberController@InviteList');
    Route::post('bind-bank', 'MemberController@BindBank');
    // Route::post('modify-bank', 'MemberController@ModifyBank');
    Route::post('bind-wechat', 'MemberController@BindWeChat');
    // Route::post('modify-wechat', 'MemberController@ModifyWechat');
    Route::post('bind-alipay', 'MemberController@BindAlipay');
    // Route::post('modify-alipay', 'MemberController@ModifyAlipay');
    Route::post('bind-adress', 'MemberController@BindAddress');
    // Route::post('modify-adress', 'MemberController@ModifyAddress');
    Route::get('bind-pay-info', 'MemberController@PayInfo');
    Route::post('post.read.all', 'IndexController@readAll');
    Route::post('post.modifyphone', 'MemberController@ModifyPhone');

    Route::get('self', 'MemberController@My');
    Route::get('team', 'MemberController@Group');
    Route::post('member-set-paypassword', 'MemberController@SetPayPassword');
    Route::post('auth-member', 'MemberController@Auth');
    Route::get('auth-info', 'MemberController@AuthInfo');
    Route::post('auth-safe', 'MemberController@Safe');
    Route::post('ocr', 'MemberController@ocr');

    //账户资金
    Route::get('finace-molds', 'MemberController@FinaceMolds');
    Route::get('finace-list', 'MemberController@FinaceList');
    //币种
    Route::get('coin-list', 'CoinController@List');
    Route::get('single-coin', 'CoinController@Single');
    Route::get('coin-balance', 'CoinController@Balance');
    Route::get('coin-single-balance', 'CoinController@SingleBalance');
    Route::get('withdraw-detail', 'CoinController@WithdrawDetail');
    Route::get('recharge-detail', 'CoinController@RechargeDetail');
    Route::get('recharge-address', 'CoinController@RechargeAddress');
    Route::get('recharge-withdraw', 'CoinController@RechargeAndWithdraw');
    Route::post('withdraw', 'CoinController@Withdraw');
    Route::post('post.exchange', 'CoinController@exchange');
    Route::get('get.wallet.info', 'CoinController@walletInfo');

    //矿机
    Route::get('product-list', 'ProductController@List');
    Route::get('product-detail', 'ProductController@Detail');
    Route::post('product-purchase', 'ProductController@Purchase');
    Route::post('product-draw', 'ProductController@Draw');
    Route::get('product-newreg', 'ProductController@NewReg');
    Route::get('product-rush', 'ProductController@Rush');
    Route::post('product-rush-purchase', 'ProductController@RushPurchase');
    Route::get('free-list', 'ProductController@FreeList');
    Route::post('balance-withdraw', 'ProductController@Withdraw');
    Route::get('balance-withdraw-detail', 'ProductController@WithdrawDetail');
    Route::get('output-list', 'ProductController@Output');
    Route::get('output-sum', 'ProductController@OutputSum');
    Route::get('plan-recored', 'ProductController@PlanRecord');
    Route::get('my-product', 'ProductController@MyProduct');
    Route::get('my-product-detail', 'ProductController@MyProductDetail');
    Route::get('my-output-list', 'ProductController@MyOutput');
    Route::get('rush-log', 'ProductController@RushLog');
    Route::get('rush-success', 'ProductController@RushSuccess');

    //py
    Route::post('trade-purchase', 'TradeController@Purchase');
    Route::get('trade-list', 'TradeController@List');
    Route::get('trade-detail', 'TradeController@Detail');
    Route::post('trade-pay', 'TradeController@Pay');
    Route::post('trade-sell', 'TradeController@Sell');
    Route::post('trade-collections', 'TradeController@Collections');
    Route::post('trade-cancle', 'TradeController@Cancle');

    //ctc
    Route::get('ctc-coin', 'CtcController@Coin');
    Route::post('add-sell-order', 'CtcController@AddSellOrder');
    Route::get('ctc-list', 'CtcController@List');
    Route::get('ctc-my-list', 'CtcController@MyList');
    Route::post('ctc-order-stop', 'CtcController@OrderStop');
    Route::post('ctc-buy', 'CtcController@Buy');
    Route::get('ctc-trade-list', 'CtcController@TradeList');
    Route::get('ctc-trade-my-list', 'CtcController@TradeMyList');
    Route::get('ctc-trade-detail', 'CtcController@TradeDetail');
    Route::get('ctc-pay-method', 'CtcController@PayMethod');
    Route::post('ctc-sell', 'CtcController@Sell');
    Route::post('ctc-confirm', 'CtcController@Confirm');
    Route::get('ctc-info', 'CtcController@Info');
    Route::get('ctc-member-info', 'CtcController@MemberInfo');
    Route::post('ctc-cancle', 'CtcController@Cancle');
    Route::post('ctc-trade-pay', 'CtcController@TradePay');
    Route::get('ctc-appeal-reason', 'CtcController@AppealReason');
    Route::post('ctc-appeal', 'CtcController@Appeal');
    //sms
    Route::post('sms-bindpay-code', 'SmsController@BindPayCode');
    Route::post('sms-setpaypass-code', 'SmsController@SetPayPassCode')->middleware('captcha');;
    Route::post('sms-unbind-code', 'SmsController@unbindCode');
    Route::post('sms-bind-code', 'SmsController@bindCode');
    Route::post('sms-vcode', 'SmsController@vCode');
    Route::post('sms-transfer', 'SmsController@transfer')->middleware('captcha');

    Route::get('get.tx.power', 'MemberController@txPower');
    Route::post('modify-alipay', 'MemberController@ModifyAlipay');
    Route::post('modify-adress', 'MemberController@ModifyAddress');
    Route::post('post.ctc.unfrozen', 'CtcController@unfrozen');

    //用户树苗首页
    Route::get('sapling-user-home', 'MinerUserSaplingController@userHome');

    Route::get('sapling-user-home1', 'MinerUserSaplingController@userHome1');
    //获取树苗列表
    Route::get('sapling-list', 'MinerSaplingController@saplingList');
    //树苗详情
    Route::get('sapling-details', 'MinerSaplingController@saplingDetail');

    //购买树苗
    Route::post('sapling-buy', 'MinerUserSaplingController@userBuySapling');
    //用户树苗列表
    Route::get('sapling-user-list', 'MinerUserSaplingController@userSaplingList');
    //用户树苗详情
    Route::get('sapling-user-details', 'MinerUserSaplingController@userSaplingDetail');
    //用户树苗浇水
    Route::post('sapling-user-watering', 'MinerUserSaplingController@userWatering');
    //用户领取树苗收益
    Route::post('sapling-user-receive', 'MinerUserSaplingController@userReceive');
    //用户是否可领取小树苗
    Route::get('user-auth-is-giveAway', 'MinerUserSaplingController@userIsGiveAway');
    //领取小树苗
    Route::get('user-auth-giveAway', 'MinerUserSaplingController@UserIsGiveAwayByAuth');

    //获取可升级等级
    Route::get('level-list', 'MinerUserLevelController@levelList');
    //获取用户等级
    Route::get('level-user', 'MinerUserLevelController@getUserLevel');

    //团队信息之算力分息
    Route::get('user-my-team-sapling-info', 'MinerUserSaplingController@userMyTeamSaplingInfo');
    //用户算力列表
    Route::get('user-power-list', 'MinerUserComputingPowerController@userComputingPowerList');


    //查看订单列表
    Route::get('user-submit-ecology-order-list', 'EcologyOrderController@orderList');
    //初始化金额
    Route::get('user-submit-ecology-info', 'EcologyOrderController@info');

    #商店
    Route::get('store', 'Store\StoreController@store');
    #购田 耗子 狗子
    Route::post('store-buy', 'Store\StoreController@buy');
    #详情
    Route::get('store-details', 'Store\StoreController@details');

    //认证后才能操作的
    Route::middleware('userAuth')->group(function () {
        #提交充值油卡，手机花费订单
        Route::post('user-submit-ecology-order', 'EcologyOrderController@order');
        #老鼠偷取收益
        Route::post('user-mouse-steal', 'Mouse\MouseController@stealIncome');
        #大黄站岗
        Route::post('user-dog-stand_guard', 'Dog\DogUserController@standGuard');
        #正在站岗的大黄
        Route::get('user-dog-is-stand-guard', 'Dog\DogUserController@isStandGuard');
        #仓库转出至账户
        Route::Post('user-income-transfer', 'User\UserTotalIncomeController@transfer');
        #账户转入至备用斤
        Route::Post('user-income-turning', 'User\UserTotalIncomeController@turning');
    });

    #获取可偷取收益用户列表
    Route::get('user-mouse-steal-users', 'Mouse\MouseController@getStealUsers');
    #我的老鼠
    Route::get('user-mouse-list', 'Mouse\MouseController@list');
    #我的大黄列表
    Route::get('user-dog-list', 'Dog\DogUserController@list');
    #仓库记录
    Route::get('user-income-list', 'User\UserTotalIncomeController@list');

    #问题反馈
    Route::POST('user-work-submit', 'Other\OtherWorkOrderController@submit');
    #问题列表
    Route::get('user-work-list', 'Other\OtherWorkOrderController@orderList');
    #问题详情
    Route::get('user-work-details', 'Other\OtherWorkOrderController@details');

    #视频签到
    Route::post('user-sign', 'System\MembersSignController@sign');

});

Route::get('test', 'TestController@test');








