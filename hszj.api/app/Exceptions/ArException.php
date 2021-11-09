<?php

namespace App\Exceptions;

//自定义异常类，方便全局异常处理
class ArException extends \Exception
{
    //未知
    const NET_WORK_ERROR = 99999;
    //公共
    const UNKONW = 10000;
    const NOT_DATA = 10001;
    const PARAM_ERROR = 10002;
    const SELF_ERROR = 10003;
    const ACCOUNT_NOT_FOUND = 10004;

    //Token
    const AUTH_ERROR = 20001;
    const TOKEN_ERROR = 20003;
    const TOKEN_EXPIRE = 20004;
    const NO_LOGIN = 20005;
    const ILLEGAL_LOGIN = 20006;

    //Member
    const USER_NOT_FOUND = 30001;
    const USER_BE_BAN = 30002;
    const PAY_PASS_ERROR = 30003;
    const USER_IDCRAD_CUNZAI = 30004;
    const USER_ID_CARD_AUTH_IMAGE = 30019;
    const USER_ID_CARD_AUTH_BIRTH = 30020;
    const USER_ID_CARD_AUTH_NAME = 30021;
    const USER_ID_CARD_AUTH_NUM = 30022;
    const USER_ID_CARD_AUTH_SEX = 30023;

    //Coin
    const COIN_NOT_FOUND = 40001;
    const HAS_NO_COIN = 40002;
    const COIN_NOT_ENOUGH = 40003;


    const SHOP_FOUND_NOT_EXISTS = 50000;
    const SHOP_FOUND_END = 50001;
    const SHOP_ACTIVITY_FOUND_END = 50002;
    const SHOP_ACTIVITY_GOOD_NOT_EXISTS = 50003;
    const SHOP_USER_EXISTS_FOUND = 50004;
    const SHOP_JOIN_SELF_FOUNd = 50005;
    const SHOP_ACTIVITY_END = 50006;
    const SHOP_USER_FOLLOW_NOT_EXISTS = 50007;
    const SHOP_FOLLOW_PAY_END = 50008;
    const SHOP_FOLLOW_TIME_OUT = 50009;



    protected $map = [
        //Common
        self::UNKONW => '未知错误',
        self::NOT_DATA => '暂无数据',
        self::PARAM_ERROR => '参数错误',
        self::SELF_ERROR => '%s',
        self::PAY_PASS_ERROR => '支付密码错误',
        self::USER_IDCRAD_CUNZAI => '该身份证已存在',
        self::USER_ID_CARD_AUTH_IMAGE => '身份证照片识别失败',
        self::USER_ID_CARD_AUTH_NAME => '身份证姓名不匹配',
        self::USER_ID_CARD_AUTH_BIRTH => '身份证出生日期不匹配',
        self::USER_ID_CARD_AUTH_NUM => '身份证号码不匹配',
        self::USER_ID_CARD_AUTH_SEX => '身份证性别不匹配',
        //System
        self::NET_WORK_ERROR => '网络繁忙，请稍后再试',
        //Auth
        self::AUTH_ERROR => '登录凭证无效',
        self::TOKEN_ERROR => '登录凭证已失效',
        self::TOKEN_EXPIRE => '登录凭证过期,请重新登录',
        self::NO_LOGIN => '请先登录',
        self::ILLEGAL_LOGIN => '登录信息已失效,请重新登录', //非法token
        self::USER_NOT_FOUND => '用户不存在或已注销',
        self::USER_BE_BAN => '您账户异常,请联系客服',
        self::ACCOUNT_NOT_FOUND => '账号或密码错误',
        //Coin
        self::COIN_NOT_FOUND => '未找到该币种',
        self::COIN_NOT_ENOUGH => '余额不足',

        //shop
        self::SHOP_FOUND_NOT_EXISTS => '团信息不存在',
        self::SHOP_FOUND_END => '团已结束,请重新开团',
        self::SHOP_ACTIVITY_FOUND_END => '拼购活动不存在',
        self::SHOP_ACTIVITY_GOOD_NOT_EXISTS => '拼购商品不存在',
        self::SHOP_USER_EXISTS_FOUND => '您已参加过该团了',
        self::SHOP_JOIN_SELF_FOUNd => '您不能参加自己开的团',
        self::SHOP_ACTIVITY_END => '活动已结束',
        self::SHOP_USER_FOLLOW_NOT_EXISTS => '拼购订单不存在',
        self::SHOP_FOLLOW_PAY_END => '该订单已支付',
        self::SHOP_FOLLOW_TIME_OUT => '订单超时已不能支付',

    ];

    /**
     * ArException constructor.
     * @param int|10000 $code
     * @param string|string $arData
     */
    public function __construct(int $code = 10000, string $arData = '')
    {
        $this->message = sprintf($this->map[$code], $arData);
        $this->code = $code;
    }

}
