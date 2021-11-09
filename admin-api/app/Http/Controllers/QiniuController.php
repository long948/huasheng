<?php
/**
 * Created by PhpStorm.
 * User: ChenJulong
 * Date: 2019/8/26
 * Time: 10:00
 */

namespace App\Http\Controllers;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class QiniuController extends Controller
{


    /**
     * Notes:获取七牛TOKEN给前端进行图片上传
     */
    public function tokenGet()
    {
        $bucket         = $this->config['Bucket'];
        $accessKey      = $this->config['AccessKey'];
        $secretKey      = $this->config['SecretKey'];
        $auth           = new Auth($accessKey, $secretKey);
//        $upToken        = $auth->uploadToken($bucket, null, 3600);//获取上传所需的token
        $policy = [];
        $policy["saveKey"] = "$(etag)$(ext)";
        $upToken = $auth->uploadToken($bucket, null, 3600, $policy);//获取上传所需的token
        $data['Domain'] = $this->config['Domain'];
        $data['Token']  = $upToken;
//        dd($data);
        return self::returnMsg($data);
    }


    public function upload()
    {
        if(empty($this->config['Bucket']) || empty($this->config['AccessKey']) || empty($this->config['SecretKey']) || empty($this->config['UpDomain'])){
            return ['status'=>false,'data'=>'七牛的配置项不完整，请完善七牛的配置'];
        }
        $bucket = $this->config['Bucket'];
        $accessKey = $this->config['AccessKey'];
        $secretKey = $this->config['SecretKey'];
        $UpDomain = str_replace("https:", "", $this->config['UpDomain']);
        $UpDomain = str_replace("http:", "", $UpDomain);
        $auth = new Auth($accessKey, $secretKey);
        $policy = [];
        $policy["saveKey"] = "$(etag)$(ext)";
        $upToken = $auth->uploadToken($bucket, null, 3600, $policy);//获取上传所需的token

        $res = [];
        $res["upload_url"] = $UpDomain;
        $res["up_token"] = $upToken;
        $res["domain"] = $this->config["Domain"];
        return self::returnMsg($res,'操作成功',20000);
    }


    private function request_by_curl($remote_server, $post_string, $upToken)
    {
        $headers   = [];
        $headers[] = 'Content-Type:image/png';
        $headers[] = 'Authorization:UpToken ' . $upToken;
        $ch        = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_server);
        //curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}
