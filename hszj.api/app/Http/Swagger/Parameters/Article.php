<?php
/**
 * @OA\Parameter(
 *      parameter="ArticleCallIndex",
 *      name="CallIndex",
 *      description="文章索引（商学院school 用户协议user_agreement 关于我们about_us 交易规则tx_rule）",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * @OA\Parameter(
 *      parameter="ArticleId",
 *      name="Id",
 *      description="文章ID",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 *
 * @OA\Parameter(
 *      parameter="keyword",
 *      name="keyword",
 *      description="搜索关键词",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 */
