<?php
/**
 * @OA\Schema(
 *      schema="userHome",
 *      type="object",
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(property="status", description="状态", type="integer", default="1"),
 *              @OA\Property(property="msg", description="信息", type="string", default="操作成功"),
 *              @OA\Property(
 *                  property="data",
 *                  type="object",
 *                  allOf={
 *                      @OA\Schema(
 *                          @OA\Property(property="sapling", description="树苗本身", type="integer"),
 *                          @OA\Property(property="details", description="根据details中对象生成气泡，如is_watering=0 说明details下的收益未浇水，不能领取", type="integer"),
 *                          @OA\Property(property="user", description="用户周边信息", type="integer"),
 *                          @OA\Property(property="package", description="用户的机器人", type="integer"),
 *                          @OA\Property(property="level", description="用户的等级", type="integer"),
 *                          @OA\Property(property="other", description="其他公共，规则等", type="integer"),

 *                      )
 *                  }
 *              )
 *          )
 *      }
 *  )
 */
