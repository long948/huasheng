<?php

namespace App\Http\Controllers;

use App\Exceptions\ArException;
use App\Models\ArticleModel;
use App\Models\BannerModel;
use App\Models\NoticeModel;
use App\Models\QiniuConfigModel;
use App\Models\UserFeedbackModel;
use Illuminate\Http\Request;
use App\Models\NoticeModel as Notice;
use App\Models\BannerModel as Banner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BannerNoticeController extends Controller
{

    public function Server(Request $request)
    {
        $data = DB::table('PlatServer')->first();
        return self::returnMsg($data);
    }

    public function ServerUpdate(Request $request)
    {
        $rules = [
            'QQ' => 'required',
            'Wechat' => 'required',
        ];
        $v = Validator::make($request->all(), $rules, [
            'QQ.required' => '请填写QQ',
            'Wechat.required' => '请填写微信'
        ]);
        if ($v->fails()) return self::returnError(20001, $v->errors()->first());
        $sqlmap = $v->validated();
        DB::table('PlatServer')->update($sqlmap);
        return self::returnMsg();
    }

    public function PayDocEdit(Request $request)
    {
        $content = $request->input('content');
        if (empty($content))
            throw new ArException(ArException::SELF_ERROR, '请填写内容');

        $data = DB::table('MemberDoc')->first();
        if (empty($data)) {
            DB::table('MemberDoc')->insert([
                'PayDoc' => $content
            ]);
        } else {
            DB::table('MemberDoc')->update(['PayDoc' => $content]);
        }
        return self::returnMsg([], '', 20000);
    }

    /**
     * 用户协议 修改
     */
    public function MemberDocEdit(Request $request)
    {
        $content = $request->input('content');
        if (empty($content))
            throw new ArException(ArException::SELF_ERROR, '请填写内容');

        $data = DB::table('articlelist')->where('ArticleCallIndex','user_agreement')->first();
        if (empty($data)) {
            DB::table('articlelist')->insert([
                'ArticleDetails' => $content
            ]);
        } else {
            DB::table('articlelist')->where('ArticleCallIndex','user_agreement')->update(['ArticleDetails' => $content]);
        }
        return self::returnMsg([], '', 20000);
    }

    /**
     * 用户协议
     */
    public function MemberDoc(Request $request)
    {
        $data = DB::table('articlelist')->where('ArticleCallIndex','user_agreement')->first();
        if (empty($data)) return self::returnMsg(['articlelist' => ''], '', 20000);
        return self::returnMsg(['articlelist' => $data->ArticleDetails], '', 20000);
    }

    /**
     * 关于我们 修改
     */
    public function AboutUsEdit(Request $request)
    {
        $content = $request->input('content');
        if (empty($content))
            throw new ArException(ArException::SELF_ERROR, '请填写内容');

        $data = DB::table('MemberDoc')->first();
        if (empty($data)) {
            DB::table('MemberDoc')->insert([
                'AboutUs' => $content
            ]);
        } else {
            DB::table('MemberDoc')->update(['AboutUs' => $content]);
        }
        return self::returnMsg([], '', 20000);
    }

    /**
     * 关于我们
     */
    public function AboutUs(Request $request)
    {
        $data = DB::table('MemberDoc')->first();
        if (empty($data)) return self::returnMsg(['AboutUs' => ''], '', 20000);
        return self::returnMsg(['AboutUs' => $data->AboutUs], '', 20000);
    }

    /**
     * @func获取Notice列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function noticeList(Request $request)
    {
        $count = intval($request->input('limit')) > 0 ? intval($request->input('limit')) : 20;
        $where = function ($query) use ($request) {
            //筛选查询关键字
            if ($request->has('keywords') and $request->keywords != '') {
                $keywords = "%" . $request->keywords . "%";
                $query->where('Title', 'like', $keywords);
            }
        };

        $Noticelist = Notice::GetPageList($count, $where);
        foreach ($Noticelist as $key => &$value) {
            $value['AddTimeName'] = date('Y-m-d H:i:s', $value['AddTime']);
        }
        return self::returnMsg($Noticelist, '', 20000);
    }

    public function NewList(Request $request)
    {
        $count = intval($request->input('limit')) > 0 ? intval($request->input('limit')) : 20;
        $data = DB::table('News')->paginate($count);
        $list = [];
        foreach ($data as $item) {
            $list[] = $item;
        }
        $res = ['list' => $list, 'total' => $data->total()];
        return self::returnMsg($res, '', 20000);
    }

    public function QaList(Request $request)
    {
        $count = intval($request->input('limit')) > 0 ? intval($request->input('limit')) : 20;
        $data = DB::table('CommonQA')->paginate($count);
        $list = [];
        foreach ($data as $item) {
            $list[] = $item;
        }
        $res = ['list' => $list, 'total' => $data->total()];
        return self::returnMsg($res, '', 20000);
    }

    public function QaAdd(Request $request)
    {
        $rules = [
            'Question' => 'required',
            'Answer' => 'required',
        ];
        $v = Validator::make($request->all(), $rules, [
            'Question.required' => '请填写问题',
            'Answer.required' => '请填写回答'
        ]);
        if ($v->fails()) return self::returnError(20001, $v->errors()->first());
        $sqlmap = $v->validated();

        try {
            DB::table('CommonQA')->insert([
                'Question' => $sqlmap['Question'],
                'Answer' => $sqlmap['Answer']
            ]);
            return self::successMsg();
        } catch (\Exception $exception) {
            return self::errorMsg($exception->getMessage());
        }
    }

    /**
     * @func更新Notice
     * @param Request $request
     */
    public function noticeAdd(Request $request)
    {
        $rules = [
            'title' => 'required',
            'content' => 'required',
        ];
        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) return self::returnError(20001, $v->errors()->first());
        $sqlmap = $v->validated();

        $notice = new NoticeModel();
        $notice->Title = $sqlmap['title'];
        $notice->Content = $sqlmap['content'];
        $notice->AddTime = time();
        $notice->IsRead = 0;

        try {
            $notice->save();
            return self::successMsg();
        } catch (\Exception $exception) {
            return self::errorMsg($exception->getMessage());
        }

    }

    //添加快讯
    public function NewsAdd(Request $request)
    {
        $rules = [
            'title' => 'required',
            'content' => 'required',
        ];
        $v = Validator::make($request->all(), $rules, [
            'title.required' => '请填写标题',
            'content.required' => '请填写内容'
        ]);
        if ($v->fails()) return self::returnError(20001, $v->errors()->first());
        $sqlmap = $v->validated();

        try {
            DB::table('News')->insert([
                'Title' => $sqlmap['title'],
                'Content' => $sqlmap['content'],
                'AddTime' => time()
            ]);
            return self::successMsg();
        } catch (\Exception $exception) {
            return self::errorMsg($exception->getMessage());
        }

    }

    public function QaDelete(Request $request)
    {
        $id = (int)$request->input('id');
        try {
            DB::table('CommonQA')->where('Id', $id)->delete();
            return self::successMsg();
        } catch (\Exception $exception) {
            return self::errorMsg($exception->getMessage());
        }
    }

    /**
     * @func更新Notice
     * @param Request $request
     */
    public function noticeUpdate(Request $request)
    {
        $rules = [
            'id' => 'required|integer',
            'title' => 'required',
            'content' => 'required',
        ];
        $v = Validator::make($request->all(), $rules);
        if ($v->fails())
            return self::errorMsg($v->errors());
        $sqlmap = $v->validated();

        $notice = NoticeModel::get_by_id($sqlmap['id']);
        if (!isset($notice))
            return self::errorMsg('该文章不存在');
        $notice->Title = $sqlmap['title'];
        $notice->Content = $sqlmap['content'];
        $notice->AddTime = time();
        $notice->IsRead = 0;

        try {
            $notice->save();
            return self::successMsg();
        } catch (\Exception $exception) {
            return self::errorMsg($exception->getMessage());
        }
    }

    //更新快讯
    public function NewsUpdate(Request $request)
    {
        $rules = [
            'id' => 'required|integer',
            'title' => 'required',
            'content' => 'required',
        ];
        $v = Validator::make($request->all(), $rules, [
            'id.required' => '不存在此快讯',
            'title.required' => '请填写标题',
            'content.required' => '请填写内容'
        ]);
        if ($v->fails())
            return self::errorMsg($v->errors()->first());
        $sqlmap = $v->validated();

        try {
            DB::table('News')->where('Id', $sqlmap['id'])->update([
                'Title' => $sqlmap['title'],
                'Content' => $sqlmap['content']
            ]);
            return self::successMsg();
        } catch (\Exception $exception) {
            return self::errorMsg($exception->getMessage());
        }
    }

    public function QaUpdate(Request $request)
    {
        $rules = [
            'id' => 'required|integer',
            'Question' => 'required',
            'Answer' => 'required',
        ];
        $v = Validator::make($request->all(), $rules, [
            'id.required' => '不存在此快讯',
            'Question.required' => '请填写问题',
            'Answer.required' => '请填写回答'
        ]);
        if ($v->fails())
            return self::errorMsg($v->errors()->first());
        $sqlmap = $v->validated();

        try {
            DB::table('CommonQA')->where('Id', $sqlmap['id'])->update([
                'Question' => $sqlmap['Question'],
                'Answer' => $sqlmap['Answer']
            ]);
            return self::successMsg();
        } catch (\Exception $exception) {
            return self::errorMsg($exception->getMessage());
        }
    }


    /** @func删除Notice
     * @param Request $request
     */
    public function noticeDelete(Request $request)
    {
        $id = (int)$request->input('id');
        $data = Notice::get_by_id($id);
        if (!isset($data))
            return self::errorMsg('该文章不存在');

        $data->IsDel = 1;
        try {
            $data->save();
            return self::successMsg();
        } catch (\Exception $exception) {
            return self::errorMsg($exception->getMessage());
        }
    }

    //删除快讯
    public function NewsDelete(Request $request)
    {
        $id = (int)$request->input('id');

        try {
            DB::table('News')->where('Id', $id)->delete();
            return self::successMsg();
        } catch (\Exception $exception) {
            return self::errorMsg($exception->getMessage());
        }
    }

    /**
     * @func根据id查找数据
     */
    public function getNotice(Request $request)
    {
        $id = (int)$request->input('id');
        $data = NoticeModel::get_by_id($id);
        return self::returnMsg($data);
    }

    public function GetQA(Request $request)
    {
        $id = (int)$request->input('id');
        $data = DB::table('CommonQA')->where('Id', $id)->first();
        return self::returnMsg($data);
    }

    //获取快讯
    public function GetNews(Request $request)
    {
        $id = (int)$request->input('id');
        $data = DB::table('News')->where('Id', $id)->first();
        return self::returnMsg($data);
    }


    /**
     * @func获取banner列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bannerList(Request $request)
    {
        $count = intval($request->input('limit')) > 0 ? intval($request->input('limit')) : 10;
        $type = DB::table('ArticleType')
            ->where('CallIndex', 'home_banner')
            ->orWhere('CallIndex', 'store_banner')
            ->get();
        $bannerlist = DB::table('ArticleList')
            ->where('IsDel', '<>', 1)
            ->where('ArticleCallIndex', 'home_banner')
            ->orWhere('ArticleCallIndex', 'store_banner')
            ->paginate($count);
        foreach ($bannerlist as &$v) {
            $ArticleTypeId = DB::table('ArticleType')->where('Id', $v->ArticleTypeId)->get();
            foreach ($ArticleTypeId as &$k) {
                $v->name = $k->TypeTitle;
            }
        }

        $res = [];
        $res["total"] = $bannerlist->total();
        $res["list"] = $bannerlist->items();
        $res["type"] = $type;
        return self::returnMsg($res);
    }

    public function WechatGroup()
    {
        $list = DB::table('WechatGroup')->get();
        return self::returnMsg($list);
    }


    /**
     * @func更新banner
     * @param Request $request
     */
    public function bannerUpdate(Request $request)
    {
        $data = $request->post();
        $Id = $data['Id'];
        if (empty($Id)) return self::returnMsg([], '参数错误', '20001');
        $res = DB::table('ArticleType')->where('TypeTitle', $data['name'])->first();
        $data = [

            'ArticleCallIndex' => $res->CallIndex,
            'ArticleTypeId' => $res->Id,
            'MainImg' => $data['MainImg'],
            'TypeTitle' => $res->TypeTitle,
            'ArticleTitle' => $res->TypeTitle,
            'ArticleDetails' => $res->TypeTitle,
        ];
        DB::table('ArticleList')->where('Id', $Id)->update($data);
        return self::returnMsg();
    }

    //添加banner
    public function bannerAdd(Request $request)
    {
        $data = $request->post();
        if (empty($data['name'])) return self::returnMsg([], '请选择类型', '20001');
        if (empty($data['MainImg'])) return self::returnMsg([], '请上传图片', '20001');
        $res = DB::table('ArticleType')->where('TypeTitle', $data['name'])->first();
        $res = [
            'ArticleCallIndex' => $res->CallIndex,
            'ArticleTypeId' => $res->Id,
            'MainImg' => $data['MainImg'],
            'TypeTitle' => $res->TypeTitle,
            'ArticleTitle' => $res->TypeTitle,
            'ArticleDetails' => $res->TypeTitle,
        ];
        DB::table('ArticleList')->insert($res);
        return self::returnMsg();
    }

    /** @func删除banner
     * @param Request $request
     */
    public function bannerDelete(Request $request)
    {
        $id = (int)$request->input('Id');
        $banner_arr = ArticleModel::GetBId($id);
        if (!isset($banner_arr))
            return self::errorMsg('数据不存在,或者已经被删除');

        $banner_arr->IsDel = 1;
        try {
            $res = $banner_arr->save();
            if ($res) {
                //删除七牛云的物理图片
            }
            return self::successMsg();
        } catch (\Exception $exception) {
            return self::errorMsg($exception->getMessage());
        }
    }

    public function AddWechat(Request $request)
    {
        $rules = [
            'Name' => 'required|string',
            'QrCode' => 'required|string',
            'IsOpen' => 'required|integer'
        ];
        $v = Validator::make($request->all(), $rules, [
            'Name.required' => '请填写群名',
            'QrCode.required' => '请上传二维码',
            'IsOpen.required' => '请选择是否开启',
        ]);
        if ($v->fails())
            return self::errorMsg($v->errors()->first());
        $sqlmap = $v->validated();
        DB::table('WechatGroup')->insert($sqlmap);
        return self::returnMsg();
    }

    public function EditWechat(Request $request)
    {
        $rules = [
            'Name' => 'required|string',
            'QrCode' => 'required|string',
            'IsOpen' => 'required|integer'
        ];
        $v = Validator::make($request->all(), $rules, [
            'Name.required' => '请填写群名',
            'QrCode.required' => '请上传二维码',
            'IsOpen.required' => '请选择是否开启',
        ]);
        if ($v->fails())
            return self::errorMsg($v->errors()->first());
        $id = intval($request->input('Id'));
        if (empty($id)) throw new ArException(ArException::SELF_ERROR, '参数错误');

        $sqlmap = $v->validated();
        DB::table('WechatGroup')->where('Id', $id)->update($sqlmap);
        return self::returnMsg();
    }

    //公告
    public function Notice(Request $request)
    {

        $count = intval($request->input('limit')) > 0 ? intval($request->input('limit')) : 20;
        $where = function ($query) use ($request) {
            //筛选查询关键字
            if ($request->has('keywords') and $request->keywords != '') {
                $keywords = "%" . $request->keywords . "%";
                $query->where('TypeTitle', 'like', $keywords);
            }
        };
        $Notice = DB::table('ArticleList')->where('IsDel', '<>', 1)->where('ArticleCallIndex', 'notice')->where($where)->paginate($count);
        foreach ($Notice as $key => &$value) {
            $value->AddTimeName = date('Y-m-d H:i:s', $value->AddTime);
        }
        return self::returnMsg($Notice, '', 20000);
    }

    /**
     * @func根据id查找数据
     */
    public function getNotices(Request $request)
    {
        $id = (int)$request->input('id');
        if (empty($id)) {
            return self::returnMsg('参数错误', 20001);
        }
        $data = ArticleModel::GetBId($id);
        return self::returnMsg($data);
    }

    //修改公告
    public function NoticeEdit(Request $request)
    {
        $id = $request->input('id');
        $content = $request->input('content');
        $title = $request->input('title');
        if (empty($content))
            throw new ArException(ArException::SELF_ERROR, '请填写内容');
        if (empty($title))
            throw new ArException(ArException::SELF_ERROR, '请填写标题');
        $res = [
            'TypeTitle' => $title,
            'ArticleDetails' => $content
        ];
        DB::table('ArticleList')->where('Id', $id)->update($res);

        return self::returnMsg([], '', 20000);
    }

    //添加公告
    public function NoticesAdd(Request $request)
    {
        $data = $request->post();
        $title = $data['title'];
        $content = $data['content'];
//        return $result;
        if (empty($title)) return self::returnMsg('请输入公告标题', 20001);
        if (empty($content)) return self::returnMsg('请输入公告内容', 20001);
        $res = [
            'TypeTitle' => $title,
            'ArticleDetails' => $content,
            'ArticleCallIndex' => 'notice',
            'ArticleTypeId' => 13,
            'ArticleTitle' => $title,
            'AddTime' => time(),
        ];
        DB::table('ArticleList')->insert($res);

        return self::returnMsg([], '公告发布成功', 20000);
    }

    //删除公告
    public function NoticesDel(Request $request)
    {

        $id = (int)$request->input('id');
        if (empty($id)) return self::returnMsg([], '参数错误', 20001);
        $res = [
            'IsDel' => 1
        ];
        DB::table('ArticleList')->where('Id', $id)->update($res);
        return self::returnMsg([], '删除成功', 20000);
    }

    /**
     * @func根据id查找数据
     */
    public function getBanner(Request $request)
    {
        $id = (int)$request->input('id');
        if (empty($id))
            return self::returnMsg([], 'id不能为空', 20003);

        $banner_arr = Banner::get_by_id($id);
        $banner_arr['Image'] = $this->config['Domain'] . '/' . $banner_arr['Image'];
        return self::returnMsg($banner_arr);
    }


    /**
     * @func获取交易指导列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function schoolList(Request $request)
    {
        $count = intval($request->input('limit')) > 0 ? intval($request->input('limit')) : 10;
        $bannerlist = DB::table('ArticleList')
            ->where('IsDel', '<>', 1)
            ->where('ArticleCallIndex', 'school')
            ->paginate($count);
        $res = [];
        $res["total"] = $bannerlist->total();
        $res["list"] = $bannerlist->items();
        return self::returnMsg($res);
    }

    /**
     * @func更新交易指导
     * @param Request $request
     */
    public function schoolUpdate(Request $request)
    {
        $data = $request->post();
//        dd($data);
        $Id = $data['Id'];
        if (empty($Id)) return self::returnMsg([], '参数错误', '20001');
        $res = DB::table('ArticleType')->where('Id', $data['ArticleTypeId'])->first();
        $data = [
            'ArticleCallIndex' => $res->CallIndex,
            'ArticleTypeId' => $res->Id,
//            'MainImg' => $data['MainImg'],
            'TypeTitle' => $data['TypeTitle'],
            'ArticleTitle' => $res->TypeTitle,
            'ArticleDetails' => $data['ArticleDetails'],
        ];
        DB::table('ArticleList')->where('Id', $Id)->update($data);
        return self::returnMsg();
    }

    //添加交易指导
    public function schoolAdd(Request $request)
    {
        $data = $request->post();
        if (empty($data['TypeTitle'])) return self::returnMsg([], '请输入标题', '20001');
        if (empty($data['ArticleDetails'])) return self::returnMsg([], '请输入内容', '20001');
        $res = DB::table('ArticleType')->where('CallIndex', 'school')->first();
        $data = [
            'ArticleCallIndex' => 'school',
            'ArticleTypeId' => $res->Id,
//            'MainImg' => $data['MainImg'],
            'TypeTitle' => $data['TypeTitle'],
            'ArticleTitle' =>  $data['TypeTitle'],
            'ArticleDetails' => $data['ArticleDetails'],
        ];
        DB::table('ArticleList')->insert($data);
        return self::returnMsg();
    }

    /** @func删除交易指导
     * @param Request $request
     */
    public function schoolDelete(Request $request)
    {
        $id = (int)$request->input('Id');
        $banner_arr = ArticleModel::GetBId($id);
        if (!isset($banner_arr))
            return self::errorMsg('数据不存在,或者已经被删除');

        $banner_arr->IsDel = 1;
        try {
            $res = $banner_arr->save();
            if ($res) {
                //删除七牛云的物理图片
            }
            return self::successMsg();
        } catch (\Exception $exception) {
            return self::errorMsg($exception->getMessage());
        }
    }
    //用户反馈
    public function userFeedback(Request $request){
        $where = [];
        $Phone = $request->input('Phone');
        if (!empty($Phone)) {
                $user_id=DB::table('members')->where('Phone',$Phone)->value('id');
            $where[] = ["user_id", "$user_id"];
        }
        $NickName = $request->input('NickName');
        if (!empty($NickName)) {
            $user_id=DB::table('members')->where('NickName',$NickName)->value('id');
            $where[] = ["user_id", "$user_id"];
        }
        $title = $request->input('title');
        if (!empty($title)) {
            $where[] = ["title", "like", "%$title%"];
        }
        $is_hand = $request->input('is_hand');
        if ($is_hand!=='') {
            $where[] = ["is_hand",  "$is_hand"];
        }
        $count = intval($request->input('limit', 10));
        $lists = UserFeedbackModel::GetList($count, $where);
        foreach ($lists as $v){
            $v->create_time = date('Y-m-d H:i:s', $v->create_time);
            $v->update_time = date('Y-m-d H:i:s', $v->update_time);

        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    public function userFeedbackAnswer(Request $request){
        $data=$request->post();
        if(empty($data['Id'])) return self::returnMsg([],'参数错误',20001);
        if(empty($data['reply'])) return self::returnMsg([],'请输入回复内容',20001);
        $datas=[
          'reply' => $data['reply'],
          'is_hand' => 1,
          'update_time' => time()
        ];
        DB::table('other_work_order')->where('id',$data['Id'])->update($datas);
        return self::returnMsg();
    }
    //拼购规则
    public function pgRule(){
     $list = DB::table('setting')->where('k','shop_rule')->value('v');
     return $list;
    }
    public function pgRuleEdit(Request $request){
        $data = $request->post();
        if (empty($data['content'])) return self::returnMsg([],'请输入拼购规则内容',20001);
        DB::table('setting')->where('k','shop_rule')->update(['v'=>$data['content']]);
        return self::returnMsg();
    }
}
