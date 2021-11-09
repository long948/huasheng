<?php


namespace App\Utils\Enum;


class ConfigEnum
{

    /**
     * 拼团订单超时取消时间
     * 单位：秒（半小时）
     */
    const FOUND_ORDER_TIME_OUT = 1800;

    /**
     * 普通订单超时取消时间
     * 单位：秒（半小时）
     */
    const ORDER_TIME_OUT = 1800;

    /**
     * 活动订单超时取消时间
     * 单位：秒（10分钟）
     */
    const ACTIVITY_ORDER_TIME_OUT = 600;

    /**
     * 开团时间
     * 单位：秒（24小时）
     */
    const ACTIVE_TIME_OUT = 86400;

    /**
     * 订单发货后的签收时间
     * 单位：秒（15天）
     */
    const ORDER_SIGN_FOR_TIME = 1296000;


    /**
     * 订单收货后评价时间
     * 单位：秒（7天）
     */
    const ORDER_COMMENT_TIME = 604800;


    /**
     * 首页拼团用户展示个数
     */
    const HOME_USER_AVATAR_NUM = 5;

    /**
     * 进账
     */
    const AMOUNT_INCREASE = 1;

    /**
     * 出账
     */
    const AMOUNT_REDUCE = 2;

    /**
     * 请求类型 苹果
     */
    const IOS = 'iOS';

    /**
     * 请求类型 安卓
     */
    const ANDROID = 'Android';

    /**
     * 小程序
     */
    const Applets = 'Applets';


}
