<?php
/**
 * @OA\Parameter(
 *      parameter="sapling_id",
 *      name="sapling_id",
 *      description="树苗编号(树苗列表中提供的)",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 *
 * @OA\Parameter(
 *      parameter="user_sapling_id",
 *      name="user_sapling_id",
 *      description="用户树苗编号(用户树苗列表中)",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 *
 * @OA\Parameter(
 *      parameter="receive_type",
 *      name="receive_type",
 *      description="领取类型 全部(WHOLE),单个（SINGLE）",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 *
 * @OA\Parameter(
 *      parameter="user_sapling_receive_id",
 *      name="user_sapling_receive_id",
 *      description="树苗领取编号 单个领取类型为'SINGLE'时必须（详情中的details数组中单个收益编号）",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * @OA\Parameter(
 *      parameter="sapling_package_id",
 *      name="sapling_package_id",
 *      description="机器人套餐编号(机器人列表中)",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 *
 * @OA\Parameter(
 *      parameter="sapling_package_type",
 *      name="sapling_package_type",
 *      description="购买类型 1.全款 2.分期（暂不扣钱，等有钱了再给）",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 *
 */
