<?php
/**
 * @OA\Parameter(
 *      parameter="CoinId",
 *      name="Id",
 *      description="币种Id",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="WithdrawId",
 *      name="Id",
 *      description="提现ID",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="RechargeId",
 *      name="Id",
 *      description="充值ID",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="Money",
 *      name="Money",
 *      description="金额",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="Address",
 *      name="Address",
 *      description="地址",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="Memo",
 *      name="Memo",
 *      description="提现备注",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="CoinNumber",
 *      name="Number",
 *      description="数量",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="Type",
 *      name="Type",
 *      description="0全部 1充值 2提现",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="OldAuthCode",
 *      name="OldAuthCode",
 *      description="旧手机号验证码",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="NewAuthCode",
 *      name="NewAuthCode",
 *      description="新手机号验证码",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="NewPhone",
 *      name="NewPhone",
 *      description="新手机号",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * 
 * @OA\Parameter(
 *      parameter="order_type",
 *      name="order_type",
 *      description="订单类型（0全部 1求购 2出售）",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * @OA\Parameter(
 *      parameter="order_status",
 *      name="order_status",
 *      description="订单状态 0全部 1待付款 2待确认 3完成 4取消 5申诉",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * @OA\Parameter(
 *      parameter="min",
 *      name="min",
 *      description="最小数量",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * @OA\Parameter(
 *      parameter="max",
 *      name="max",
 *      description="最大数量",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * @OA\Parameter(
 *      parameter="start",
 *      name="start",
 *      description="开始时间",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * @OA\Parameter(
 *      parameter="end",
 *      name="end",
 *      description="结束时间",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="TxType",
 *      name="TxType",
 *      description="交易类型（0全部 1转入 2转出）",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="year",
 *      name="year",
 *      description="年份",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * @OA\Parameter(
 *      parameter="month",
 *      name="month",
 *      description="月份",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 */