<?php


namespace App\Services;

use App\Exceptions\ArException;
use App\Models\MembersModel as Members;

class SmsService extends Service
{

    /**
     * @method 设置交易密码验证码
     */
    public function SetPayPassCode(int $uid){
        if($uid <= 0) throw new ArException(ArException::UNKONW);

        $member = Members::find($uid);
        if(empty($member)) throw new ArException(ArException::USER_NOT_FOUND);

        $phone = $member->Phone;

        $this->SendCode('SetPayPass', $phone);
    }

    /**
     * @method 绑定支付验证码
     */
    public function BindPayCode(int $uid){
        if($uid <= 0) throw new ArException(ArException::UNKONW);

        $member = Members::find($uid);
        if(empty($member)) throw new ArException(ArException::USER_NOT_FOUND);

        $phone = $member->Phone;

        $this->SendCode(self::BIND_PAY_CODE, $phone);
    }

    /**
     * @method 忘记交易密码
     * @param $phone 手机号
     */
    public function ModifyPayPassCode($phone){
        preg_match_all("/^1[3456789]\d{9}$/", $phone, $match);
        if(empty($match[0])) throw new ArException(ArException::SELF_ERROR,'手机号错误');
        //是否有用户
        $has = Members::where('Phone', $phone)->first();
        if(empty($has)) throw new ArException(ArException::SELF_ERROR,'该手机号没有注册');

        $this->SendCode('ModifyPayPass', $phone);
    }

    /**
     * @method 忘记密码
     * @param $phone 手机号
     */
    public function ModifyPassCode($phone){
        preg_match_all("/^1[3456789]\d{9}$/", $phone, $match);
        if(empty($match[0])) throw new ArException(ArException::SELF_ERROR,'手机号错误');
        $has = Members::where('Phone', $phone)->first();
        if(empty($has)) throw new ArException(ArException::SELF_ERROR,'该手机号没有注册');

        $this->SendCode('ModifyPass', $phone);
    }

    /**
     * @method 注册
     * @param $phone 手机号
     */
    public function RegisterCode($phone){
        preg_match_all("/^1[3456789]\d{9}$/", $phone, $match);
        if(empty($match[0])) throw new ArException(ArException::SELF_ERROR,'手机号错误');
        $has = Members::where('Phone', $phone)->first();
        if(!empty($has)) throw new ArException(ArException::SELF_ERROR,'该手机号已注册');

        $this->SendCode('AuthCode', $phone);
    }

    /**
     * @method 解绑手机号
     */
    public function unbindCode(int $uid){
        if($uid <= 0) throw new ArException(ArException::UNKONW);

        $member = Members::find($uid);
        if(empty($member)) throw new ArException(ArException::USER_NOT_FOUND);

        $phone = $member->Phone;

        $this->SendCode('unbindCode', $phone);
    }

    /**
     * @method
     * @param $phone 绑定手机号
     */
    public function bindCode($phone){
        preg_match_all("/^1[3456789]\d{9}$/", $phone, $match);
        if(empty($match[0])) throw new ArException(ArException::SELF_ERROR,'手机号错误');
        $has = Members::where('Phone', $phone)->first();
        if(!empty($has)) throw new ArException(ArException::SELF_ERROR,'该手机号已注册');

        $this->SendCode('bindCode', $phone);
    }

    /**
     * 发送验证码
     * @method
     */
    public function vCode($uid){
        if($uid <= 0) throw new ArException(ArException::UNKONW);

        $member = Members::find($uid);
        if(empty($member)) throw new ArException(ArException::USER_NOT_FOUND);

        $phone = $member->Phone;

        $this->SendCode('vcode', $phone);
    }

    /**
     * @method 转账
     * @param int $uid
     * @throws ArException
     */
    public function transfer(int $uid){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        $member = Members::find($uid);
        if(empty($member)) throw new ArException(ArException::USER_NOT_FOUND);
        $phone = $member->Phone;
        $this->SendCode('userTransfer', $phone);
    }
}
