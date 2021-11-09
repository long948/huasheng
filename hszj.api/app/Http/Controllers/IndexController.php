<?php

namespace App\Http\Controllers;

use App\Exceptions\ArException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SettingModel;
use App\Services\IndexService;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="花生世界 APi",
 * ),
 * @OA\Server(
 *      description="本地",
 *     url="http://hssj.com"
 * ),
 * @OA\Server(
 *      url="http://ehb.api.php.8kpay.com:10001",
 *      description="测试"
 * )
 */
class IndexController extends Controller
{

    /**
     * @OA\Get(
     *     path="/notice-list",
     *     operationId="/notice-list",
     *     tags={"Common"},
     *     summary="消息列表",
     *     description="消息列表",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Parameter(ref="#/components/parameters/count"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function NoticeList(Request $request, IndexService $service){
        $uid = intval($request->get('uid'));
        $count = intval($request->input('count'));
        $list = $service->NoticeList($uid, $count);
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/notice-detail",
     *     operationId="/notice-detail",
     *     tags={"Common"},
     *     summary="消息详情",
     *     description="消息详情",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/NoticeId"),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function NoticeDetail(Request $request, IndexService $service){
        $uid = intval($request->get('uid'));
        $id = intval($request->input('Id'));
        $list = $service->NoticeDetail($uid, $id);
        return self::success($list);
    }

    /**
     * @OA\Post(
     *     path="/post.read.all",
     *     operationId="readAll",
     *     tags={"Common"},
     *     summary="阅读全部",
     *     description="阅读全部",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function readAll(Request $request, IndexService $service){
        $uid = intval($request->get('uid'));
        $service->realAll($uid);
        return self::success();
    }

    /**
     * @OA\Get(
     *     path="/banner-list",
     *     operationId="/banner-list",
     *     tags={"Common"},
     *     summary="Banner列表",
     *     description="Banner列表",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function BannerList(Request $request, IndexService $service){
        $list = $service->BannerList();
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/qiniu-upload",
     *     operationId="/qiniu-upload",
     *     tags={"Common"},
     *     summary="七牛上传Token",
     *     description="七牛上传Token",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function QiniuUpload(Request $request, IndexService $service){
        $token = $service->QiniuUpload();
        return self::success($token);
    }

    /**
     * @OA\Get(
     *     path="/plat-setting",
     *     operationId="/plat-setting",
     *     tags={"Common"},
     *     summary="平台设置",
     *     description="平台设置",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Header(
     *         header="api_key",
     *         description="Api key header",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     security={
     *          {"Authorization":{}}
     *     }
     * )
     */
    public function Setting(Request $request, IndexService $service){
        $res = DB::table('PlatformSetting')->first();
        return self::success($res);
    }

    /**
     * @OA\Get(
     *     path="/common-question",
     *     operationId="/common-question",
     *     tags={"Common"},
     *     summary="常见问题",
     *     description="常见问题",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     )
     * )
     */
    public function Question(Request $request, IndexService $service){
        $list = DB::table('CommonQA')->get();
        return self::success($list);
    }

     /**
     * @OA\Get(
     *     path="/member-doc",
     *     operationId="/member-doc",
     *     tags={"Common"},
     *     summary="关于我们&用户协议&支付协议",
     *     description="关于我们&用户协议&支付协议",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     )
     * )
     */
    public function Doc(Request $request, IndexService $service){
        $list = DB::table('MemberDoc')->first();
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/plat-server",
     *     operationId="/plat-server",
     *     tags={"Common"},
     *     summary="客服",
     *     description="客服",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     )
     * )
     */
    public function Server(Request $request, IndexService $service){
        $list = DB::table('PlatServer')->first();
        return self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/qq",
     *     operationId="/qq",
     *     tags={"Common"},
     *     summary="qq",
     *     description="qq",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     )
     * )
     */
    public function qq()
    {
        return self::success(SettingModel::getValueByKey('qq'));
    }

    /**
     * @OA\Get(
     *     path="/captcha",
     *     operationId="captcha",
     *     tags={"Common"},
     *     summary="图形验证码",
     *     description="图形验证码",
     *     @OA\Parameter(ref="#/components/parameters/rand"),
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     )
     * )
     */
    public function captcha(Request $request)
    {
        $rand = trim($request->input('rand'));
        if(empty($rand)) throw new ArException(ArException::SELF_ERROR,'请输入随机码');

        $phrase = rand(1000,9999);
        $builder = new CaptchaBuilder((string)$phrase);
        //可以设置图片宽高及字体
        $builder->build($width = 100, $height = 40, $font = null);
        // $phrase = $builder->getPhrase();

        //把内容存入redis
        // $ip = $request->getClientIp();
        // Redis::set('captcha.'.$ip, $phrase);
        Redis::set('captcha.'.$rand, $phrase);
        Redis::expire('captcha.'.$rand, 300);

        //生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: image/jpeg');
        $builder->output();
        die;

        // return self::success($builder->inline());
    }

    /**
     * @OA\Get(
     *     path="/get.update",
     *     operationId="update",
     *     tags={"Common"},
     *     summary="检查更新",
     *     description="检查更新",
     *     @OA\Parameter(ref="#/components/parameters/version"),
     *     @OA\Parameter(ref="#/components/parameters/device"),
     *     @OA\Parameter(ref="#/components/parameters/version_type"),
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     )
     * )
     */
    public function update(Request $request)
    {
        $version = addslashes($request->input('version', '1.0.0'));
        $device = addslashes($request->input('device', 'android'));
        $version_type = addslashes($request->input('version_type', 'RC'));

        $app = DB::table('device_update')->where([
            'device' => $device,
            'version_type' => $version_type,
        ])->first();
        if (!$app) throw new ArException(ArException::SELF_ERROR,'暂不支持该设备');

        $data = [];
        $data['update'] = false;
        $data['url'] = '';

        switch (version_compare($version, $app->version)) {
            case 1: //大于
            case 0: //等于
                break;
            case -1: //低版本
                $data['update'] = true;
                $data['must'] = $app->must ? true : false;
                $data['url'] = $app->url;
                $data['type'] = $app->type;
                $data['log'] = $app->log;
                break;
        }

        return self::success($data);
    }

    /**
     * @OA\Get(
     *     path="/get.download",
     *     operationId="download",
     *     tags={"Common"},
     *     summary="下载地址",
     *     description="下载地址",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     )
     * )
     */
    public function download()
    {
        return DB::table('Setting')->where('k','android')->orWhere('k','ios')->pluck('v','k');
    }

}
