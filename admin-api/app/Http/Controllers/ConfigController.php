<?php

namespace App\Http\Controllers;

use App\Models\CoinModel;
use App\Models\ConfigModel;
use App\Models\SMSConfigModel;
use App\Models\UpdateInfoModel;
use Illuminate\Http\Request;
use App\Models\QiniuConfigModel as Qiniu;
use App\Models\SMSConfigModel as SMS;
use App\Models\UpdateInfoModel as APPVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class ConfigController extends Controller
{


    /**
     * 获取七牛的配置
     */
    public function list()
    {
        $qiniuconfig = Qiniu::first();
        return self::returnMsg($qiniuconfig);
    }

    /**
     * 添加/添加七牛的配置
     */
    public function updateAddQiniu(Request $request)
    {
        $rules = [
            'domain'    => 'required',
            'bucket'    => 'required',
            'accesskey' => 'required',
            'secretkey' => 'required',
        ];
        $v     = Validator::make($request->all(), $rules);
        if ($v->fails())
            return self::errorMsg($v->errors());
        $sqlmap = $v->validated();

        $qiniu            = Qiniu::first();
        $qiniu->Domain    = $sqlmap['domain'];
        $qiniu->Bucket    = $sqlmap['bucket'];
        $qiniu->AccessKey = $sqlmap['accesskey'];
        $qiniu->SecretKey = $sqlmap['secretkey'];

        try {
            $qiniu->save();
            return self::successMsg();
        } catch (\Exception $exception) {
            return self::errorMsg($exception->getMessage());
        }
    }


    //短信配置
    public function smsList()
    {
        $qiniuconfig = SMS::GetPageList();
        return self::returnMsg($qiniuconfig);
    }


    //更新或者插入短信配置
    public function updateAddSms(Request $request)
    {
        $qiniuconfig = SMS::GetPageList();
        if ($qiniuconfig) {
            $id = (int) trim($request->input('id'));
            if (empty($id)) {
                return self::returnMsg([], 'id错误', 20001);
            } else {
                $smsC = SMS::GetBId($id);
                if (empty($smsC)) {
                    return self::returnMsg([], '系统错误，没有找到记录', 20001);
                }
            }
        }
        $account = trim($request->input('account'));
        $password = trim($request->input('password'));
        $secretKey = trim($request->input('secretKey'));
        $signName = trim($request->input('signName'));
        $mold = trim($request->input('mold'));
        $vaildCodeLength = trim($request->input('vaildCodeLength'));
        if (!empty($vaildCodeLength)) {
            if (!is_numeric($vaildCodeLength)) {
                return self::returnMsg([], '短信位数请填入数字', 200001);
            }
        }
        $timeOut = trim($request->input('timeOut'));
        if (!empty($timeOut)) {
            if (!is_numeric($timeOut)) {
                return self::returnMsg([], '短信有效期请填入数字', 200001);
            }
        }
        $errorCount = trim($request->input('errorCount'));
        if (!empty($errorCount)) {
            if (!is_numeric($errorCount)) {
                return self::returnMsg([], '最多错误次数请填入数字', 200001);
            }
        }
        $sqldata['Account'] = $account;
        $sqldata['Password'] = $password;
        $sqldata['SecretKey'] = $secretKey;
        $sqldata['SignName'] = $signName;
        $sqldata['Mold'] = $mold;
        $sqldata['VaildCodeLength'] = $vaildCodeLength;
        $sqldata['TimeOut'] = $timeOut;
        if ($qiniuconfig) {
            $result = SMS::where('id', '=', $id)->update($sqldata);
        } else {
            $result = SMS::insert($sqldata);
        }
        if ($result) {
            return self::returnMsg([], '操作成功', 20000);
        } else {
            return self::returnMsg([], '操作失败', 20011);
        }
    }



    /**
     * 短信常量配置
     * @return \Illuminate\Http\JsonResponse
     */
    public function smstype()
    {
        $sms_array = [
            [
                'id'    => 1,
                'title' => '阿里云短信',
            ],
            [
                'id'    => 2,
                'title' => '互易短信',
            ],
            [
                'id'    => 3,
                'title' => '聚合短信',
            ],
            [
                'id'    => 4,
                'title' => '253短信',
            ],
            [
                'id'    => 5,
                'title' => '创瑞短信',
            ],
        ];
        return self::returnMsg($sms_array, '操作成功', 20000);
    }


    /**
     * app配置列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function appList(Request $request)
    {
//        $data = APPVersion::GetPageList();
//        $data->MustUpdate = intval($data->MustUpdate);
//        $data->NeedInstall = intval($data->NeedInstall);
//        return self::returnMsg($data);
        $where = [];
        $count = intval($request->input('limit', 20));
        $lists = DB::table("device_update")
            ->where($where)
            ->orderBy("id", "DESC")
            ->paginate($count);
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }


    //更新或者插入版本配置
    public function updateAddAppVersion(Request $request)
    {
        $data=$request->post();
        $Id=intval($data['id']);
       if (empty($Id)) return self::returnMsg([],'请选择需要操作的记录','20001');
        if (empty($data['url'])) return self::returnMsg([],'请上传更新包或安装包','20001');
        if (empty($data['version'])) return self::returnMsg([],'请输入版本号','20001');
        if (empty($data['version_type'])) return self::returnMsg([],'请输入版本类型','20001');
        if (empty($data['type'])) return self::returnMsg([],'请输入文件类型（后缀名）','20001');
        if (!is_numeric($data['must'])) return self::returnMsg([],'请选择是否强制更新','20001');
        $domain = strstr($data['url'], 'http');
        if (!$domain) return self::returnMsg([],'下载地址输入有误','20001');
       $data=[
           'device'=>$data['device'],
           'version'=>$data['version'],
           'version_type'=>$data['version_type'],
           'url'=>$data['url'],
           'must'=>$data['must'],
           'type'=>$data['type'],
           'updated_at'=>time()
       ];
       DB::table('device_update')->where('id',$Id)->update($data);
       return self::returnMsg();

    }
  //系统维护
    public function SystemList(){
        $data= DB::table('Setting')->where('k','system_is_close')->first();
        return self::returnMsg($data);
    }
    public function SystemClose(Request $request){
     $data=$request->post();
        $v=$data['v'];
        $system_close_explanation=$data['system_close_explanation'];
        if (empty($system_close_explanation)){
            return self::returnMsg([],'请输入维护说明！',20001);
        }
       $res=[
           'v'=>$v,
           'remark'=>$system_close_explanation
       ];
       $data=[
           'v'=>$system_close_explanation,
       ];
       DB::table('Setting')->where('k','system_is_close')->update($res);
      if ($v==1){
       DB::table('Setting')->where('k','system_close_explanation')->update($data);
      }
        return self::returnMsg([],'操作成功',20000);
    }
    /**
     * 分享配置
     * @return \Illuminate\Http\JsonResponse
     */
    public function share()
    {
        $date = DB::table("Setting")->where('k','posters_background')->pluck('v', 'k')->all();
        list($background_a, $background_b) = json_decode($date['posters_background'], true);
        $info = [
            'posters_background_a' => $background_a['image'],
            'posters_background_b' => $background_b['image'],
        ];
        return self::returnMsg($info, '获取成功', 20000);
    }
    /**
     * 更新分享配置
     */
    public function updateShare(Request $request)
    {
        $date=$request->post();

        $share_posters_background_a = trim($request->input('posters_background_a'));
//        $test=$this->config["Domain"] . $share_posters_background_a;
        $share_posters_background_b = trim($request->input('posters_background_b'));

        $date = DB::table("Setting")->where('k','posters_background')->pluck('v', 'k')->all();
        foreach ($date as $k => $v) {
            switch ($k) {
                case 'posters_background':
                    list($background_a, $background_b) = json_decode($date['posters_background'], true);

                    if ($background_a != $share_posters_background_a || $background_b != $share_posters_background_b) {
                        $result = DB::table("Setting")->where('k', 'posters_background')->update([
                            'v' => json_encode([
                                [
                                    'module' => 1,
                                    'image' => $share_posters_background_a,
                                ],
                                [
                                    'module' => 2,
                                    'image' => $share_posters_background_b,
                                ]
                            ])
                        ]);
                    }
                    break;
            }
        }
        return self::returnMsg([], '操作成功', 20000);
    }
    //分享赠送配置
    public function GiveSettingList(){
        $data= DB::table('Setting')->where('k','give_setting')->first();
        return self::returnMsg($data);
    }
    public function GiveSetting(Request $request){
        $data=$request->post();
        $v=$data['v'];
        $res=[
            'v'=>$v,
        ];
            DB::table('Setting')->where('k','give_setting')->update($res);
        return self::returnMsg([],'操作成功',20000);
    }
    //清除缓存
    public function ClearCache(){
        self::redisFlushAll();
        return self::successMsg();
    }
    //下载链接
    public function downloadLink(){
        $list = DB::table('setting')->whereIn('k',['ios_url','android_url'])->get();
        return $list;
    }
    public function downloadLinkEdit(Request $request){
        $data = $request->post();
        DB::table('setting')->where('k',$data['k'])->update(['v'=>$data['v']]);
        return self::returnMsg([],'操作成功',20000);
    }
}
