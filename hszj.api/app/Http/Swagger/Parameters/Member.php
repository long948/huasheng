<?php
/**
 * @OA\Parameter(
 *      parameter="Phone",
 *      name="Phone",
 *      description="手机号",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="Password",
 *      name="Password",
 *      description="密码",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="RepeatPassword",
 *      name="RepeatPassword",
 *      description="重复密码",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="PayPassword",
 *      name="PayPassword",
 *      description="支付密码",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="RepeatPayPassword",
 *      name="RepeatPayPassword",
 *      description="重复支付密码",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="InviteCode",
 *      name="InviteCode",
 *      description="邀请码",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="AuthCode",
 *      name="AuthCode",
 *      description="验证码",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="OldPassword",
 *      name="OldPassword",
 *      description="原密码",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="OldPayPassword",
 *      name="OldPayPassword",
 *      description="原交易密码",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="NickName",
 *      name="NickName",
 *      description="昵称",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="Avatar",
 *      name="Avatar",
 *      description="头像",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="NewPassword",
 *      name="NewPassword",
 *      description="新密码",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="NewPayPassword",
 *      name="NewPayPassword",
 *      description="新交易密码",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="TurnoverType",
 *      name="Type",
 *      description="类型Id 0为全部",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="RealName",
 *      name="Name",
 *      description="持卡人",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="Bank",
 *      name="Bank",
 *      description="开户行",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="Card",
 *      name="Card",
 *      description="卡号",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="IdCard",
 *      name="IdCard",
 *      description="身份证号",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * 
 * @OA\Parameter(
 *      parameter="IdCardImg",
 *      name="IdCardImg",
 *      description="身份证照片,数组字符串",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="AuthName",
 *      name="Name",
 *      description="姓名",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="OpenPayPass",
 *      name="OpenPayPass",
 *      description="交易密码 1关闭 2开启",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="MemberIdCardFrontImage",
 *      name="front_image",
 *      description="身份证正面",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * @OA\Parameter(
 *      parameter="MemberIdCardReverseImage",
 *      name="reverse_image",
 *      description="身份证反面",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * @OA\Parameter(
 *      parameter="MemberIdCardShouFrontImage",
 *      name="shou_fron_image",
 *      description="手持正面照",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 *  @OA\Parameter(
 *      parameter="MemberIdCardVideo",
 *      name="video",
 *      description="自录视频",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * @OA\Parameter(
 *      parameter="MemberIdCardType",
 *      name="type",
 *      description="类型（1orc+手持 2orc+视频 3支付宝）",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="ClientId",
 *      name="ClientId",
 *      description="客户端Id",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="captcha",
 *      name="captcha",
 *      description="图形验证码",
 *      in="query",
 *      required=true,
 *      @OA\Schema(
 *          type="string",
 *      )
 * )
 * 
 * @OA\Parameter(
 *      parameter="auth_status",
 *      name="auth_status",
 *      description="认证状态 0未实名 1已实名",
 *      in="query",
 *      required=false,
 *      @OA\Schema(
 *          type="integer",
 *      )
 * )
 */