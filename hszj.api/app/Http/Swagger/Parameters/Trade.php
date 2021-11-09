<?php
/**
 * @OA\Parameter(
 *      parameter="PayMethod",
 *      name="PayMethod",
 *      description="支付方式 1银行卡 2支付宝 3微信 4USDT地址",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="TradeType",
 *      name="Type",
 *      description="0全部 1买 2卖",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="OrderNumber",
 *      name="OrderNumber",
 *      description="订单号",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="CTCType",
 *      name="Type",
 *      description="交易类型 1出售 2求购",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="Price",
 *      name="Price",
 *      description="价格",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="MinMoney",
 *      name="MinMoney",
 *      description="最低限额",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="MaxMoney",
 *      name="MaxMoney",
 *      description="最大限额",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="IsBank",
 *      name="IsBank",
 *      description="是否支持银行卡",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="IsWechat",
 *      name="IsWechat",
 *      description="是否支持微信",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="IsAlipay",
 *      name="IsAlipay",
 *      description="是否支持支付宝",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="IsAddress",
 *      name="IsAddress",
 *      description="是否支持USDT地址",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 *  @OA\Parameter(
 *      parameter="OrderId",
 *      name="Id",
 *      description="订单Id",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="Imgs",
 *      name="Imgs",
 *      description="图片 数组字符串格式",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="filter",
 *      name="filter",
 *      description="筛选条件(eg:1-10)",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 */