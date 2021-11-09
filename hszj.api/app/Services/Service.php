<?php

namespace App\Services;

use App\Exceptions\ArException;
use App\Libraries\Verify;
use App\Models\CoinModel as Coin;
use App\Models\FinancingMoldModel as FinancingMold;
use App\Models\MemberCoinModel as MemberCoin;
use App\Models\MembersModel as Members;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use zgldh\QiniuStorage\QiniuStorage;

class Service
{
    //引入验证类
    use Verify;

    //绑定支付方式验证码
    const BIND_PAY_CODE = 'BindPayCode';

    protected static $key = 'sblw-3hn8-sqoy19sblw-3hn';

    protected static $juhe_key = '5ca68e2f11164cc54e5a5a037d0b9d0b';

    protected static $_forget_tpl = '216963';

    protected static $_create_trade = '217009';


    //验证支付密码
    public function VerifyPayPass(int $uid, string $pass)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if (empty($pass)) throw new ArException(ArException::SELF_ERROR, '请输入支付密码');

        $member = Members::where('Id', $uid)->first();
        if (empty($member->PayPassword))
            throw new ArException(ArException::SELF_ERROR, '请先设置交易密码');

        if (empty($member)) throw new ArException(ArException::PAY_PASS_ERROR);
        if (!password_verify($pass, $member->PayPassword))
            throw new ArException(ArException::PAY_PASS_ERROR);
    }

    /**
     * @method 获取邀请码
     */
    protected function GetInviteCode()
    {
        //随机生成再去匹配，不存在就添加
        $code = mt_rand(100000, 999999);
        $has = Members::where('InviteCode', $code)->select('Id')->first();
        while (!empty($has)) {
            $code = mt_rand(100000, 999999);
            $has = Members::where('InviteCode', $code)->select('Id')->first();
        }
        return $code;
    }

    /**
     * @method 验证参数格式
     */
    protected function VerifyParamsData(array $data)
    {
        if (!empty($data['Phone']) && !self::PhoneFmt($data['Phone']))
            throw new ArException(ArException::SELF_ERROR, '手机号格式错误');

        if (!empty($data['Password']) && !self::PassFmt($data['Password'], 8, 20))
            throw new ArException(ArException::SELF_ERROR, '密码长度8-20位,需要包含字母和数字');

        if (!empty($data['PayPassword']) && !self::PassFmt($data['PayPassword'], 8, 20))
            throw new ArException(ArException::SELF_ERROR, '交易密码长度8-20位,需要包含字母和数字');

        if (!empty($data['RepeatPassword']) && $data['Password'] != $data['RepeatPassword'])
            throw new ArException(ArException::SELF_ERROR, '两次密码不一致');

        if (!empty($data['RepeatPayPassword']) && $data['PayPassword'] != $data['RepeatPayPassword'])
            throw new ArException(ArException::SELF_ERROR, '两次交易密码不一致');
    }

    /**
     * @method 验证注册参数
     */
    protected function VerifyRegParams(array $data)
    {
        //手机号
        $phone = env('LOGIN_PARAM_PHONE', true);
        if ($phone && empty($data['Phone'])) throw new ArException(ArException::SELF_ERROR, '请填写手机号');
        //密码
        $pass = env('LOGIN_PARAM_PASS', true);
        if ($pass && empty($data['Password'])) throw new ArException(ArException::SELF_ERROR, '请填写密码');
        //重复密码
        $repass = env('LOGIN_PARAM_RE_PASS', true);
        if ($repass && empty($data['RepeatPassword'])) throw new ArException(ArException::SELF_ERROR, '请重复密码');
        //交易密码
        // $paypass = env('LOGIN_PARAM_PAYPASS',true);
        // if($paypass && empty($data['PayPassword'])) throw new ArException(ArException::SELF_ERROR,'请填写交易密码');
        // //重复交易密码
        // $repaypass = env('LOGIN_PARAM_RE_PAYPASS',true);
        // if($repaypass && empty($data['RepeatPayPassword'])) throw new ArException(ArException::SELF_ERROR,'请重复交易密码');
        //邀请码
        $inviteCode = env('LOGIN_PARAM_INVITECODE', true);
        if ($inviteCode && empty($data['InviteCode'])) throw new ArException(ArException::SELF_ERROR, '请填写邀请码');
        //验证码
        $AuthCode = env('LOGIN_PARAM_AUTHCODE', true);
        if ($AuthCode && empty($data['AuthCode'])) throw new ArException(ArException::SELF_ERROR, '请填写验证码');
        //昵称
        // if(empty($data['NickName'])) throw new ArException(ArException::SELF_ERROR,'请填写昵称');

        $this->VerifyParamsData($data);
    }

    //加日志
    protected static function AddLog(int $uid, $money, Coin $coin, string $mold)
    {
        $sort = $uid % 20;
        if ($sort < 10) $sort = '0' . $sort;
        $table = 'FinancingList_' . $sort;
        $fina = FinancingMold::where('call_index', $mold)->first();
        if (empty($fina)) throw new ArException(ArException::SELF_ERROR, 'mold ' . $mold . ' not found');
        $memberCoin = MemberCoin::where('MemberId', $uid)->where('CoinId', $coin->Id)->first();
        if (empty($memberCoin)) throw new ArException(ArException::UNKONW);
        if (bccomp($memberCoin->Money, 0, 10) < 0) throw new ArException(ArException::COIN_NOT_ENOUGH);
        $data = [
            'MemberId' => $uid,
            'Money' => $money,
            'CoinId' => $coin->Id,
            'CoinName' => $coin->EnName,
            'Mold' => $fina->id,
            'MoldTitle' => $fina->title,
            'Remark' => $fina->title,
            'AddTime' => time(),
            'Balance' => $memberCoin->Money
        ];
        DB::table($table)->insert($data);
    }

    /**
     * @method 聚合发送短信
     */
    public function JuHeSms($phone, $code, $tpl)
    {
        $url = "http://v.juhe.cn/sms/send";
        $params = array(
            'key' => self::$juhe_key,
            'mobile' => $phone,
            'tpl_id' => $tpl,
            'tpl_value' => '#code#=' . $code
        );

        $paramstring = http_build_query($params);
        $content = SendRequest($url, $paramstring);
        $result = json_decode($content, true);
        if (!is_array($result)) throw new ArException(ArException::SELF_ERROR, '发送失败');
        if (!isset($result['error_code'])) throw new ArException(ArException::SELF_ERROR, '网络错误，请稍后再试');
        if ($result['error_code'] != 0) throw new ArException(ArException::SELF_ERROR, $result['reason']);
    }

    /**
     * @method 聚合发送短信
     */
    public function JuHeMsg($phone, $tpl)
    {
        $url = "http://v.juhe.cn/sms/send";
        $params = array(
            'key' => self::$juhe_key,
            'mobile' => $phone,
            'tpl_id' => $tpl
        );

        $paramstring = http_build_query($params);
        $content = SendRequest($url, $paramstring);
        $result = json_decode($content, true);
        if (!is_array($result)) throw new ArException(ArException::SELF_ERROR, '发送失败');
        if (!isset($result['error_code'])) throw new ArException(ArException::SELF_ERROR, '网络错误，请稍后再试');
        if ($result['error_code'] != 0) throw new ArException(ArException::SELF_ERROR, $result['reason']);
    }

    /**
     * @method 七牛上传Token 需要配置 config/filesystem.php
     */
    public function QiniuUpload()
    {
        $conf = DB::table('QiniuConfig')->first();
        if (empty($conf)) throw new ArException(ArException::SELF_ERROR, '暂时无法上传');
        config([
            'filesystems.disks.qiniu.domains.default' => $conf->Domain,
            'filesystems.disks.qiniu.access_key' => $conf->AccessKey,
            'filesystems.disks.qiniu.secret_key' => $conf->SecretKey,
            'filesystems.disks.qiniu.bucket' => $conf->Bucket
        ]);
        $disk = QiniuStorage::disk('qiniu');
        $token = $disk->uploadToken();
        $data = [
            'token' => $token,
            'domain' => $conf->Domain,
            'region' => $conf->Region
        ];
        return $data;
    }

    /**
     * @method 获取Token
     * @param string $hash hash值
     */
    protected function MakeToken(int $uid, $hash)
    {
        /**
         * iss：发行人
         * exp：到期时间
         * sub：主题
         * aud：用户
         * nbf：在此之前不可用
         * iat：发布时间
         */
        $jwt = JWT::encode(array(
            "iss" => "system",
            "aud" => "user",
            "nbf" => time(),
            "iat" => time(),
            //不设置过期时间，过期时间交给系统控制
            //"exp" => time() + 30,
            "member_id" => $uid,
            "token" => $hash
        ), self::$key);
        $token = base64_encode(openssl_encrypt($jwt, 'DES-EDE3', self::$key, OPENSSL_RAW_DATA));
        return $token;
    }

    //验证验证码
    public function VerifyAuthCode($key, $phone, $code)
    {
        $auth = Redis::hget($key, $phone);
        if (empty($auth)) throw new ArException(ArException::SELF_ERROR, '请先发送验证码');
        $auth = json_decode($auth, true);
        if (bccomp($auth['verificationCount'], '0') == 0) {
            throw new ArException(ArException::SELF_ERROR, '验证异常,请重新发送');
        }
        $auth['verificationCount'] -= 1;
        Redis::hset($key, $phone, json_encode($auth));

        if (!is_array($auth)) throw new ArException(ArException::SELF_ERROR, '验证码已失效，请重新发送');
        if ($auth['Code'] != $code) throw new ArException(ArException::SELF_ERROR, '验证码错误');
        if ($auth['ExpireTime'] < time()) throw new ArException(ArException::SELF_ERROR, '验证码已过期');
    }

    public function GetRoot($root)
    {
        $root = explode(',', $root);
        unset($root[count($root) - 1]);
        $root = array_values($root);
        unset($root[0]);
        $root = array_values($root);
        return array_reverse($root);
    }

    public function Get9Root($root)
    {
        $root = explode(',', $root);
        unset($root[count($root) - 1]);
        $root = array_values($root);
        unset($root[0]);
        $root = array_values($root);
        $root = array_slice($root, -9);
        return array_reverse($root);
    }

    //发送验证码
    public function SendCode($key, $phone)
    {
        $auth = Redis::hget($key, $phone);
        if (!empty($auth)) {
            $auth = json_decode($auth, true);
            if (is_array($auth)) {
                if ((time() - $auth['SendTime']) < 60) throw new ArException(ArException::SELF_ERROR, '每分钟只能发送一次验证码');
            }
        }

        $time = 10;//有效时间10分钟
        if (env('APP_DEBUG')) {
            $code = 111111;
        } else {
            $code = rand(100000, 999999);
            $this->smsTreasure($phone, $code, $time);
        }
        $auth = [
            'Code' => $code,
            'ExpireTime' => time() + 600,
            'SendTime' => time(),
            'verificationCount' => 3
        ];
        Redis::hset($key, $phone, json_encode($auth));
    }


    /**
     * 短信宝
     * @param $phone
     * @param $code
     * @param $time
     * @return bool
     * @throws ArException
     */
    public function smsTreasure($phone, $code, $time)
    {
        Log::info("短信发送：手机号码:{$phone},发送code：{$code},时间:{$time}");
        $statusStr = array(
            "0" => "短信发送成功",
            "-1" => "参数不全",
            "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
            "30" => "密码错误",
            "40" => "账号不存在",
            "41" => "余额不足",
            "42" => "帐户已过期",
            "43" => "IP地址限制",
            "50" => "内容含有敏感词"
        );
        $smsapi = "http://api.smsbao.com/";
        $user = env('smsTreasureUserName'); //短信平台帐号
        $pass = md5(env('smsTreasureUserPassword')); //短信平台密码
        $content = "【花生世界】您的验证码为{$code}，在{$time}分钟内有效。";//要发送的短信内容
        $sendurl = $smsapi . "sms?u=" . $user . "&p=" . $pass . "&m=" . $phone . "&c=" . urlencode($content);
        $result = file_get_contents($sendurl);
        if ($result == 0) {
            return true;
        }
        throw new ArException(ArException::SELF_ERROR, '短信发送失败,稍后再试!');
    }


    /**
     * 短信通知
     * @param $phone
     * @return bool
     * @throws ArException
     */
    public function smsTreasureMsg($phone)
    {
        $statusStr = array(
            "0" => "短信发送成功",
            "-1" => "参数不全",
            "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
            "30" => "密码错误",
            "40" => "账号不存在",
            "41" => "余额不足",
            "42" => "帐户已过期",
            "43" => "IP地址限制",
            "50" => "内容含有敏感词"
        );
        $smsapi = "http://api.smsbao.com/";
        $user = env('smsTreasureUserName'); //短信平台帐号
        $pass = md5(env('smsTreasureUserPassword')); //短信平台密码
        $content = "【花生世界】尊敬的花生世界用户，您有一笔新的订单正在交易，请尽快处理。";//要发送的短信内容
        $sendurl = $smsapi . "sms?u=" . $user . "&p=" . $pass . "&m=" . $phone . "&c=" . urlencode($content);
        $result = file_get_contents($sendurl);
        if ($result == 0) {
            return true;
        }
        throw new ArException(ArException::SELF_ERROR, $statusStr[$result]);
    }

    public function QiniuDomain()
    {
        $domain = '';
        $qiniu = DB::table('QiniuConfig')->first();
        if (!empty($qiniu)) $domain = $qiniu->Domain;
        return $domain;
    }

}
