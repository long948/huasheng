<?php

namespace App\Http\Middleware;

use App\Exceptions\ArException;
use Closure;
use Illuminate\Http\Request;
use TencentCloud\Captcha\V20190722\Models\DescribeCaptchaResultRequest;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Captcha\V20190722\CaptchaClient;

class VerifyCaptcha
{
    public static $key = 'sblw-3hn8-sqoy19sblw-3hn';

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws ArException
     */
    public function handle(Request $request, Closure $next)
    {
        $ticket = $request->input('ticket');
        $randStr = $request->input('randstr');

        if (empty($ticket) || empty($randStr)) {
            throw new ArException(ArException::SELF_ERROR, '参数有误');
        }

        try {

            $SecretId = 'AKID2Wpv9wzP7ax88x7gWBboxud8NTN5enpf';
            $SecretKey = 'tpUaWEhvxrggU3YWFp79xfKJ6sRtCE1Y';

            $cred = new Credential($SecretId, $SecretKey);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("captcha.tencentcloudapi.com");

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new CaptchaClient($cred, "", $clientProfile);

            $CaptchaAppId = 2054205383;
            $AppSecretKey = '0kUmMMuZQpFH2LXylvZjvRw**';

            $req = new DescribeCaptchaResultRequest();
            $params = array(
                "CaptchaType" => 9,
                "Ticket" => $ticket,
                "UserIp" => $request->getClientIp(),
                "Randstr" => $randStr,
                "CaptchaAppId" => $CaptchaAppId,
                "AppSecretKey" => $AppSecretKey
            );

            $req->fromJsonString(json_encode($params));
            $resp = $client->DescribeCaptchaResult($req);
            if ($resp->getCaptchaCode() == 1) {
                return $next($request);
            }
            throw new ArException(ArException::SELF_ERROR, '验证失败');
        } catch (TencentCloudSDKException $e) {
            throw new ArException(ArException::SELF_ERROR, '验证失败');
        }
    }
}
