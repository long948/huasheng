<?php


namespace App\Services;

use App\Exceptions\ArException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class IndexService extends Service
{


    /**
     * @method 更新
     * @param string $version 版本号
     */
    public function Update($version){
        if(empty($version)) throw new ArException(ArException::PARAM_ERROR);
        $up = DB::table('UpdateInfo')->first();
        if(empty($up)) throw new ArException(ArException::SELF_ERROR,'No update');
        if(bccomp($version, $up->ver, 5) < 0) return $up;
        throw new ArException(ArException::SELF_ERROR,'No update');
    }

    /**
     * @method banner列表
     */
    public function BannerList(){
        $banner = DB::table('Banner')
            ->where('IsDel',0)
            ->orderBy('Sort','desc')
            ->get();
        $list = [];
        $domain = '';
        $qiniu = DB::table('QiniuConfig')->first();
        if(!empty($qiniu)) $domain = $qiniu->Domain;
        foreach($banner as $item){
            $list[] = [
                'Id' => $item->Id,
                'Img' => $domain.$item->Image,
                'Title' => $item->Title,
                'Url' => $item->Url
            ];
        }
        return $list;
    }

    /**
     * @method 消息详情
     * @param int $id 消息ID
     */
    public function NoticeDetail(int $uid, int $id){
        if($id <= 0) throw new ArException(ArException::PARAM_ERROR);
        $notice = DB::table('Notice')->where('Id', $id)->where('MemberId', $uid)->first();
        if(empty($notice))
            throw new ArException(ArException::SELF_ERROR,'消息不存在或已删除');
        $deital = [
            'Id' => $notice->Id,
            'Title' => $notice->Title,
            'AddTime' => $notice->AddTime,
            'Content' => $notice->Content
            //'IsRead' => $item->IsRead //是否已读，需要再开启
        ];
        if($notice->IsRead == 0) $this->readNotice($uid, $id);
        return $deital;
    }

    /**
     * @method 消息列表
     * @param int $count 分页参数
     */
    public function NoticeList(int $uid, int $count){
        if($count <= 0) throw new ArException(ArException::PARAM_ERROR);
        $notices = DB::table('Notice')
            ->where('MemberId', $uid)
            ->where('IsDel', 0)
            ->orderBy('Id','desc')
            ->paginate($count);
        $list = [];
        foreach($notices as $item){
            $list[] = [
                'Id' => $item->Id,
                'Title' => $item->Title,
                'AddTime' => $item->AddTime,
                'IsRead' => $item->IsRead //是否已读，需要再开启
            ];
        }
        return $list;
    }

    public function readNotice(int $uid, int $id)
    {
        if($id <= 0) throw new ArException(ArException::PARAM_ERROR);
        DB::table('Notice')->where('Id', $id)->where('MemberId', $uid)->update([
            'IsRead' => 1
        ]);
        return true;
    }

    public function realAll(int $uid)
    {
        $result = DB::table('Notice')->where('MemberId', $uid)->where('IsRead',0)->update([
            'IsRead' => 1
        ]);
        if(!$result) throw new ArException(ArException::SELF_ERROR, '暂无需要阅读项');
        return true;
    }

    /**
     * 校验图形验证码
     */
    public function checkCaptcha(int $captcha, string $ip)
    {
        if(empty($captcha)) throw new ArException(ArException::SELF_ERROR,'请输入图形验证码');
        $phrase = Redis::get('captcha.'.$ip);
        if(!$phrase) throw new ArException(ArException::SELF_ERROR, '请重新获取图形验证码');

        if($phrase != $captcha) throw new ArException(ArException::SELF_ERROR,'图形验证码错误');

        Redis::del('captcha.'.$ip);
        return true;
    }
}
