<?php


namespace App\Utils\Enum;


/**
 *
 * 常量列表
 * Class Enums
 * @package App\Services\Enum
 */
class Enums
{
    /**
     * redis KEY
     * @var array
     */
    const REDIS_LOCK_KEY = [

        //领取树苗
        'USER_RECEIVE' => 'USER_RECEIVE_',
        //购买机器人
        'BUY_USER_SAPLING_PACKAGE' => 'BUY_USER_SAPLING_PACKAGE_',
        //提交充值订单
        'USER_ECOLOGY_ORDER' => 'USER_ECOLOGY_ORDER_',
        //购买树苗
        'USER_BUY_SAPLING' => 'USER_BUY_SAPLING_',
        //浇水
        'USER_WATERING' => 'USER_WATERING_',
        //领取花田
        'USER_RECEIVE' => 'USER_RECEIVE_',
        //提交充值订单
        'USER_ECOLOGY_ORDER' => 'USER_ECOLOGY_ORDER_',
        //仓库花生米转出至余额
        'USER_TRANSFER_HSM' => 'USER_TRANSFER_HSM_',
        #偷取花生米
        'USER_STEAL_INCOME' => 'USER_STEAL_INCOME_',
        #购买耗子
        'USER_BUY_HOUSE' => 'USER_BUY_HOUSE_',
        #购买狗子
        'USER_BUY_DOG' => 'USER_BUY_DOG_',
        #收益转入备用金
        'USER_TURNING' => 'USER_TURNING_',
    ];

    /**
     * 缓存key
     * @var array
     */
    const CACHE_KEY = [
        //大黄站岗
        'USER_DOG_STAND_GUARD_UP' => 'user:dog:stand_guard_up_',
        //大黄休息
        'USER_DOG_REST' => 'user:dog:',
        //再次偷取
        'user_mouse_steal' => 'user:mouse:steal:',
        #商店
        'store' => 'o:s:',
        #商店详情
        'store_details' => 'o:s:d:'
    ];

    /**
     * 队列名称
     * @var array
     */
    const QUEUE_NAME = [
        //团队信息统计
        'team' => 'team',
        //释放花田
        'release' => 'release',
        //大黄休息队列
        'dog_rest' => 'dog_rest',
        //大黄站岗
        'dog_stand_guard' => 'dog_stand_guard',
        //老鼠受伤
        'mouse_healing' => 'mouse_healing'
    ];
}
