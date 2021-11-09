<?php
/**
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace App\Http\Controllers\Other;


use App\Exceptions\ArException;
use App\Http\Controllers\Controller;
use App\Services\Other\OtherWorkOrderService;
use Illuminate\Http\Request;

class OtherWorkOrderController extends Controller
{
    
    /**
     * @var Request
     */
    private $request;

    /**
     * @var OtherWorkOrderService
     */
    private $otherWorkOrderService;


    /**
     * OtherWorkOrderController constructor.
     * @param Request $request
     * @param OtherWorkOrderService $otherWorkOrderService
     */
    public function __construct(Request $request,
                                OtherWorkOrderService $otherWorkOrderService)
    {
        $this->request = $request;
        $this->otherWorkOrderService = $otherWorkOrderService;
    }


    /**
     * 提交订单
     * @throws ArException
     */
    public function submit()
    {
        $userId = $this->request->get('uid');
        $title = $this->request->input('title');
        $details = $this->request->input('details');
        if (empty($title)) {
            throw new ArException(ArException::SELF_ERROR, '标题不能为空');
        }

        if (strlen($title) > 300) {
            throw new ArException(ArException::SELF_ERROR, '标题最多300个字');
        }

        if (empty($details)) {
            throw new ArException(ArException::SELF_ERROR, '详情不能为空');
        }

        $this->otherWorkOrderService->submit($userId, $title, $details);
        self::success();
    }

    /**
     * 提交列表
     */
    public function orderList()
    {
        $userId = $this->request->get('uid');
        self::success($this->otherWorkOrderService->getUserOrder($userId));
    }


    /**
     * 提交详情
     * @throws ArException
     */
    public function details()
    {
        $userId = $this->request->get('uid');
        $orderId = $this->request->get('orderId');
        if (empty($orderId)) {
            throw new ArException(ArException::SELF_ERROR, '参数错误');
        }
        self::success($this->otherWorkOrderService->details($userId, $orderId));
    }

}
