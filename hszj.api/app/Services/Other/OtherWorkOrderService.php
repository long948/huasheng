<?php
/**
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace App\Services\Other;

use App\Exceptions\ArException;
use App\Models\Other\OtherWorkOrder;
use App\Utils\Snowflake;

class OtherWorkOrderService
{

    /**
     * 当前提交未处理的反馈条数
     * @param $userId
     * @return int
     */
    public function getUserOrderMax($userId)
    {
        return OtherWorkOrder::query()->where('user_id', $userId)->where('is_hand', 0)->count('id');
    }

    /**
     * 提交反馈
     * @param $userId
     * @param $title
     * @param $details
     * @return bool
     * @throws \Exception
     */
    public function submit($userId, $title, $details)
    {
        if ($this->getUserOrderMax($userId) >= 10) {
            throw new ArException(ArException::SELF_ERROR, '您还有未处理完成的反馈');
        }
        $workOrder = new OtherWorkOrder();
        $workOrder->id = (new Snowflake())->nextId();
        $workOrder->title = $title;
        $workOrder->details = $details;
        $workOrder->user_id = $userId;
        return $workOrder->save();
    }

    /**
     * 获取提交反馈
     * @param $userId
     * @return Collection
     */
    public function getUserOrder($userId)
    {
        return OtherWorkOrder::query()->where('user_id', $userId)->get();
    }

    /**
     * @param $userId
     * @param $orderId
     * @return Model|null
     */
    public function details($userId, $orderId)
    {
        return OtherWorkOrder::query()->where('id', $orderId)->where('user_id', $userId)->first();
    }

}
