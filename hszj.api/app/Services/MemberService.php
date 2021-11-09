<?php

namespace App\Services;

use App\Events\UserRegisterEvent;
use App\Exceptions\ArException;
use App\Models\CoinModel;
use App\Models\FinaceMoldModel as FinaceMold;
use App\Models\MembersModel as Members;
use App\Models\SettingModel;
use App\Services\System\MembersSignService;
use App\Services\User\MinerUserSaplingTotalReleaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class MemberService extends Service
{

    /**
     * @method 支付信息
     */
    public function PayInfo(int $uid)
    {
        $card = DB::table('MemberBindPay')->where('MemberId', $uid)->where('Type', 1)->first();
        $wechat = DB::table('MemberBindPay')->where('MemberId', $uid)->where('Type', 2)->first();
        $alipay = DB::table('MemberBindPay')->where('MemberId', $uid)->where('Type', 3)->first();

        if (!empty($wechat)) $wechat->QrCode = $this->QiniuDomain() . '/' . $wechat->QrCode;
        if (!empty($alipay)) $alipay->QrCode = $this->QiniuDomain() . '/' . $alipay->QrCode;
        $data = [
            'BankCard' => $card,
            'Wechat' => $wechat,
            'Alipay' => $alipay
        ];
        return $data;
    }

    /**
     * @method 开启
     */
    public function Safe(int $uid, int $open)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);

        //关闭交易密码直接清空交易密码
        if ($open == 1) {
            DB::table('Members')->where('Id', $uid)->update([
                'PayPassword' => '',
                'IsOpenPayPass' => 0
            ]);
        } else {
            $member = Members::find($uid);
            if (empty($member->PayPassword))
                throw new ArException(ArException::SELF_ERROR, '请先设置交易密码');
        }
    }

    /**
     * @method 实名认证信息
     */
    public function AuthInfo(int $uid)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        $auth = DB::table('MemberAuth')->where('MemberId', $uid)->orderBy('Id', 'desc')->first();
        if (!empty($auth)) $auth->Imgs = json_decode($auth->Imgs);
        return $auth;
    }

    /**
     * @method 实名认证
     */
    public function Auth(int $uid, $video, $idCard, $name, $type, $front_image)
        // public function Auth(int $uid, $video, $front_image, $reverse_image, $type)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);

        // if ($type == 1 && empty($shou_fron_image))
        //     throw new ArException(ArException::PARAM_ERROR);
        if (empty($front_image))
            throw new ArException(ArException::PARAM_ERROR);
        // if (empty($reverse_image))
        //     throw new ArException(ArException::PARAM_ERROR);
        if (empty($idCard))
            throw new ArException(ArException::PARAM_ERROR);
        if (empty($name))
            throw new ArException(ArException::PARAM_ERROR);
        if ($type == 2 && empty($video))
            throw new ArException(ArException::PARAM_ERROR);

        $info = DB::table("MemberIdCard")->where("MemberId", $uid)->first();
        if (!empty($info->Status) && $info->Status === 2) {
            throw new ArException(ArException::SELF_ERROR, '您已实名成功');
        }

        // if (!IdentityCard::make($idCard))
        //     throw new ArException(ArException::SELF_ERROR, '身份证号码错误');

        // 识别图片
        $Birthday = '2020-01-01';
        $Sex = 1;
        // $name = '123';
        // $idCard = '123123';
        // $appCode = env("AUTH_APP_CODE");
        // $faceFile = $this->QiniuDomain() . $front_image;
        // $authData = AutoRecognitionService::push($appCode, $faceFile, "face");
        // $Birthday = !empty($authData["birth"]) ? date("Y-m-d", strtotime($authData["birth"])) : "";
        // $name = $authData["name"] ?? "";
        // $idCard = $authData["num"] ?? "";
        // $Sex = $authData["sex"] ?? "";
        // $Sex = $Sex == "男" ? 1 : ($Sex == "女" ? 2 : 0);

        $IdCardInfo = DB::table("MemberIdCard")->where("IdCard", $idCard)->first();
        if ($IdCardInfo && $IdCardInfo->MemberId != $uid) {
            throw new ArException(ArException::USER_IDCRAD_CUNZAI);
        }

        $data = [
            "Type" => $type,
            "AuthName" => $name,
            "IdCard" => $idCard,
            "ShouFrontImage" => '',
            "FrontImage" => isset($front_image) ? $front_image : '',
            "ReverseImage" => isset($reverse_image) ? $reverse_image : '',
            "Video" => $video,
            "Sex" => $Sex,
            "Birthday" => $Birthday,
            "Status" => 0,
            "UpdateTime" => date("Y-m-d H:i:s"),
        ];

        DB::beginTransaction();
        try {
            $member = Members::find($uid);
            if (empty($member)) throw new ArException(ArException::USER_NOT_FOUND);
            if ($member->IsAuth != 0) throw new ArException(ArException::SELF_ERROR, '此账号已实名认证');

            if ($info) {
                DB::table("MemberIdCard")->where("MemberId", $uid)->update($data);
            } else {
                $data["MemberId"] = $uid;
                $data["CreateTime"] = date("Y-m-d H:i:s");
                DB::table("MemberIdCard")->insert($data);
            }

            DB::table('Members')->where('Id', $uid)->update(['IsAuth' => 0]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
    }

    /**
     * @method 绑定支付宝
     */
    public function BindAlipay($uid, $account, $qrcode)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if (empty($account)) throw new ArException(ArException::SELF_ERROR, '请填写账号');
        // if (empty($name)) throw new ArException(ArException::SELF_ERROR, '请填写昵称');
        if (empty($qrcode)) throw new ArException(ArException::SELF_ERROR, '请上传收款码');

        $member = Members::find($uid);
        if (empty($member)) throw new ArException(ArException::USER_NOT_FOUND);
        if ($member->IsBindAlipay != 0) throw new ArException(ArException::SELF_ERROR, '此账号已绑定支付宝');

        // if ($account != $member->Phone) throw new ArException(ArException::SELF_ERROR, '账号必须为绑定的手机号');
        //验证验证码
        // $this->VerifyAuthCode(self::BIND_PAY_CODE, $member->Phone, $code);

        DB::beginTransaction();
        try {
            DB::table('Members')->where('Id', $uid)->where('IsBindAlipay', 0)->update(['IsBindAlipay' => 1]);
            DB::table('BindPay')->insert([
                'Account' => $account,
                // 'NickName' => $name,
                'QrCode' => $qrcode,
                'MemberId' => $uid,
                'Type' => 1,
                'PayType' => 2
            ]);
            Redis::hdel(self::BIND_PAY_CODE, $member->Phone);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
    }

    /**
     * @method 修改支付宝
     */
    public function ModifyAlipay($uid, $account, $qrcode)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if (empty($account)) throw new ArException(ArException::SELF_ERROR, '请填写账号');
        // if (empty($name)) throw new ArException(ArException::SELF_ERROR, '请填写昵称');
        if (empty($qrcode)) throw new ArException(ArException::SELF_ERROR, '请上传收款码');

        $member = Members::find($uid);
        if (empty($member)) throw new ArException(ArException::USER_NOT_FOUND);
        if ($member->IsBindAlipay == 0) throw new ArException(ArException::SELF_ERROR, '此账号还未绑定支付宝');

        // if ($account != $member->Phone) throw new ArException(ArException::SELF_ERROR, '账号必须为绑定的手机号');
        //验证验证码
        // $this->VerifyAuthCode(self::BIND_PAY_CODE, $member->Phone, $code);

        DB::beginTransaction();
        try {
            DB::table('BindPay')->where('MemberId', $uid)->where('Type', 1)->where('PayType', 2)->update([
                'Account' => $account,
                // 'NickName' => $name,
                'QrCode' => $qrcode
            ]);
            Redis::hdel(self::BIND_PAY_CODE, $member->Phone);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
    }

    /**
     * @method 绑定微信
     */
    public function BindWeChat($uid, $account, $qrcode)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if (empty($account)) throw new ArException(ArException::SELF_ERROR, '请填写账号');
        // if (empty($name)) throw new ArException(ArException::SELF_ERROR, '请填写昵称');
        if (empty($qrcode)) throw new ArException(ArException::SELF_ERROR, '请上传收款码');

        $member = Members::find($uid);
        if (empty($member)) throw new ArException(ArException::USER_NOT_FOUND);
        if ($member->IsBindWx != 0) throw new ArException(ArException::SELF_ERROR, '此账号已绑定微信');

        //验证验证码
        // $this->VerifyAuthCode(self::BIND_PAY_CODE, $member->Phone, $code);

        DB::beginTransaction();
        try {
            DB::table('Members')->where('Id', $uid)->where('IsBindWx', 0)->update(['IsBindWx' => 1]);
            DB::table('BindPay')->insert([
                'Account' => $account,
                // 'NickName' => $name,
                'QrCode' => $qrcode,
                'MemberId' => $uid,
                'Type' => 1,
                'PayType' => 1
            ]);
            Redis::hdel(self::BIND_PAY_CODE, $member->Phone);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
    }

    /**
     * @method 修改微信
     */
    public function ModifyWeChat($uid, $account, $qrcode)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if (empty($account)) throw new ArException(ArException::SELF_ERROR, '请填写账号');
        // if (empty($name)) throw new ArException(ArException::SELF_ERROR, '请填写昵称');
        if (empty($qrcode)) throw new ArException(ArException::SELF_ERROR, '请上传收款码');

        $member = Members::find($uid);
        if (empty($member)) throw new ArException(ArException::USER_NOT_FOUND);
        if ($member->IsBindWx == 0) throw new ArException(ArException::SELF_ERROR, '此账号还未绑定微信');

        //验证验证码
        // $this->VerifyAuthCode(self::BIND_PAY_CODE, $member->Phone, $code);

        DB::beginTransaction();
        try {
            DB::table('BindPay')->where('MemberId', $uid)->where('Type', 1)->where('PayType', 1)->update([
                'Account' => $account,
                // 'NickName' => $name,
                'QrCode' => $qrcode
            ]);
            Redis::hdel(self::BIND_PAY_CODE, $member->Phone);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
    }

    /**
     * @method 绑定地址
     */
    public function BindAddress($uid, $account)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if (empty($account)) throw new ArException(ArException::SELF_ERROR, '请填写账号');

        $member = Members::find($uid);
        if (empty($member)) throw new ArException(ArException::USER_NOT_FOUND);
        if ($member->IsBindAddress != 0) throw new ArException(ArException::SELF_ERROR, '此账号已绑定USDT地址');

        //验证验证码
        // $this->VerifyAuthCode(self::BIND_PAY_CODE, $member->Phone, $code);

        DB::beginTransaction();
        try {
            DB::table('Members')->where('Id', $uid)->where('IsBindAddress', 0)->update(['IsBindAddress' => 1]);
            DB::table('BindPay')->insert([
                'Account' => $account,
                'NickName' => '',
                'QrCode' => '',
                'MemberId' => $uid,
                'Type' => 1,
                'PayType' => 3
            ]);
            Redis::hdel(self::BIND_PAY_CODE, $member->Phone);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
    }

    /**
     * @method 修改地址
     */
    public function ModifyAddress($uid, $account)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if (empty($account)) throw new ArException(ArException::SELF_ERROR, '请填写账号');

        $member = Members::find($uid);
        if (empty($member)) throw new ArException(ArException::USER_NOT_FOUND);
        if ($member->IsBindAddress == 0) throw new ArException(ArException::SELF_ERROR, '此账号还未绑定USDT地址');

        //验证验证码
        // $this->VerifyAuthCode(self::BIND_PAY_CODE, $member->Phone, $code);

        DB::beginTransaction();
        try {
            DB::table('BindPay')->where('MemberId', $uid)->where('Type', 1)->where('PayType', 3)->update([
                'Account' => $account,
            ]);
            Redis::hdel(self::BIND_PAY_CODE, $member->Phone);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
    }

    /**
     * @method 我的
     */
    public function My(int $uid)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);

        // $product = MemberProduct::where('MemberId', $uid)->where('SurplusDay', '>', 0)->count();
        // $invite = Members::where('ParentId', $uid)->where('IsValid', 1)->count();
        // $trade = DB::table('Trade')->where('MemberId', $uid)->where('State', '<', 3)->count();

        $tx_price = SettingModel::getValueByKey('tx_price');
        $total_assets = DB::table('MemberCoin')->where('CoinName', 'PT')->where('MemberId', $uid)->value('Money') ?? '0.0000';

        $userComputingPowerService = new MinerUserComputingPowerService();

        //我的花田亩数
        $userComputingPower = $userComputingPowerService->userComputingPower($uid);;

        $userGiveAwayService = new UserGiveAwayService();
        $userGiveAwayAmount = $userGiveAwayService->getUserAmount($uid) ?? 0;

        $userTotalReleaseService = new MinerUserSaplingTotalReleaseService();
        $output = $userTotalReleaseService->getAmount($uid);
        $coin = CoinModel::GetByEnName();
        return [
            'power' => $userComputingPower ?? 0,//我的算力
            'output' => $output,//我的产出
            'total_assets' => $total_assets,//总资产
            'total_assets_cny' => bcmul($total_assets, $coin->PriceCny, 4),//总资产cny
            // 'OnlineProduct' => $product,
            // 'ValidInvite' => $invite,
            // 'TradeNumber' => $trade
            'userGiveAwayAmount' => $userGiveAwayAmount
        ];
    }

    /**
     * @method 绑定银行卡
     */
    public function BindBank(int $uid, string $name, string $phone, string $bank, string $card, $code)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if (empty($name)) throw new ArException(ArException::SELF_ERROR, '请填写持卡人');
        if (!isMobile($phone)) throw new ArException(ArException::SELF_ERROR, '手机号错误');
        if (empty($bank)) throw new ArException(ArException::SELF_ERROR, '请填写开户行');
        if (!is_numeric($card)) throw new ArException(ArException::SELF_ERROR, '卡号错误');

        //验证验证码
        $member = Members::where('Id', $uid)->where('IsBan', 0)->first();
        if (empty($member)) throw new ArException(ArException::USER_NOT_FOUND);
        if ($member->IsBindBank != 0)
            throw new ArException(ArException::SELF_ERROR, '您已绑定银行卡');

        //必须先实名认证
        if ($member->IsAuth != 1) throw new ArException(ArException::SELF_ERROR, '请先实名认证');
        //持卡人必须和实名认证为同一个
        $auth = DB::table('MemberAuth')->where('MemberId', $uid)->first();
        if (empty($auth)) throw new ArException(ArException::SELF_ERROR, '此账号还未实名认证');
        if ($name != $auth->Name) throw new ArException(ArException::SELF_ERROR, '持卡人和实名认证人必须一致');

        $this->VerifyAuthCode(self::BIND_PAY_CODE, $member->Phone, $code);

        DB::beginTransaction();
        try {
            $member = Members::where('Id', $uid)->where('IsBan', 0)->first();
            if (empty($member)) throw new ArException(ArException::USER_NOT_FOUND);
            if ($member->IsBindBank != 0)
                throw new ArException(ArException::SELF_ERROR, '您已绑定银行卡');
            DB::table('BankCard')->insert([
                'MemberId' => $uid,
                'Name' => $name,
                'Phone' => $phone,
                'Bank' => $bank,
                'CardNo' => $card,
                'AddTime' => time(),
                'Type' => 1
            ]);
            DB::table('Members')->where('Id', $uid)->update(['IsBindBank' => 1]);
            Redis::hdel(self::BIND_PAY_CODE, $member->Phone);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
    }

    /**
     * @method 修改银行卡
     */
    public function ModifyBank(int $uid, string $name, string $phone, string $bank, string $card, $code)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if (empty($name)) throw new ArException(ArException::SELF_ERROR, '请填写持卡人');
        if (!isMobile($phone)) throw new ArException(ArException::SELF_ERROR, '手机号错误');
        if (empty($bank)) throw new ArException(ArException::SELF_ERROR, '请填写开户行');
        if (!is_numeric($card)) throw new ArException(ArException::SELF_ERROR, '卡号错误');

        //验证验证码
        $member = Members::where('Id', $uid)->where('IsBan', 0)->first();
        if (empty($member)) throw new ArException(ArException::USER_NOT_FOUND);
        if ($member->IsBindBank == 0)
            throw new ArException(ArException::SELF_ERROR, '此账号还未绑定银行卡');

        //必须先实名认证
        if ($member->IsAuth != 1) throw new ArException(ArException::SELF_ERROR, '请先实名认证');
        //持卡人必须和实名认证为同一个
        $auth = DB::table('MemberAuth')->where('MemberId', $uid)->first();
        if (empty($auth)) throw new ArException(ArException::SELF_ERROR, '此账号还未实名认证');
        if ($name != $auth->Name) throw new ArException(ArException::SELF_ERROR, '持卡人和实名认证人必须一致');

        $this->VerifyAuthCode(self::BIND_PAY_CODE, $member->Phone, $code);

        DB::beginTransaction();
        try {
            $member = Members::where('Id', $uid)->where('IsBan', 0)->first();
            if (empty($member)) throw new ArException(ArException::USER_NOT_FOUND);
            DB::table('BankCard')->where('MemberId', $uid)->where('Type', 1)->update([
                'Name' => $name,
                'Phone' => $phone,
                'Bank' => $bank,
                'CardNo' => $card
            ]);
            Redis::hdel(self::BIND_PAY_CODE, $member->Phone);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
    }

    /**
     * @method 用户信息
     * @param int $uid
     */
    public function Info(int $uid)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        $member = Members::where('Id', $uid)->first();
        if (empty($member)) throw new ArException(ArException::USER_NOT_FOUND);

        $this->updateTxLevel($uid);
        $this->updateTxLevel($member->ParentId);
        $this->updateFrozenCTCSellStatus($uid);
        $member = Members::where('Id', $uid)->first();

        $msg = '';
        if ($member->IsAuth == 1) {
            $auth = 3;
        } else {
            $idcard = DB::table('MemberIdCard')->where('MemberId', $uid)->first();
            if ($idcard) {
                switch ($idcard->Status) {
                    case 0:
                        $auth = 1;//认证中
                        break;
                    case 1:
                        $auth = 2;//认证失败
                        $msg = $idcard->Message;
                        break;
                    case 2:
                        $auth = 3;//认证通过
                        break;
                }
            } else {
                $auth = 0;//未认证
            }
        }

        $card = DB::table('BankCard')->where('MemberId', $uid)->first();
        $wechat = DB::table('BindPay')->where('MemberId', $uid)->where('Type', 1)->first();
        $alipay = DB::table('BindPay')->where('MemberId', $uid)->where('Type', 2)->first();
        $address = DB::table('BindPay')->where('MemberId', $uid)->where('Type', 3)->first();

        if (!empty($wechat)) $wechat->QrCode = $this->QiniuDomain() . '/' . $wechat->QrCode;
        if (!empty($alipay)) $alipay->QrCode = $this->QiniuDomain() . '/' . $alipay->QrCode;

        $info = [
            'Id' => $member->Id,
            'NickName' => $member->NickName,
            'Phone' => $member->Phone,
            'SubPhone' => replaceMobile($member->Phone),
            'Avatar' => empty($member->Avatar) ? '' : $this->QiniuDomain() . $member->Avatar,
            'InviteCode' => $member->InviteCode,
            'RegTime' => $member->RegTime,
            'IsBindBank' => $member->IsBindBank,
            'IsBindWx' => $member->IsBindWx,
            'IsBindAlipay' => $member->IsBindAlipay,
            'IsBindAddress' => $member->IsBindAddress,
            'IsSetPayPass' => empty($member->PayPassword) ? 0 : 1,
            'IsBindGoogle' => $member->IsBindGoogle,
            'IsOpenPayPass' => $member->IsOpenPayPass,
            'IsOpenPhone' => $member->IsOpenPhone,
            'IsAuth' => $member->IsAuth,
            // 'Level' => $member->Level,
            'Level' => 'E' . ($member->Level + 1),
            'Power' => 0,
            'Output' => 0,
            'auth_status' => $auth,
            'auth_msg' => $msg,
            'BankCard' => $card,
            'Wechat' => $wechat,
            'Alipay' => $alipay,
            'Address' => $address,
            'isPartner' => $member->isPartner,
            'IsOpenFee' => $member->IsOpenFee,
            'Fee' => $member->Fee,
            'IsFrozenCTCSell' => $member->IsFrozenCTCSell,
            'IsFrozenCTC' => $member->IsFrozenCTC,
            'notify' => DB::table('Notice')->where('MemberId', $uid)->where('IsRead', 0)->count(),
            'realName' => $data['name'] = DB::table('MemberIdCard')->where('MemberId', $uid)->value('AuthName') ?? '暂无',
            'isSign' => (new MembersSignService())->dayIsSign($uid)
        ];

        return $info;
    }

    /**
     * @method 忘记交易密码
     * @param $code 验证码
     * @param $phone 手机号
     * @param $pass 新交易密码
     * @param $repass 重复交易密码
     */
    public function ForgetPayPassword($code, $phone, $pass, $repass)
    {
        $auth = Redis::hget('ModifyPayPass', $phone);
        if (empty($auth)) throw new ArException(ArException::SELF_ERROR, '请先发送验证码');
        $auth = json_decode($auth, true);

        if (bccomp($auth['verificationCount'], '0') == 0) {
            throw new ArException(ArException::SELF_ERROR, '验证异常,请重新发送');
        }
        $auth['verificationCount'] -= 1;
        Redis::hset('ModifyPayPass', $phone, json_encode($auth));

        if (!is_array($auth)) throw new ArException(ArException::SELF_ERROR, '验证码已失效，请重新发送');
        if ($auth['Code'] != $code) throw new ArException(ArException::SELF_ERROR, '验证码错误');
        if ($auth['ExpireTime'] < time()) throw new ArException(ArException::SELF_ERROR, '验证码已过期');

        if (strlen($pass) < 8 || strlen($pass) > 20) throw new ArException(ArException::SELF_ERROR, '交易密码长度8-20位');
        if (!ctype_alnum($pass)) throw new ArException(ArException::SELF_ERROR, '交易密码只能包含数字和字母');
        if ($pass != $repass) throw new ArException(ArException::SELF_ERROR, '两次交易密码不一致');

        $member = Members::where('Phone', $phone)->first();
        if (empty($member)) throw new ArException(ArException::USER_NOT_FOUND);

        $password = password_hash($pass, PASSWORD_DEFAULT);
        DB::table('Members')->where('Phone', $phone)->update(['PayPassword' => $password, 'IsOpenPayPass' => 1]);
    }

    /**
     * @method 忘记密码
     * @param $code 验证码
     * @param $phone 手机号
     * @param $pass 新密码
     * @param $repass 重复密码
     */
    public function ForgetPassword($code, $phone, $pass, $repass)
    {
        $auth = Redis::hget('ModifyPass', $phone);
        if (empty($auth)) throw new ArException(ArException::SELF_ERROR, '请先发送验证码');
        $auth = json_decode($auth, true);

        if (bccomp($auth['verificationCount'], '0') == 0) {
            throw new ArException(ArException::SELF_ERROR, '验证异常,请重新发送');
        }
        $auth['verificationCount'] -= 1;
        Redis::hset('ModifyPass', $phone, json_encode($auth));

        if (!is_array($auth)) throw new ArException(ArException::SELF_ERROR, '验证码已失效，请重新发送');
        if ($auth['Code'] != $code) throw new ArException(ArException::SELF_ERROR, '验证码错误');
        if ($auth['ExpireTime'] < time()) throw new ArException(ArException::SELF_ERROR, '验证码已过期');

        if (strlen($pass) < 8 || strlen($pass) > 20) throw new ArException(ArException::SELF_ERROR, '密码长度8-20位');
        if (!ctype_alnum($pass)) throw new ArException(ArException::SELF_ERROR, '密码只能包含数字和字母');
        if (ctype_digit($pass)) throw new ArException(ArException::SELF_ERROR, '密码只能包含数字和字母');
        if (ctype_alpha($pass)) throw new ArException(ArException::SELF_ERROR, '密码只能包含数字和字母');
        if ($pass != $repass) throw new ArException(ArException::SELF_ERROR, '两次密码不一致');

        $member = Members::where('Phone', $phone)->first();
        if (empty($member)) throw new ArException(ArException::USER_NOT_FOUND);

        $password = password_hash($pass, PASSWORD_DEFAULT);
        DB::table('Members')->where('Phone', $phone)->update(['Password' => $password]);
    }

    /**
     * @method 修改头像
     * @param int $uid 用户Id
     * @param $avatar 头像
     */
    public function ModifyAvatar(int $uid, $avatar)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if (empty($avatar)) throw new ArException(ArException::SELF_ERROR, '请上传头像');

        DB::table('Members')->where('Id', $uid)->update(['Avatar' => $avatar]);
    }

    /**
     * @method 修改昵称
     * @param int $uid 用户Id
     * @param $name 昵称
     */
    public function ModifyNickName(int $uid, string $name)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if (empty($name)) throw new ArException(ArException::SELF_ERROR, '请填写昵称');

        DB::table('Members')->where('Id', $uid)->update(['NickName' => $name]);
    }

    /**
     * @method 修改交易密码
     * @param int $uid 用户ID
     * @param $password 密码
     * @param $repeatPassword 重复密码
     */
    public function ModifyPayPassword(int $uid, $oldPassword, $password, $repeatPassword, $AuthCode)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if (empty($password)) throw new ArException(ArException::SELF_ERROR, '请输入交易密码');
        if (strlen($password) < 8 || strlen($password) > 20) throw new ArException(ArException::SELF_ERROR, '交易密码长度8-20位');
        if (!ctype_alnum($password)) throw new ArException(ArException::SELF_ERROR, '交易密码只能包含数字和字母');
        if ($password !== $repeatPassword) throw new ArException(ArException::SELF_ERROR, '两次交易密码不一致');

        //验证
        $member = Members::where('Id', $uid)->first();
        $auth = Redis::hget('vcode', $member->Phone);
        if (empty($auth)) throw new ArException(ArException::SELF_ERROR, '请先发送验证码');
        $auth = json_decode($auth, true);

        if (bccomp($auth['verificationCount'], '0') == 0) {
            throw new ArException(ArException::SELF_ERROR, '验证异常,请重新发送');
        }
        $auth['verificationCount'] -= 1;
        Redis::hset('vcode', $member->Phone, json_encode($auth));

        if (!is_array($auth)) throw new ArException(ArException::SELF_ERROR, '验证码已失效，请重新发送');
        if ($auth['Code'] != $AuthCode) throw new ArException(ArException::SELF_ERROR, '验证码错误');
        if ($auth['ExpireTime'] < time()) throw new ArException(ArException::SELF_ERROR, '验证码已过期');

        if (empty($member)) throw new ArException(ArException::UNKONW);
        if (!password_verify($oldPassword, $member->PayPassword))
            throw new ArException(ArException::SELF_ERROR, '原交易密码错误');
        if (password_verify($password, $member->PayPassword))
            throw new ArException(ArException::SELF_ERROR, '新交易密码不得与原交易密码相同');

        $payPassword = password_hash($password, PASSWORD_DEFAULT);
        $res = DB::table('Members')->where('Id', $uid)->update(['PayPassword' => $payPassword, 'IsOpenPayPass' => 1]);
        if (empty($res)) throw new ArException(ArException::SELF_ERROR, '修改失败,请稍后再试');
    }

    /**
     * @method 设置交易密码
     * @param int $uid 用户ID
     * @param $password 密码
     * @param $repeatPassword 重复密码
     */
    public function SetPayPassword(int $uid, $password, $repeatPassword, $code)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if (empty($password)) throw new ArException(ArException::SELF_ERROR, '请输入交易密码');
        if (strlen($password) < 8 || strlen($password) > 20) throw new ArException(ArException::SELF_ERROR, '交易密码长度8-20位');
        if (!ctype_alnum($password)) throw new ArException(ArException::SELF_ERROR, '交易密码只能包含数字和字母');
        if ($password !== $repeatPassword) throw new ArException(ArException::SELF_ERROR, '两次交易密码不一致');

        //验证
        $member = Members::where('Id', $uid)->first();
        if (empty($member)) throw new ArException(ArException::UNKONW);
        if (!empty($member->PayPassword)) throw new ArException(ArException::SELF_ERROR, '此账号已经设置交易密码');

        $this->VerifyAuthCode('SetPayPass', $member->Phone, $code);

        $payPassword = password_hash($password, PASSWORD_DEFAULT);
        $res = DB::table('Members')->where('Id', $uid)->update(['PayPassword' => $payPassword, 'IsOpenPayPass' => 1]);
        if (empty($res)) throw new ArException(ArException::SELF_ERROR, '修改失败,请稍后再试');
    }

    /**
     * @method 修改密码
     * @param int $uid 用户ID
     * @param $password 密码
     * @param $repeatPassword 重复密码
     */
    public function ModifyPassword(int $uid, $oldPassword, $password, $repeatPassword, $AuthCode)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if (empty($password)) throw new ArException(ArException::SELF_ERROR, '请输入密码');
        if (strlen($password) < 8 || strlen($password) > 20) throw new ArException(ArException::SELF_ERROR, '密码长度8-20位');
        if (!ctype_alnum($password)) throw new ArException(ArException::SELF_ERROR, '密码只能包含数字和字母');
        if (ctype_digit($password)) throw new ArException(ArException::SELF_ERROR, '密码只能包含数字和字母');
        if (ctype_alpha($password)) throw new ArException(ArException::SELF_ERROR, '密码只能包含数字和字母');
        if ($password !== $repeatPassword) throw new ArException(ArException::SELF_ERROR, '两次密码不一致');

        //验证
        $member = Members::where('Id', $uid)->first();
        $auth = Redis::hget('vcode', $member->Phone);

        if (empty($auth)) {
            throw new ArException(ArException::SELF_ERROR, '请先发送验证码');
        }

        $auth = json_decode($auth, true);

        if (bccomp($auth['verificationCount'], '0') == 0) {
            throw new ArException(ArException::SELF_ERROR, '验证异常,请重新发送');
        }

        $auth['verificationCount'] -= 1;
        Redis::hset('vcode', $member->Phone, json_encode($auth));

        if (!is_array($auth)) throw new ArException(ArException::SELF_ERROR, '验证码已失效，请重新发送');
        if ($auth['Code'] != $AuthCode) throw new ArException(ArException::SELF_ERROR, '验证码错误');
        if ($auth['ExpireTime'] < time()) throw new ArException(ArException::SELF_ERROR, '验证码已过期');
        if (empty($member)) throw new ArException(ArException::UNKONW);
        if (!password_verify($oldPassword, $member->Password)) throw new ArException(ArException::SELF_ERROR, '原密码错误');
        if (password_verify($password, $member->Password)) throw new ArException(ArException::SELF_ERROR, '新密码不得与原密码相同');

        $password = password_hash($password, PASSWORD_DEFAULT);
        $res = DB::table('Members')->where('Id', $uid)->update(['Password' => $password]);
        if (empty($res)) throw new ArException(ArException::SELF_ERROR, '修改失败,请稍后再试');
    }

    /**
     * @method 登录
     * @param $phone 电话
     * @param $pass 密码
     * @throws ArException
     */
    public function Login($phone, $pass, $client_id)
    {
        $loginCount = Cache::get("login:{$phone}");
        if ($loginCount >= 9) {
            throw new ArException(ArException::SELF_ERROR, '密码错误次数过多,一小时后再试');
        }

        $loginCount = Cache::get("login:24:{$phone}");
        if ($loginCount >= 20) {
            throw new ArException(ArException::SELF_ERROR, '密码错误次数过多,已冻结24小时');
        }

        preg_match_all("/^1[3456789]\d{9}$/", $phone, $match);
        if (empty($match[0])) {
            Cache::set("login:{$phone}", ($loginCount + 1), 3600);
            Cache::set("login:24:{$phone}", ($loginCount + 1), 86400);
            throw new ArException(ArException::SELF_ERROR, '手机号格式错误');
        }
        //验证用户、密码
        $member = Members::where('Phone', $phone)->first();
        if (empty($member)) {
            Cache::set("login:{$phone}", ($loginCount + 1), 3600);
            Cache::set("login:24:{$phone}", ($loginCount + 1), 86400);
            throw new ArException(ArException::ACCOUNT_NOT_FOUND);
        }

        if (!password_verify($pass, $member->Password)) {
            Cache::set("login:{$phone}", ($loginCount + 1), 3600);
            Cache::set("login:24:{$phone}", ($loginCount + 1), 86400);
            throw new ArException(ArException::ACCOUNT_NOT_FOUND);
        }

        if ($member->IsBan != 0) {
            Cache::set("login:{$phone}", ($loginCount + 1), 3600);
            Cache::set("login:24:{$phone}", ($loginCount + 1), 86400);
            throw new ArException(ArException::USER_BE_BAN);
        }

        //token
        $hash = md5(microtime(true));
        $token = $this->MakeToken($member->Id, $hash);
        $res = DB::table('MemberToken')->updateOrInsert([
            'MemberId' => $member->Id
        ], [
            'Token' => $hash,
            'ExpireTime' => time() + 7 * 86400,  //过期时间七天
            'Mold' => 1
        ]);

        if (empty($res)) {
            throw new ArException(ArException::NET_WORK_ERROR);
        }
        DB::table('Members')->where('Id', $member->Id)->update(['ClientId' => $client_id]);
        return $token;
    }

    /**
     * @method 注册
     */
    public function Register(array $data)
    {
        //验证参数
        $this->VerifyRegParams($data);

        $parentId = 0;
        $root = [];
        //邀请码
        if (!empty($data['InviteCode'])) {
            $pMember = Members::where('InviteCode', $data['InviteCode'])->first();
            if (empty($pMember)) throw new ArException(ArException::SELF_ERROR, '邀请码错误');
            $parentId = $pMember->Id;
            $root = trim($pMember->Root);
            if (empty($root))
                $root = ",{$pMember->Id},";
            else
                $root .= "{$pMember->Id},";
        }
        //手机号
        if (!empty($data['Phone'])) {
            $has = Members::where('Phone', $data['Phone'])->first();
            if (!empty($has)) throw new ArException(ArException::SELF_ERROR, '账号已存在');
        }
        //验证码
        if (!empty($data['AuthCode'])) {
            $auth = Redis::hget('AuthCode', $data['Phone']);
            if (empty($auth)) throw new ArException(ArException::SELF_ERROR, '请先发送验证码');
            $auth = json_decode($auth, true);
            if (!is_array($auth)) throw new ArException(ArException::SELF_ERROR, '验证码已失效，请重新发送');
            if ($auth['Code'] != $data['AuthCode']) throw new ArException(ArException::SELF_ERROR, '验证码错误');
            if ($auth['ExpireTime'] < time()) throw new ArException(ArException::SELF_ERROR, '验证码已过期');
        }
        $payPass = '';
        if (isset($data['PayPassword']) && !empty($data['PayPassword']))
            $payPass = password_hash($data['PayPassword'], PASSWORD_DEFAULT);
        //生成邀请码
        $inviteCode = $this->GetInviteCode();

        $ctc_level_rule = SettingModel::getValueByKey('ctc_level_rule');
        $rule = json_decode($ctc_level_rule, true);
        $data = [
            'Phone' => $data['Phone'],
            'NickName' => $data['Phone'],
            'Password' => password_hash($data['Password'], PASSWORD_DEFAULT),
            'PayPassword' => $payPass,
            'ParentId' => $parentId,
            'Root' => $root,
            'RegTime' => time(),
            'RegIp' => ip2long($data['Ip']),
            'InviteCode' => $inviteCode,
            'IsOpenPayPass' => $payPass ? 1 : 0,
            'IsOpenFee' => 1,
            'Fee' => $rule[0]['rate'],
        ];
        $uid = DB::table('Members')->insertGetId($data);
        //删除Redis保存的数据
        Redis::hdel('AuthCode', $data['Phone']);
        event(new UserRegisterEvent($uid));
        $this->addMemberCoin($uid);
    }

    public function addMemebrCoin($uid)
    {
        $coins = DB::table('Coin')->get();
        foreach ($coins as $coin) {
            $coinExists = DB::table('MemberCoin')->where('MemberId', $uid)->where('CoinId', $coin->Id)->exists();
            if ($coinExists) {
                continue;
            }
            DB::table('MemberCoin')->insert([
                'CoinId' => $coin->Id,
                'CoinName' => $coin->EnName,
                'MemberId' => $uid,
                'Address' => $this->generate_str(),
            ]);
        }
    }

    public function generate_str($length = 40)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        if (DB::table('MemberCoin')->where('Address', '0x' . $str)->first()) $this->generate_str(40);
        return '0x' . $str;
    }

    /**
     * @method 获得直推人数、团队人数
     * @param int $uid 用户Id
     */
    public function Group(int $uid)
    {
        //等级
        $member = Members::where('Id', $uid)->first();
        if (empty($member)) throw new ArException(ArException::USER_NOT_FOUND);
        //有效直推
        $invite = Members::where('ParentId', $uid)->where('IsAuth', 1)->count();
        $team = Members::where('Root', 'like', '%,' . $uid . ',%')->count();

        return [
            'ValidInvite' => $invite,
            'Team' => $team,
            'TeamPower' => 0,
            'TeamExchange' => 0,
        ];
    }

    /**
     * @method 获得直推列表
     * @param int $uid 用户Id
     * @param int $count 分页参数
     */
    public function InviteList(int $uid, int $count, $auth_status)
    {
        //直推人数
        $zt = Members::where('ParentId', $uid)->where('IsAuth', $auth_status)->paginate($count);
        $list = [];
        foreach ($zt as $item) {
            $team = Members::where('Root', 'like', '%,' . $item->Id . ',%')->count();
            $list[] = [
                'Id' => $item->Id,
                'Name' => $item->NickName,
                'Phone' => replaceMobile($item->Phone),
                'RealPhone' => $item->Phone,
                'Avatar' => $item->Avatar ? $this->QiniuDomain() . $item->Avatar : '',
                'Team' => $team,
                'Achievement' => $item->TeamAchievement,
                'Level' => $item->Level,
                'Power' => DB::table('miner_user_computing_power')->where('user_id', $item->Id)->where('end_time', '>=', dateFormat())->sum('computing_power'),
                'IsAuth' => $item->IsAuth,
            ];
        }
        return ['list' => $list, 'total' => $zt->total()];
    }

    /**
     * @method 资金变动列表
     * @param int $tpye 变动类型
     */
    public function List(int $uid, int $type, int $tx_type, $min, $max, $year, $month, $coinId, int $count)
    {
        $sort = $uid % 20;
        if ($sort < 10) $sort = '0' . $sort;
        $table = 'FinancingList_' . $sort;
        $list = DB::table($table)->where('MemberId', $uid);
        if ($type > 0) $list = $list->where('Mold', $type);

        switch ($tx_type) {
            case 1:
                $list->where('Money', '>', 0);
                break;
            case 2:
                $list->where('Money', '<', 0);
                break;
        }
        if ($min > 0 && $max > 0 && $min >= $max) throw new ArException(ArException::SELF_ERROR, '请正确输入交易数量');
        if ($min > 0) $list->where(DB::raw('abs(Money)'), '>=', $min);
        if ($max > 0) $list->where(DB::raw('abs(Money)'), '<=', $max);

        if ($year > 0 && $month > 0) {
            $start = Carbon::create($year, $month, '01')->getTimestamp();
            $end = Carbon::create($year, $month, '01')->addMonths(1)->subDays(1)->endOfDay()->getTimestamp();
            if ($start > 0) $list->where('AddTime', '>=', $start);
            if ($end > 0) $list->where('AddTime', '<=', $end);
        }

        if ($coinId > 0) {
            $list->where('CoinId', $coinId);
        }

        $list = $list->orderBy('Id', 'desc')->paginate($count);
        $data = [];
        $coin = CoinModel::getCoinAll();
        foreach ($list as $item) {
            $team = [
                'Id' => $item->Id,
                'CoinName' => $item->CoinName,
                'AddTime' => $item->AddTime,
                'MoldTitle' => $item->MoldTitle,
                'Money' => $item->Money,
                'Balance' => $item->Balance,
                'Remark' => $item->Remark
            ];
            $team['Logo'] = '';
            foreach ($coin as $c) {
                if ($item->CoinId = $c->Id) {
                    $team['Logo'] = $c->Logo;
                }
            }
            $data[] = $team;
        }
        return ['list' => $data, 'total' => $list->total()];
    }

    /**
     * @method 资金变动类型
     */
    public function Molds()
    {
        $list = FinaceMold::get();
        return $list;
    }

    /**
     * 更新冻结出售状态
     */
    public function updateFrozenCTCSellStatus(int $user_id, $direct_update = false)
    {
        $user = DB::table('Members')->where('Id', $user_id)->first();
        if ($user && $user->IsFrozenCTCSell == 1 && $direct_update == true) {
            DB::table('Members')->where('Id', $user_id)->update([
                'IsFrozenCTCSell' => 0
            ]);
            return true;
        }

        if ($user && $user->IsFrozenCTCSell && $direct_update == false) {
            $limit = SettingModel::getValueByKey('sell_limit_invite');
            $count = DB::table('Members')->where('ParentId', $user_id)->where('IsAuth', 1)->count();
            if ($count >= $limit) {
                DB::table('Members')->where('Id', $user_id)->update([
                    'IsFrozenCTCSell' => 0
                ]);
                return true;
            }
        }
        return false;
    }

    public function txPower(int $uid)
    {
        $user = DB::table('Members')->where('Id', $uid)->first();
        $ctc_level_rule = SettingModel::getValueByKey('ctc_level_rule');
        $rule = json_decode($ctc_level_rule, true);

        $user_info = [];
        $user_info['now_level'] = $rule[$user->Level]['name'];
        $user_info['target_level'] = $rule[$user->Level]['name'];
        $user_info['now_invite'] = DB::table('Members')->where('ParentId', $uid)->where('IsAuth', 1)->count() ?? 0;
        $user_info['target_invite'] = $rule[$user->Level]['invite'];
        $user_info['invite_rate'] = $user_info['now_invite'] ? min(1, bcdiv($user_info['now_invite'], $user_info['target_invite'] ?: 1, 4)) : 0;
        $user_info['now_buy'] = (int)DB::table('CTCTrade')->where('OrderMemberId', $uid)->where('Type', 2)->where('State', 2)->sum('Number');
        $user_info['target_buy'] = $rule[$user->Level]['buy'];
        $user_info['buy_rate'] = $user_info['now_buy'] ? min(1, bcdiv($user_info['now_buy'], $user_info['target_buy'] ?: 1, 4)) : 0;

        return [
            'rule' => $rule,
            'user_info' => $user_info,
        ];
    }

    public function ModifyPhone(int $uid, int $OldAuthCode, int $NewAuthCode, int $NewPhone)
    {
        if ($OldAuthCode <= 0 || $NewAuthCode <= 0) throw new ArException(ArException::SELF_ERROR, '请输入验证码');
        if ($NewPhone <= 0) throw new ArException(ArException::SELF_ERROR, '请输入验证码');
        // //验证码
        $user = Members::where('Id', $uid)->first();
        $auth = Redis::hget('unbindCode', $user->Phone);
        if (empty($auth)) throw new ArException(ArException::SELF_ERROR, '请先发送验证码');
        $auth = json_decode($auth, true);
        if (!is_array($auth)) throw new ArException(ArException::SELF_ERROR, '验证码已失效，请重新发送');
        if ($auth['Code'] != $OldAuthCode) throw new ArException(ArException::SELF_ERROR, '验证码错误');
        if ($auth['ExpireTime'] < time()) throw new ArException(ArException::SELF_ERROR, '验证码已过期');

        $new_auth = Redis::hget('bindCode', $NewPhone);
        if (empty($new_auth)) throw new ArException(ArException::SELF_ERROR, '请先发送验证码');
        $new_auth = json_decode($new_auth, true);
        if (!is_array($new_auth)) throw new ArException(ArException::SELF_ERROR, '验证码已失效，请重新发送');
        if ($new_auth['Code'] != $NewAuthCode) throw new ArException(ArException::SELF_ERROR, '验证码错误');
        if ($new_auth['ExpireTime'] < time()) throw new ArException(ArException::SELF_ERROR, '验证码已过期');

        $result = Members::where('Id', $uid)->update([
            'Phone' => $NewPhone
        ]);
        if (!$result) throw new ArException(ArException::SELF_ERROR, '更新失败');
        return true;
    }

    public function updateTxLevel($uid)
    {
        if ($uid <= 0) return true;
        $user = DB::table('Members')->where('Id', $uid)->first();
        if (!$user) return true;
        $ctc_level_rule = SettingModel::getValueByKey('ctc_level_rule');
        $rule = json_decode($ctc_level_rule, true);

        $level = $user->Level;
        $rate = 0;
        $user_info = [];
        foreach ($rule as $k => $v) {
            if (($user->Level + 1) == $v['level']) {
                if (isset($rule[$k + 1])) {
                    $user_info['now_level'] = $v['name'];
                    $user_info['target_level'] = $rule[$k + 1]['name'];
                    $user_info['now_invite'] = DB::table('Members')->where('ParentId', $uid)->where('IsAuth', 1)->count() ?? 0;
                    $user_info['target_invite'] = $rule[$k + 1]['invite'];
                    $user_info['invite_rate'] = $user_info['now_invite'] ? min(1, bcdiv($user_info['now_invite'], $user_info['target_invite'], 4)) : 0;
                    $user_info['now_buy'] = (int)DB::table('CTCTrade')->where('OrderMemberId', $uid)->where('Type', 2)->where('State', 2)->sum('Number');
                    $user_info['target_buy'] = $rule[$k + 1]['buy'];
                    $user_info['buy_rate'] = $user_info['now_buy'] ? min(1, bcdiv($user_info['now_buy'], $user_info['target_buy'], 4)) : 0;
                    if ($user_info['invite_rate'] == 1 || $user_info['buy_rate'] == 1) {
                        $level = $user->Level + 1;
                        $rate = $rule[$k + 1]['rate'];
                    }
                }
            }
        }

        if ($level != $user->Level) {
            DB::table('Members')->where('Id', $uid)->update([
                'Level' => $level,
                'IsOpenFee' => 1,
                'Fee' => $rate,
            ]);
        }
        return true;
    }
}
