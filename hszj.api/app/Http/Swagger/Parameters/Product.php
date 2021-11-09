<?php
/**
 * @OA\Parameter(
 *      parameter="ProductId",
 *      name="Id",
 *      description="矿机Id",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="RushId",
 *      name="RushId",
 *      description="抢购Id",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="OutputType",
 *      name="Type",
 *      description="产出类型 0全部 1挖矿 2邀请 3团队",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="PlanType",
 *      name="Type",
 *      description="类型 0全部 1中奖 2未中奖",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="MyProductType",
 *      name="Type",
 *      description="类型 0全部 1在线 2过期 3新人",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="MemberProductId",
 *      name="Id",
 *      description="我的矿机Id",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="Account",
 *      name="Account",
 *      description="账号",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="QrCode",
 *      name="QrCode",
 *      description="收款二维码",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="AddDate",
 *      name="AddDate",
 *      description="中奖日期",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * 
 */