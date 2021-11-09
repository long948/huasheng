<?php

namespace App\Http\Controllers;

use App\Common\DTO\UserDeductionDTO;
use App\Exceptions\ArException;
use App\Http\Util\Snowflake;
use App\Libraries\base;
use App\Models\CoinModel;
use App\Models\FinancingListModel;
use App\Models\MembersModel as Members;

use App\Models\MembersModel;
use App\Models\MinerUserLevelModel;
use App\Models\SettingModel;
use App\Models\UserSaplingPackageModel;
use App\Services\UserGiveAwayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\MembersCoinModel as MemberCoin;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\DocBlock;

class MembersController extends Controller
{

//    //添加僵尸号
//    public function MemberFakeAdd(Request $request)
//    {
//        $rules = [
//            'Phone' => 'required|integer',
//            'Logo' => 'required|string',
//            'Name' => 'required|string',
//        ];
//        $v = Validator::make($request->all(), $rules, [
//            'Phone.required' => '请输入手机号',
//            'Logo.required' => '请上传Logo',
//            'Name.required' => '请输入姓名',
//        ]);
//        if ($v->fails())
//            return self::errorMsg($v->errors()->first());
//        $data = $v->validated();
//        $has = DB::table('FakeMember')->where('Phone', $data['Phone'])->first();
//        if (!empty($has)) throw new ArException(ArException::SELF_ERROR, '此手机号已存在');
//        DB::table('FakeMember')->insert($data);
//        return self::returnMsg();
//    }
//
//    //僵尸号
//    public function MemberFake(Request $request)
//    {
//        $limit = intval($request->input('limit'));
//        if ($limit <= 0) throw new ArException(ArException::PARAM_ERROR);
//        $data = DB::table('FakeMember')->paginate($limit);
//        $list = [];
//        foreach ($data as $item) {
//            $list[] = $item;
//        }
//        $res = ['list' => $list, 'total' => $data->total()];
//        return self::returnMsg($res);
//    }
     //资金记录
     public function MemberBills(Request $request){

//         $buy_amount = DB::table('CTCOrder')->where('Type', 2)->sum('Number');
//         $tx_amount = DB::table('CTCTrade')->where('State', 2)->sum('Number');
         $mid = intval($request->input('Id'));
         $limit = intval($request->input('limit'));
         if ($mid <= 0) throw new ArException(ArException::PARAM_ERROR);
         if ($limit <= 0) throw new ArException(ArException::PARAM_ERROR);
         $res=$mid%20;
         if ($res<10){
         $table='FinancingList_0'.$res;
//         dd($table);
             $lists= DB::table($table)->where('MemberId',$mid)->paginate($limit);
         }else{
         $table1='FinancingList_'.$res;
             $lists= DB::table($table1)->where('MemberId',$mid)->paginate($limit);
         }
         return self::returnList($lists->items(), $lists->total());


    }
    //收益明细
    public function MemberBill(Request $request)
    {
        $mid = intval($request->input('Id'));
        $limit = intval($request->input('limit'));
        if ($mid <= 0) throw new ArException(ArException::PARAM_ERROR);
        if ($limit <= 0) throw new ArException(ArException::PARAM_ERROR);
        $data = DB::table('RewardRecord')->where('MemberId', $mid)->paginate($limit);
        $list = [];
        foreach ($data as $item) {
            $list[] = [
                'Id' => $item->Id,
                'Number' => $item->Number,
                'Balance' => $item->Balance,
                'Lock' => $item->Lock,
                'AddTime' => $item->AddTime,
                'Type' => $item->Type
            ];
        }
        //静态奖励
        $static = DB::table('RewardRecord')->where('MemberId', $mid)->where('Type', 1)->sum('Number');
        $staticBalance = DB::table('RewardRecord')->where('MemberId', $mid)->where('Type', 1)->sum('Balance');
        $staticLock = DB::table('RewardRecord')->where('MemberId', $mid)->where('Type', 1)->sum('Lock');
        //邀请
        $invite = DB::table('RewardRecord')->where('MemberId', $mid)->where('Type', 2)->sum('Number');
        $inviteBalance = DB::table('RewardRecord')->where('MemberId', $mid)->where('Type', 2)->sum('Balance');
        $inviteLock = DB::table('RewardRecord')->where('MemberId', $mid)->where('Type', 2)->sum('Lock');
        //团队
        $team = DB::table('RewardRecord')->where('MemberId', $mid)->where('Type', 3)->sum('Number');
        $teamBalance = DB::table('RewardRecord')->where('MemberId', $mid)->where('Type', 3)->sum('Balance');
        $teamLock = DB::table('RewardRecord')->where('MemberId', $mid)->where('Type', 3)->sum('Lock');
        //空投
        $air = DB::table('RewardRecord')->where('MemberId', $mid)->where('Type', 4)->sum('Number');
        $airBalance = DB::table('RewardRecord')->where('MemberId', $mid)->where('Type', 4)->sum('Balance');
        $airLock = DB::table('RewardRecord')->where('MemberId', $mid)->where('Type', 4)->sum('Lock');

        $sum = [
            'static' => $static,
            'team' => $team,
            'invite' => $invite,
            'air' => $air,
            'staticBalance' => $staticBalance,
            'staticLock' => $staticLock,
            'inviteBalance' => $inviteBalance,
            'inviteLock' => $inviteLock,
            'teamBalance' => $teamBalance,
            'teamLock' => $teamLock,
            'airBalance' => $airBalance,
            'airLock' => $airLock
        ];
        $res = [
            'list' => $list,
            'total' => $data->total(),
            'sum' => $sum
        ];
        return self::returnMsg($res);
    }
    //实名认证审核
    public function AuthReject(Request $request, UserGiveAwayService $service)
    {
        $data = $request->post();
        $Id = !empty($data["Id"]) ? intval($data["Id"]) : 0;
        if (empty($Id)) {
            return self::returnMsg([],'请选择需要操作的记录',20001);
        }
//        $res=DB::table('MemberIdCard')->where('Id')
        $info = DB::table("MemberIdCard")
            ->where("Id", $Id)
            ->where("Status",0)
            ->first();

        if (empty($info)) {
            return self::returnMsg([],'请勿重复审核！',20001);
        }

        $SaveStatus = !empty($data["SaveStatus"]) ? intval($data["SaveStatus"]) : 0;
        $Message = !empty($data["Message"]) ? $data["Message"] : "";

        if ($SaveStatus != 1 && $SaveStatus != 2) {
            return self::returnMsg([],'请选择操作状态',20001);
        }

        if ($SaveStatus == 1 && empty($Message)) {
            return self::returnMsg([],'请填写驳回原因',20001);
        }

        DB::beginTransaction();
        try{
            $up_data = [];
            $up_data["Status"] = $SaveStatus;
            $up_data["Message"] = $Message;
            $up_data["UpdateTime"]=date('Y-m-d H:i:s');
            // 修改状态
            DB::table("MemberIdCard")->where("Id", $Id)->update($up_data);
            if ($SaveStatus!==1){
                // 修改用户实名认证信息
                $up_member_data = [
                    "IsAuth"=>1,
                ];
                DB::table("members")->where("Id", $info->MemberId)->where('IsAuth','=','0')->update($up_member_data);
//                $this->giveAwayByAuth($info->MemberId);
//                $this->inviteComputingPower($info->MemberId);
//                  $setting=  DB::table('Setting')->where('k','give_setting')->value('v');
//                  if ($setting==0){
//                      $userId=DB::table('members')->where('Id',$info->MemberId)->value('ParentId');
//                       $service->getUserAmount($userId);
//                       $userGiveAwayService = new UserGiveAwayService();
//                       $userGiveAwayService->getUserAmount($userId);
//                      $userDTO = new UserDeductionDTO();
//                      $userDTO->setUserId($userId);
//                      $userDTO->setChildId(0);
//                      $userDTO->setBusinessId(0);
//                      $userDTO->setMethod(1);
//                      $userDTO->setType(1);
//                      $userDTO->setAmount(0.1);
//                      $userDTO->setRemarks('邀请赠送');
//    //                  $userDTO->setAmount($sapling->price);
//                      $userDTO->setStatus(1);
//                      $userDTO->setCoinId(5);
//                      $userGiveAwayService->changeUserAmount($userDTO);
//             }\
                $this->effectivePeopleCount($info->MemberId);
                $this->total_people_count($info->MemberId);
                $this->total_invite_computing_power($info->MemberId);

            }
            DB::commit();
        }
        catch(\Exception $e){
            echo $e->getMessage();
            DB::rollBack();
        }

        return self::returnMsg(["Id" => $Id]);
    }
    /**
     * 有效直推人数（实名认证后就算）
     * @param $userId
     */
    public function effectivePeopleCount($userId)
    {
        $parents = $this->userParents($userId, 1);
        if ($parents) {
            DB::table('miner_user_team_info')->whereIn('user_id', $parents)->update([
                'effective_people_count' => DB::raw("effective_people_count + 1")
            ]);
        }
    }

    /**
     * 团队人数（实名认证后就算）
     * @param $userId
     */
    public function total_people_count($userId)
    {
        $parents = $this->userParents($userId, 6);
        if ($parents) {
            DB::table('miner_user_team_info')->whereIn('user_id', $parents)->update([
                'total_people_count' => DB::raw("total_people_count + 1")
            ]);
        }
    }
    /**
     * 个人算力 团队总算力+0.1（实名认证后就算）
     * @param $userId
     */
    public function total_invite_computing_power($userId)
    {
        $parents = $this->userParents($userId, 6);
        if ($parents) {
            DB::table('miner_user_team_info')->whereIn('user_id', $parents)->update([
                'total_invite_computing_power' => DB::raw("total_invite_computing_power + 0.1"),
                'total_computing_power' => DB::raw("total_computing_power + 0.1")
            ]);
        }
    }
    //实名认证列表
    public function AuthList(Request $request)
    {
        $where = [];
        $Status = $request->input('Status');
        if (is_numeric($Status)) {
            $where[] = ["Status", "like", "%$Status%"];
        }
        $UserAccount = $request->input('UserAccount');
        $IsEmail = MembersModel::IsEmail($UserAccount);
        //验证用户、密码
        $member_where = [];
        if ($UserAccount) {
            if ($IsEmail) {
                $member_where[] = ["Email", "=", $UserAccount];
            } else {
                $member_where[] = ["Phone", "=", $UserAccount];
            }
        }
        if (!empty($member_where)) {
            $MemberId = DB::table("members")->where($member_where)->value("Id");
            $where[] = ["MemberId", "=", $MemberId];
        }
        $count = intval($request->input('limit', 20));
//
        $lists = DB::table("MemberIdCard")
            ->where($where)
            ->where('Status','!=',1)
            ->orderBy("Id", "DESC")

            ->paginate($count);


        foreach ($lists as $v) {
            $v->FrontImage = $this->config['Domain'] . $v->FrontImage;
            $v->ReverseImage = $this->config['Domain'] . $v->ReverseImage;
            $v->ShouFrontImage = $this->config['Domain'] . $v->ShouFrontImage;
            $v->Video =$this->config['Domain'] . $v->Video;
            $uid = $v->MemberId;
            $name=$v->AuthName;
            $users = DB::table('MemberIdCard')
                ->where('AuthName',$name)
                // notice_sort是为了看分类,可不要
                ->select(DB::raw('count(*) as user_count, AuthName'))
                ->groupBy('AuthName')
                ->get();
//            dd($users);
            foreach ($users as &$k){
//                dd($k->user_count);
                $v->count='第'.$k->user_count.'次认证';
            }
            $user = DB::table("members")->where("Id", $uid)->first();
            if ($user) {
                $v->Phone = $user->Phone;
            }
        }

        return self::returnList($lists->items(), $lists->total());
    }

    /**
     * Notes:修改用户交易备注码
     */
    public function memberRemark(Request $request)
    {
        $Id = $request->input('Id');
        $Remark = $request->input('Remark');

        if (DB::table('members')->where('Id', $Id)->update(['Remark' => $Remark]))
            return self::returnMsg('操作成功', '操作成功');
        return self::returnError(20001, '操作失败');
    }

    /**
     * Notes:为会员增加一个币种
     */
    public function addCoin(Request $request)
    {
        $rules = [
            'MemberId' => 'required|integer|min:1',
            'CoinId' => 'required|integer|min:1',
        ];
        $v = Validator::make($request->all(), $rules);
        if ($v->fails())
            return self::errorMsg($v->errors());
        $sqlmap = $v->validated();

        $coin = CoinModel::get_by_id($sqlmap['CoinId']);
        if (!isset($coin))
            return self::errorMsg('未找到该币种信息');

        $memberCoin = DB::table('membercoin')
            ->where('MemberId', $sqlmap['MemberId'])
            ->where('CoinId', $sqlmap['CoinId'])
            ->first();
        if (!empty($memberCoin))
            return self::errorMsg('该用户已经拥有该币种的钱包信息了');

        try {
            DB::table('membercoin')->insert([
                'CoinId' => $sqlmap['CoinId'],
                'CoinName' => $coin->EnName,
                'MemberId' => $sqlmap['MemberId']
            ]);
            return self::successMsg();
        } catch (\Exception $exception) {
            return self::errorMsg($exception->getMessage());
        }
    }

    /**
     * 设置会员等级
     */
    public function MemberLevel(Request $request)
    {
        $id = intval($request->input('id'));
        $level = intval($request->input('Level'));
        DB::table('members')->where('Id', $id)->update(['SettingLevel' => $level]);
        return self::successMsg();
    }

    /**
     * @func获取用户会员列表
     */
    public function membersList(Request $request)
    {
        $where=[];
        $count = intval($request->input('limit', 20));
        $NickName = $request->input('NickName');
        if (!empty($NickName)) {
            $where[] = ["members.NickName",'like', "%$NickName%"];
        }
        $Phone = $request->input('Phone');
        if (!empty($Phone)) {
            $where[] = ["members.Phone",'like', "%$Phone%"];
        }
        $IdCard = $request->input('IdCard');
        if (!empty($IdCard)) {
            $IdCard=DB::table('MemberIdCard')->where('IdCard',$IdCard)->value('MemberId');
            $where[] = ["members.Id", $IdCard];
        }
        $IsBan = $request->input('IsBan');
        if ($IsBan!=='') {
            $where[] = ["members.IsBan", $IsBan];
        }
         $data=DB::table('members')
                ->where($where)
                ->leftJoin('members as a', 'members.ParentId', '=', 'a.Id')
                ->select('members.*', 'a.Phone as ParentPhone','a.NickName as ParentNickName','a.IsBan as ParentStatus')
                ->orderBy('members.Id', 'desc')
                ->paginate($count);
        foreach ($data as $item) {
//            $item->Level = $this->Level($item->Level);
//            dd($item->Level);
            $QrCode=DB::table('BindPay')->where('MemberId',$item->Id)->first();
            if (!empty($QrCode->QrCode)){
            $item->qrcode=$QrCode->QrCode;
            $item->paytype=$QrCode->PayType;
            }
            $item->Money=DB::table('membercoin')->where('CoinId',5)->where('MemberId',$item->Id)->value('Money');
            $item->ParentName= DB::table('memberidcard')->where('MemberId',$item->ParentId)->value('AuthName');
            //直推有效人数
//            $effective_people_count = DB::table('Members')
//                ->where('isAuth', 1)
//                ->where('ParentId', $item->Id)
//                ->count('Id');
//             $item->effective_people_count=$effective_people_count;//直推有效人数
//             $item->SettingLevel = intval($item->SettingLevel);
              $user_id=$item->Id;
             $datas= DB::table('miner_user_team_info')->where('user_id',$user_id)->get();
             foreach ($datas as &$k){
                 $item->effective_people_count=$k->effective_people_count;//直推有效人数
                 $item->total_computing_power = $k->total_computing_power;//团队总算力
             }
//            $sum1=DB::table('FinancingList_00')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum2=DB::table('FinancingList_01')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum3=DB::table('FinancingList_02')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum4=DB::table('FinancingList_03')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum5=DB::table('FinancingList_04')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum6=DB::table('FinancingList_05')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum7=DB::table('FinancingList_06')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum8=DB::table('FinancingList_07')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum9=DB::table('FinancingList_08')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum10=DB::table('FinancingList_09')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum11=DB::table('FinancingList_10')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum12=DB::table('FinancingList_11')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum13=DB::table('FinancingList_12')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum14=DB::table('FinancingList_13')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum15=DB::table('FinancingList_14')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum16=DB::table('FinancingList_15')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum17=DB::table('FinancingList_16')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum18=DB::table('FinancingList_17')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum19=DB::table('FinancingList_18')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $sum20=DB::table('FinancingList_19')->where('MemberId',$item->Id)->where('Mold',8)->sum('Money');
//            $SUM=$sum1+$sum2+$sum3+$sum4+$sum5+$sum6+$sum7+$sum8+$sum9+$sum10+$sum11+$sum12+$sum13+$sum14+$sum15+$sum16+$sum17+$sum18+$sum19+$sum20;
//            $item->SumMoney=$SUM;

//            //团对总算力
//            $teamComputingPower = $this->teamComputingPower($user_id) ?? 0;
//            $item->total_computing_power = $teamComputingPower + DB::table('Members')
//                ->where('Id', $user_id)->value('invite_computing_power');
//            $item->pople=$this->userChild($user_id);//我的团队 往下数6代
        }
        $SaplingList = DB::table("miner_sapling")
            ->select("id", "nickname")
            ->get();
        $res = [];
        $res["total"] = $data->total();
        $res["list"] = $data->items();
        $res["SaplingList"] = $SaplingList;
        return self::returnMsg($res);
//        return self::returnMsg($data ,'', 20000);
    }
    //禁用团队
    public function TeamDisable(Request $request){
        $data=$request->post();
        $user_id= !empty($data["Id"]) ? intval($data["Id"]) : 0;
        $Ids=$this->userChild($user_id);//我的团队
        $data1=[
            'IsBan'=>1,
        ];
        $data2=[
            'IsBan'=>1,
            'TeamDisable'=>1
        ];
        $data3=[
            'IsBan'=>0,
        ];
        $data4=[
            'IsBan'=>0,
            'TeamDisable'=>0
        ];
        if(empty($Ids)) return self::returnMsg([],'该会员暂无下级用户',20001);
        if($Ids && $data['Type']==1){
            DB::table('members')->whereIn('Id',$Ids)->update($data1);
            DB::table('members')->where('Id',$user_id)->update($data2);
        }
        if ($Ids && $data['Type']==2){
            DB::table('members')->whereIn('Id',$Ids)->update($data3);
            DB::table('members')->where('Id',$user_id)->update($data4);
        }
        return self::returnMsg([], '', 20000);
    }

    /**
     * 替换手机号
     * @param $mobile 手机号
     * @param string $data 替换字符串
     * @return string|string[]|null
     */
    function replaceMobile($mobile, $data = '****')
    {
        $pattern = '/(\d{3})(\d{4})(\d{4})/i';
        $replacement = "$1$data$3";
        return preg_replace($pattern, $replacement, $mobile);
    }

    public function teamComputingPower($user_id)
    {
        //此处调用用户6代内的用户编号
        $child = $this->userChild($user_id, 6);
        if (empty($child)) {
            return 0;
        }
        return DB::table('miner_user_computing_power')
            ->whereIn('user_id', $child)
            ->where('type', '>', 1)
            ->where('end_time', '>=', $this->dateFormat())
            ->sum('computing_power');
    }
    /**
     * 获取我的团队
     * @param $user_id 用户本身
     * @param int $algebra 往下数代数
     * @return array
     */
    function userChild($user_id)
    {
        $user = MembersModel::find($user_id);
        $users = MembersModel::select(['IsAuth', 'Root', 'Id'])->get();
        $data = [];
        foreach ($users as &$u) {
            if ($u->IsAuth == 0) {
                continue;
            }
            $roots = array_reverse(explode(',', trim($u->Root, ',')));
            $length = count($roots);
            if ($length > 0) {

//                $temp = array_slice($roots, 0, $algebra > $length ? $length : $algebra);
                if (in_array($user['Id'], $roots) && $user['Id'] != $u['Id']) {
                    $data[] = $u['Id'];
                }
            }
        }
        return $data;
    }
    //时间格式化
    function dateFormat($time = '', $format = 'Y-m-d H:i:s')
    {
        if (empty($time)) {
            return date($format, time());
        }
        return date($format, $time);
    }
    private function Level(int $Level)
    {
        return str_replace([0, 1, 2, 3, 4], ['E1', 'E2', 'E3', 'E4', 'E5'], $Level);
    }
 //修改会员信息
    public function MembersEdit(Request $request){
        $data=$request->post();
        if (empty($data['Id'])) return self::returnMsg([],'参数错误',20001);
        if (empty($data['Phone'])) return self::returnMsg([],'请输入用户手机号',20001);
        $list=DB::table('members')->where('Id',$data['Id'])->get();
        foreach ($list as $v){
            $Phone=$v->Phone;
        }
         if ($data['Phone']!=$Phone){
             $list1=DB::table('members')->where('Phone',$data['Phone'])->first();
             if (!empty($list1)) return self::returnMsg([],'该手机号已存在',20001);
         }
        $res1=[
            'Phone'=>$data['Phone'],
        ];
        DB::table('members')->where('Id',$data['Id'])->update($res1);
        if (!empty($data['qrcode'])){
        if (!DB::table('BindPay')->where('MemberId',$data['Id'])->first())
        return self::returnMsg([],'该会员没有绑定支付宝',20001);
        $res=[
            'QrCode'=>$data['qrcode'],
        ];
        DB::table('BindPay')->where('MemberId',$data['Id'])->update($res);
        }
        return self::returnMsg([], '', 20000);
    }
    /**
     * @func查看我的下级
     */
    public function subList(Request $request)
    {
        $where = function ($query) use ($request) {
            //筛选查询关键字
            if ($request->has('ParentId') and $request->ParentId != '') {
                $query->where('members.ParentId', '=', $request->ParentId);
            }
        };
        $bannerlist = members::GetPageList($request->get('count'), $where);
        return self::returnMsg($bannerlist);
    }


    /**
     * @func查看我的持币
     */
    public function holdCoin(Request $request)
    {

        $mid = (int)trim($request->input('mid'));
        if (empty($mid))
            return self::returnMsg([], 'id不能为空', 20003);

        $memberCoinList = MemberCoin::GetPageList($request->get('count'), $mid);
        return self::returnMsg($memberCoinList);
    }


    /**
     * @func修改余额
     */
    public function memberCoinUpdate(Request $request)
    {
        $rules = [
            'mcid' => 'required|integer|min:1',
            'money' => 'required|numeric',
        ];
        $v = Validator::make($request->all(), $rules);
        if ($v->fails())
            return self::errorMsg($v->errors());
        $sqlmap = $v->validated();

        $memberCoin_arr = MemberCoin::GetBId($sqlmap['mcid']);
        if (!isset($memberCoin_arr))
            return self::errorMsg('没有找到用户该币种信息');

        $coin = CoinModel::get_by_id($memberCoin_arr->CoinId);
        if (!isset($coin))
            return self::errorMsg('没有找到该币种信息');

        if ($coin->EnName == 'IA') {
            //IA币需要单独记录
            $res = $this->dealIaUpdate($memberCoin_arr, $sqlmap['money'], $request);
            return $res;
        } else {
            $memberCoin_arr->Money = bcadd($memberCoin_arr->Money, $sqlmap['money'], 10);
            $mold = FinancingListModel::get_mold_by_call_index('admin_member_coin_update');
            if ($memberCoin_arr->Money < 0)
                return self::errorMsg('用户余额不足');

            DB::beginTransaction();
            try {
                $memberCoin_arr->save();
                FinancingListModel::WriteLog($memberCoin_arr->MemberId, $memberCoin_arr->CoinId, $coin->EnName, $sqlmap['money'],
                    $memberCoin_arr->Money, $mold->id, '平台奖励金额', '平台奖励金额记账');
                DB::commit();
                return self::successMsg();
            } catch (\Exception $exception) {
                DB::rollBack();
                return self::errorMsg($exception->getMessage());
            }
        }
    }

    //币种审核待处理
    private function dealIaUpdate($memberCoin, $Money, $request)
    {
        $members = DB::table('members')->where('Id', $memberCoin['MemberId'])->first();
        $IAMainAddressConfig = DB::table('IAMainAddressConfig')->first();

        if (!$members->WalletAccount || !$members->WalletPrivateKey) return self::returnError(20001, '该用户未绑定钱包私钥或地址');
        /**
         * 金额为负则为减少
         */
        if ($memberCoin['Money'] <= ($Money * -1)) return self::returnError(20001, '金额不足');

        if ($Money > 0) {
            //增加金额，主地址转到私有地址
            $privateKey = $IAMainAddressConfig->PrivateKey;
            $account = $members->WalletAccount;
        } else {
            //减少金额，私有地址转到主地址
            $privateKey = $members->WalletPrivateKey;
            $account = $IAMainAddressConfig->Account;
        }
        $thrift = new base('CoinServer', 'IIAService');
        $thrift->to_Tsorket(env('THRIFT_IP_COIN'), env('THRIFT_PORT_COIN'));//默认测试服务器
        $thrift->transport->open();
//        var_dump($thrift->IIA->GetBalance($members->WalletAccount));
        $result = $thrift->IIA->Send($privateKey, $account, abs($Money), rand(1, 10000));
        $thrift->transport->close();
        $result = json_decode($result, true);
        //dd($result);
        if ((int)$result['status'] == 1) {
            $logSql = [
                'Uri' => $request->url(),
                'Name' => '后台操作IA币余额变动,Id为' . $members->Id . '的用户IA币余额变动' . $Money,
                'Ip' => $request->ip(),
                'Admin' => $request->get('uid'),
                'Time' => time(),
            ];
            DB::table('adminlog')->insert($logSql);
            if ($Money < 0) {
                //减少币，需要写日志和减少MemberCoin
                DB::beginTransaction();
                try {
                    FinancingListModel::WriteLog($members->Id, $memberCoin['CoinId'], $memberCoin['CoinName'], $Money, bcadd($memberCoin['Money'], $Money, 10),
                        2, '平台奖励金额', '平台奖励金额记账');
                    DB::table('membercoin')->where('MemberId', $members->Id)->where('CoinId', $memberCoin['CoinId'])->increment('Money', $Money);
                    DB::commit();
                    return self::returnMsg('操作成功', '操作成功');
                } catch (\Exception $exception) {
                    DB::rollBack();
                    return self::returnError(20001, $exception->getMessage());
                }
            }
            return self::returnMsg([], '操作成功，请等待区块转账', 20000);
        } else {

            $result['privateKey'] = $privateKey;
            $result['account'] = $account;
            $result['Money'] = $Money;
            return self::returnMsg($result, $result['msg'], 20001);
        }
    }


    /**
     * @func修改锁定余额
     */
    public function memberCoinLockMoney(Request $request)
    {
        $rules = [
            'mcid' => 'required|integer|min:1',
            'money' => 'required|numeric',
        ];
        $v = Validator::make($request->all(), $rules);
        if ($v->fails())
            return self::errorMsg($v->errors());
        $sqlmap = $v->validated();

        $memberCoin_arr = MemberCoin::GetBId($sqlmap['mcid']);
        if (!isset($memberCoin_arr))
            return self::errorMsg('没有找到用户该币种信息');

        $coin = CoinModel::get_by_id($memberCoin_arr->CoinId);
        if (!isset($coin))
            return self::errorMsg('没有找到该币种信息');

        if ($coin->EnName == 'IA')
            return self::errorMsg('IA币不允许锁定');

        $memberCoin_arr->LockMoney = bcadd($memberCoin_arr->LockMoney, $sqlmap['money'], 10);
        $memberCoin_arr->Money = bcsub($memberCoin_arr->Money, $sqlmap['money'], 10);
        $mold = FinancingListModel::get_mold_by_call_index('admin_member_coin_lock');
        if ($memberCoin_arr->Money < 0 || $memberCoin_arr->LockMoney < 0)
            return self::errorMsg('用户余额不足');
        DB::beginTransaction();
        try {
            $memberCoin_arr->save();
            FinancingListModel::WriteLog($memberCoin_arr->MemberId, $memberCoin_arr->CoinId, $coin->EnName, $sqlmap['money'] * -1,
                $memberCoin_arr->Money, $mold->id, '平台锁定金额', '平台锁定金额记账');
            DB::commit();
            return self::successMsg();
        } catch (\Exception $exception) {
            DB::rollBack();
            return self::errorMsg($exception->getMessage());
        }
    }


    /**
     * 禁用会员账户
     */
    public function membersStatus(Request $request)
    {
        $id = (int)($request->input('id'));
        $member = Members::find($id);
        if (!$member)
            return self::returnMsg([], '未找到此用户', 20001);

        $result = Members::where(['Id' => $id])->update(['IsBan' => intval(!$member->IsBan)]);

        return self::returnMsg([], '操作成功', 20000);

    }

//    //ctc设置
//    public function CtcSetting(Request $request)
//    {
//        $id = (int)($request->input('Id'));
//        $rules = [
//            'IsOpenFee' => 'required|integer',
//            'IsFrozenCTC' => 'required|integer',
//            'Fee' => 'required|numeric',
//            'RecvFee' => 'required|numeric',
//        ];
//        $v = Validator::make($request->all(), $rules, [
//            'IsOpenFee.required' => '请选择是否启用个人手续费',
//            'IsFrozenCTC.required' => '请选择是否冻结CTC功能',
//            'Fee.required' => '请填写出售手续费',
//            'RecvFee.required' => '请填写收款手续费',
//        ]);
//        if ($v->fails())
//            return self::errorMsg($v->errors()->first());
//        $sqlmap = $v->validated();
//        $member = Members::find($id);
//        if (!$member)
//            return self::returnMsg([], '未找到此用户', 20001);
//
//        $result = DB::table('Members')->where('Id', $id)->update($sqlmap);
//
//        if ($result)
//            return self::returnMsg([], '操作成功', 20000);
//
//        return self::returnMsg([], '操作失败', 20011);
//    }


    /**
     * 根据id获取我持币的某一条记录
     */
    public function getCoinId(Request $request)
    {
        $cid = (int)trim($request->input('cid'));
        if (empty($cid))
            return self::returnMsg([], 'id不能为空', 20003);

        $result = MemberCoin::GetBId($cid);
        if ($result)
            return self::returnMsg($result, '获取成功', 20000);
        return self::returnMsg([], '操作失败', 20011);
    }

//用户算力
    public function UserComputingPower(Request $request)
    {
        $where = [];
        $count = intval($request->input('limit', 20));
        $user_id=$request->input('user_id');
        if (!empty($user_id)){
            $where[] = ["user_id", "like", "user_id"];
        }
        $lists = DB::table("miner_user_computing_power")
            ->where($where)
            ->orderBy("id", "DESC")
            ->paginate($count);
        foreach ($lists as $item) {
            $NickName = DB::table('members')->where('Id', $item->user_id)->first();
            if (!empty($NickName)) $NickName = $NickName->NickName;
            $item->NickName = $NickName;//用户
            $ChildUser=DB::table('members')->where('Id',$item->child_user_id)->first();
            if (!empty($ChildUser)) $ChildUser = $ChildUser->NickName;
            $item->ChildUser=$ChildUser; //贡献者
        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    //分享奖励
    public function share_reward_record(Request $request){
        $where = [];
        $count = intval($request->input('limit', 20));
        $Phone = $request->input('Phone');
        if (!empty($Phone)) {
            $where[] = ["Phone", "like", "%$Phone%"];
        }
        $NickName = $request->input('NickName');
        if (!empty($NickName)) {
            $where[] = ["m.NickName", "like", "%$NickName%"];
        }
        $lists = DB::table("miner_sapling_share_reward_record")
            ->leftjoin('members as m','m.Id','=','miner_sapling_share_reward_record.user_id')
            ->where($where)
            ->select('miner_sapling_share_reward_record.*','m.NickName','m.Phone')
            ->orderBy('id','desc')
            ->paginate($count);
        foreach ($lists as &$item){
            $arr=$item->reward;
            $reward=json_decode($arr);
            $miner_sapling_id=$reward->miner_sapling_id;
           $miner_sapling=DB::table('miner_sapling')->where('id',$miner_sapling_id)->first();
           $item->nickname=$miner_sapling->nickname;
            $item->number=$reward->number;
        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    //用户拥有的老鼠
    public function user_sapling_package(Request $request){
        $where = [];
        $count = intval($request->input('limit', 20));
        $Phone = $request->input('Phone');
        if (!empty($Phone)) {
            $where[] = ["Phone", "like", "%$Phone%"];
        }
        $NickName = $request->input('NickName');
        if (!empty($NickName)) {
            $where[] = ["m.NickName", "like", "%$NickName%"];
        }
        $lists = DB::table("mouse_user")
            ->leftjoin('members as m','m.Id','=','mouse_user.user_id')
            ->leftjoin('mouse_list as p','p.id','=','mouse_user.mouse_list_id')
            ->where($where)
            ->select('mouse_user.*','m.NickName','m.Phone','p.nickname')
            ->orderBy('id','desc')
            ->paginate($count);
        foreach ($lists as $v){
            $v->create_time=date('Y-m-d H:i:s',$v->create_time);
            $v->delete_time=date('Y-m-d H:i:s',$v->delete_time);
            $v->update_time=date('Y-m-d H:i:s',$v->update_time);
            $v->healing_time_ltt=date('Y-m-d H:i:s',$v->healing_time_ltt);
            if ($v->healing_time_ltt=='1970-01-01 08:00:00'){
                $v->healing_time_ltt='';
            }
        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    //删除用户拥有的老鼠
    public function user_sapling_packageDel(Request $request){
        $data = $request->post();
        $id=$data['id'];
        if(empty($id)){
            return self::returnMsg([], '请选择需要操作的记录', 20001);
        }
        UserSaplingPackageModel::DelById($id);
        return self::returnMsg();
    }
    //用户环保等级
    public function miner_user_level(Request $request){
        $where = [];
        $count = intval($request->input('limit', 20));
        $Phone = $request->input('Phone');
        if (!empty($Phone)) {
            $where[] = ["Phone", "like", "%$Phone%"];
        }
        $NickName = $request->input('NickName');
        if (!empty($NickName)) {
            $where[] = ["m.NickName", "like", "%$NickName%"];
        }
        $miner_level_id = $request->input('miner_level_id');
        if (!empty($miner_level_id)) {
            $where[] = ["miner_level_id",$miner_level_id];
        }
        $lists = MinerUserLevelModel::GetPageList( $count,$where);
//        dd($lists);
//        $lists = DB::table("miner_user_level")
//            ->leftjoin('Members as m','m.Id','=','miner_user_level.user_id')
//            ->leftjoin('miner_level as p','p.id','=','miner_user_level.miner_level_id')
//            ->where($where)
//            ->select('miner_user_level.*','m.NickName','m.Phone','p.nickname')
//            ->orderBy('id','desc')
//            ->paginate($count);
        $leveList = DB::table("miner_level")
            ->select("id", "level","nickname")
            ->get();
        $LeveArr = [];
        foreach ($leveList as $v) {
            $LeveArr[$v->id] = $v->nickname;
        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        $res["leveList"] = $leveList;
        return self::returnMsg($res);
    }
    //会员等级审核
    public function levelEdit(Request $request){
     $data=$request->post();
        $Id = $data['id'];

        if (empty($Id)) {
            return self::returnMsg([],'请选择需要操作的记录',20001);
        }
//        $res=[
//            'is_audit'=>0
//        ];
//       $re= DB::table("miner_user_level")->where("id", $Id)->update($res);
//       if($re){
//           return 1212;
//       }
        MinerUserLevelModel::updatedById($Id);
       return self::returnMsg();
    }
    //会员分红记录
    public function user_dividendList(Request $request){
        $where = [];
        $count = intval($request->input('limit', 20));
        $Phone = $request->input('Phone');
        if (!empty($Phone)) {
            $where[] = ["Phone", "like", "%$Phone%"];
        }
        $NickName = $request->input('NickName');
        if (!empty($NickName)) {
            $where[] = ["m.NickName", "like", "%$NickName%"];
        }
        $lists = DB::table("miner_user_dividend")
            ->leftjoin('members as m','m.Id','=','miner_user_dividend.user_id')
            ->leftjoin('miner_level as l','l.id','=','miner_user_dividend.user_level')
            ->where($where)
            ->select('miner_user_dividend.*','m.NickName','m.Phone','l.nickname','l.level')
            ->orderBy('id','desc')
            ->paginate($count);
        foreach ($lists as $v){
            $v->levles=$v->nickname.' '.' '.'('.'等级'.$v->level.')';
        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    //合伙人
    public function Partner(Request $request){
        $id = intval($request->input('Id'));
        $member = DB::table('members')->where('Id', $id)->first();
        $isPartner=$member->isPartner;
        if($member->IsBan == 1) return self::errorMsg('此用户已被禁用');
        if($isPartner==0){
       $data=[
           'isPartner'=>1
       ];
            DB::table('members')->where('Id',$id)->update($data);
        }else {
            $data1 = [
                'isPartner' => 0
            ];
            DB::table('members')->where('Id', $id)->update($data1);
        }
        return self::returnMsg();
        }
    //会员等级规则
   public function UserLevel(Request $request){
       $data =DB::table('Setting')->where('k','ctc_level_rule')->get();
       foreach ($data as &$item){
           $rule=json_decode($item->v);
       }
       return self::returnMsg($rule, 20000);
   }
   //会员等级修改
    public function UserLevelEdit(Request $request){
            $data=$request->post();
            $res = json_encode($data, true);
            $arr = [
                'v' => $res,
            ];
        DB::table('Setting')->where('k','ctc_level_rule')->update($arr);
        return self::returnMsg();
        }
    //交易白名单
    public function TransactionWhitelist(Request $request){
        $where = function ($query) use ($request) {
            if ($request->has('keywords') and $request->keywords != '') {
                $keywords = "%" . $request->keywords . "%";
                $query->orWhere('Title', 'like', $keywords)->orWhere('Title', 'like', $keywords);
            }
        };
        $count = intval($request->input('limit', 20));
        $lists=DB::table('setting')->where('k','white_list')->get();
        foreach ($lists as &$item){
            $members = explode(",", $item->v);
            $data=DB::table('members')
                ->where($where)
                ->whereIn('Id',$members)
                ->orderBy('Id', 'desc')
                ->paginate($count);
//            foreach ($data as &$v){
//                $Coin = DB::table('membercoin')->where('CoinName','PT')->whereIn('MemberId',$members)->first();
//                foreach ($Coin as &$value){
//                    if (!empty($value->Money)){
//                    $v->Money=$value->Money;
//                }
//                }
//            }
        }
        $res = [];
        $res["total"] = $data->total();
        $res["list"] = $data->items();
        return self::returnMsg($res);
    }
    //添加白名单
    public function whiteAdd(Request $request){
        $data=$request->post();
        $Phone=$data['Phone'];
        $res=DB::table('members')->where('Phone',$Phone)->first();
        if (empty($Phone)){
            return self::returnMsg([],'请输入会员手机号','20001');
        }
        $WhiteList=DB::table('setting')->where('k','white_list')->get();
        foreach ($WhiteList as $item){
            if ($res) $Ids=$item->v;
            $item = explode(",", $item->v);
        }
        if (!$res) return self::returnMsg([],'该会员不存在，请检查手机号输入是否有误！',20001);
        $Id=$res->Id;
        if (in_array($Id, $item)) return self::returnMsg([],'该会员已存在白名单内，请勿重复添加！',20001);
        $data=[
            'v'=>$Ids.','.$Id
        ];
        DB::table('setting')->where('k','white_list')->update($data);
        $data1=[
            'v'=>$Id
        ];
        if (empty($Ids)){
            DB::table('setting')->where('k','white_list')->update($data1);
        }
        return self::returnMsg([],'操作成功',20000);

    }
    public function whiteDel(Request $request){
        $data=$request->post();
        $WhiteList=DB::table('setting')->where('k','white_list')->get();
        foreach ($WhiteList as $item){
            $items = explode(",", $item->v);
            $arr = array_merge(array_diff($items, array($data['Id'])));
            $user_ids=implode(',',$arr);
            $data=[
                'v'=>$user_ids
            ];
            DB::table('setting')->where('k','white_list')->update($data);
        }
        return self::returnMsg([],'操作成功',20000);
    }
    public function AdminOperationRecord(Request $request){
        $where = function ($query) use ($request) {
            if ($request->has('keywords') and $request->keywords != '') {
                $keywords = "%" . $request->keywords . "%";
                $query->orWhere('MemberId', 'like', $keywords)->orWhere('Phone', 'like', $keywords);
            }
        };
        $count = intval($request->input('limit', 1000));
        $lists=FinancingListModel::Lists($count,$where);
        foreach ($lists as &$item) {
            $item->AddTime = date('Y-m-d H:i:s', $item->AddTime);
        }
            $sum1=DB::table('FinancingList_00')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum2=DB::table('FinancingList_01')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum3=DB::table('FinancingList_02')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum4=DB::table('FinancingList_03')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum5=DB::table('FinancingList_04')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum6=DB::table('FinancingList_05')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum7=DB::table('FinancingList_06')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum8=DB::table('FinancingList_07')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum9=DB::table('FinancingList_08')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum10=DB::table('FinancingList_09')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum11=DB::table('FinancingList_10')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum12=DB::table('FinancingList_11')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum13=DB::table('FinancingList_12')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum14=DB::table('FinancingList_13')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum15=DB::table('FinancingList_14')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum16=DB::table('FinancingList_15')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum17=DB::table('FinancingList_16')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum18=DB::table('FinancingList_17')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum19=DB::table('FinancingList_18')->where('Mold',8)->where('Money','>',0)->sum('Money');
            $sum20=DB::table('FinancingList_19')->where('Mold',8)->where('Money','>',0)->sum('Money');
        $SUM=$sum1+$sum2+$sum3+$sum4+$sum5+$sum6+$sum7+$sum8+$sum9+$sum10+$sum11+$sum12+$sum13+$sum14+$sum15+$sum16+$sum17+$sum18+$sum19+$sum20;
        $today             = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $sum1=DB::table('FinancingList_00')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum2=DB::table('FinancingList_01')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum3=DB::table('FinancingList_02')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum4=DB::table('FinancingList_03')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum5=DB::table('FinancingList_04')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum6=DB::table('FinancingList_05')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum7=DB::table('FinancingList_06')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum8=DB::table('FinancingList_07')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum9=DB::table('FinancingList_08')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum10=DB::table('FinancingList_09')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum11=DB::table('FinancingList_10')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum12=DB::table('FinancingList_11')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum13=DB::table('FinancingList_12')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum14=DB::table('FinancingList_13')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum15=DB::table('FinancingList_14')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum16=DB::table('FinancingList_15')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum17=DB::table('FinancingList_16')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum18=DB::table('FinancingList_17')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum19=DB::table('FinancingList_18')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $sum20=DB::table('FinancingList_19')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$today)->sum('Money');
        $TodaySUM=$sum1+$sum2+$sum3+$sum4+$sum5+$sum6+$sum7+$sum8+$sum9+$sum10+$sum11+$sum12+$sum13+$sum14+$sum15+$sum16+$sum17+$sum18+$sum19+$sum20;
         $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        $res["SUM"] = $SUM;
        $res["TodaySUM"] = $TodaySUM;
        return self::returnMsg($res);
    }
    //管理员赠送树苗记录
    public function GiveSaplingList(Request $request){
        $where = [];
        $count = intval($request->input('limit', 20));
        $Phone = $request->input('Phone');
        if (!empty($Phone)) {
            $where[] = ["Phone", "like", "%$Phone%"];
        }
        $nickname = $request->input('nickname');
        if (!empty($nickname)) {
            $where[] = ["s.nickname", "like", "%$nickname%"];
        }
        $lists = DB::table("miner_user_sapling")
            ->leftjoin('members as m','m.Id','=','miner_user_sapling.user_id')
            ->Leftjoin('miner_sapling as s','s.id','=','miner_user_sapling.sapling_id')
            ->where('type',5)
            ->where($where)
            ->select('miner_user_sapling.*','m.NickName','m.Phone','s.nickname')
            ->orderBy('id','desc')
            ->paginate($count);
//        foreach ($lists as &$v){
//            DB::table('miner_user_sapling')->where('remarks','后台赠送')->sum('')
//
//        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    //管理员赠送树苗
    public function GiveSapling(Request $request){
        $data=$request->post();
//        dd($data);
        $saplings=$data['sapling_id'];
        $nickname=DB::table('miner_sapling')->where('nickname',$saplings)->get();
        if (empty($nickname)) self::returnMsg([],'参数错误，请重新选择！',20001);
        foreach ($nickname as &$item){
            $sapling_id=$item->id;
        }
        $user_id=intval($data['Id']);
        $IsBan=DB::table('members')->where('Id',$user_id)->where('IsBan',0)->first();
        $IsAuth=DB::table('members')->where('Id',$user_id)->where('IsAuth',1)->first();
        if (empty($IsBan)) return self::returnMsg([],'该会员已被禁用！',20001);
        if (empty($IsAuth)) return self::returnMsg([],'该会员未实名认证！',20001);
        if (empty($user_id)) return self::returnMsg([],'参数错误',20001);
        if (empty($sapling_id)) return self::returnMsg([],'请选择需要赠送的树苗',20001);
        $Numbers=intval($data['Numbers']);
        if (empty($Numbers)) return self::returnMsg([],'请输入赠送数量',20001);
        if ($Numbers>1) return self::returnMsg([],'每次赠送数量不能超过1颗',20001);
        if (!empty($sapling_id)){
        $tree=DB::table('miner_sapling')->where('id',$sapling_id)->get();
        foreach ($tree as &$item){
            $sn = new Snowflake();
            $id = $sn->nextId();//id
            $total_amount=$item->total_profit;//总收益
            $type=5;//类型（后台赠送）
            $total_price=$item->price;//岗位售价
            $computing_power=$item->computing_power;//算力
            $rate_of_return=$item->rate_of_return;//收益率
            $release_amount=0;//以释放金额
            $surplus_amount=$item->total_profit;//剩余释放金额
            $total_freed=$this->basieEvent()+$item->cycle; //总释放天数
            $freed=$this->basieEvent()+$item->cycle; //剩余释放天数
            $yield=bcdiv($item->total_profit,$total_freed,4);//日产量
            $begin_receive_time=date('Y-m-d H:i:s');//开始领取时间
            $create_time=date('Y-m-d H:i:s');//创建时间
            $release_complete_time = $this->dateFormat(strtotime('+' . $total_freed . 'day'));//释放完成时间
            $remarks='人工后台赠送';
            $is_disable=0;//是否禁用
            $is_delete=0;//是否删除
            $is_release_complete=0;//是否释放完成
            $is_experience=0;//是否为体验
            $is_superior_reward=0;//是否有上级奖励
            $is_gave_away=1;//后台赠送
        }
        //添加记录
        $data=[
            'id'=>$id,
            'user_id'=>$user_id,
            'sapling_id'=>$sapling_id,
            'total_amount'=>$total_amount,
            'type'=>$type,
            'yield'=>$yield,
            'computing_power'=>$computing_power,
            'total_price'=>$total_price,
            'rate_of_return'=>$rate_of_return,
            'release_amount'=>$release_amount,
            'surplus_amount'=>$surplus_amount,
            'total_freed'=>$total_freed,
            'freed'=>$freed,
            'is_disable'=>$is_disable,
            'is_delete'=>$is_delete,
            'is_release_complete'=>$is_release_complete,
            'is_experience'=>$is_experience,
            'is_superior_reward'=>$is_superior_reward,
            'is_gave_away'=>$is_gave_away,
            'create_time'=>$create_time,
            'begin_receive_time'=>$begin_receive_time,
            'release_complete_time'=>$release_complete_time,
            'remarks'=>$remarks,

        ];
        DB::table('miner_user_sapling')->insert($data);
        }
        return self::returnMsg([],'操作成功！',20000);
    }
    /**
     * 延长周期天数
     */
    public function basieEvent()
    {
        //基础时间
        $basie_event = DB::table('Setting')->where('k', 'basie_event')->value('v') ?? 0;
        //延长天数
        $delay_period = DB::table('Setting')->where('k', 'delay_period')->value('v') ?? 0;
        $s = $basie_event;
        $e = date('Y-m', time());;
        $start = new \DateTime($s);
        $end = new \DateTime($e);
        $interval = \DateInterval::createFromDateString('1 month');
        $period = new \DatePeriod($start, $interval, $end);
        $count = 0;
        foreach ($period as $dt) {
            $count++;
        }
        return $count * $delay_period;
    }
    /**
     * 会员EB余额/拥有树苗总数
     */
    public function UserAmount(Request $request){
        $sortby=$request->input('sortby','Money');//排序字段
        $order=$request->input('order','desc');//排序方式
        $where = [];
        $count = intval($request->input('limit', 20));
        $Phone = $request->input('Phone');
        $Id=DB::table('members')->where('Phone',$Phone)->value('Id');
        if (!empty($Id)) {
            $where[] = ["MemberId", "=", $Id];
        }
        $latestPosts = DB::table('miner_user_sapling')
            ->select('user_id', DB::raw('count(id) as sapling_count'))
            ->groupBy('user_id');

        $lists = DB::table('membercoin as mc')
            ->where('CoinId',5)
            ->joinSub($latestPosts, 'sp', function($join) {
                $join->on('mc.MemberId', '=', 'sp.user_id');
            },null,null,'left')
            ->leftJoin('members as m','mc.MemberId','m.Id')
            ->select(['mc.*','m.Phone','m.NickName','sp.sapling_count'])
            ->where($where)
            ->orderBy($sortby,$order)
            ->paginate($count);
//        foreach ($lists as $item){
//            $sum1=DB::table('FinancingList_00')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum2=DB::table('FinancingList_01')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum3=DB::table('FinancingList_02')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum4=DB::table('FinancingList_03')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum5=DB::table('FinancingList_04')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum6=DB::table('FinancingList_05')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum7=DB::table('FinancingList_06')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum8=DB::table('FinancingList_07')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum9=DB::table('FinancingList_08')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum10=DB::table('FinancingList_09')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum11=DB::table('FinancingList_10')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum12=DB::table('FinancingList_11')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum13=DB::table('FinancingList_12')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum14=DB::table('FinancingList_13')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum15=DB::table('FinancingList_14')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum16=DB::table('FinancingList_15')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum17=DB::table('FinancingList_16')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum18=DB::table('FinancingList_17')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum19=DB::table('FinancingList_18')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $sum20=DB::table('FinancingList_19')->where('MemberId',$item->MemberId)->where('Mold',8)->sum('Money');
//            $SUM=$sum1+$sum2+$sum3+$sum4+$sum5+$sum6+$sum7+$sum8+$sum9+$sum10+$sum11+$sum12+$sum13+$sum14+$sum15+$sum16+$sum17+$sum18+$sum19+$sum20;
//            $item->SumMoney=$SUM;
//        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    //油卡电话充值订单
    public function EcologyOrderList(Request $request){
        $where = [];
        $count = intval($request->input('limit', 20));
        $Phone = $request->input('Phone');
        if (!empty($Phone)) {
            $user_id=DB::table('members')->where('Phone',$Phone)->value('Id');
            $where[] = ["user_id", $user_id];
        }
        $Status = $request->input('Status');
        if ($Status!=='') {
            $where[] = ["status", $Status];
        }
        $lists = DB::table("ecology_order")
            ->leftJoin('members as m','m.Id','ecology_order.user_id' )
            ->where($where)
            ->orderBy("create_time", "DESC")
            ->select('ecology_order.*','m.Phone','m.NickName')
            ->paginate($count);
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    public function EcologyOrderCheck(Request $request){
        $data=$request->post();
         $Id = !empty($data["Id"]) ? intval($data["Id"]) : 0;
         $user_id = !empty($data["user_id"]) ? intval($data["user_id"]) : 0;
         if (empty($Id)) {
             return self::returnMsg([],'请选择需要操作的记录',20001);
         }
         $info = DB::table("ecology_order")
             ->where("id", $Id)
             ->where("status",0)
             ->first();
         if (empty($info)) {
             return self::returnMsg([],'请勿重复审核！',20001);
         }
         $Status=!empty($data["Status"]) ? intval($data["Status"]) : 0;
         $remarks = !empty($data["remarks"]) ? $data["remarks"] : "";
         if ($Status != 1 && $Status != 3) {
             return self::returnMsg([],'请选择操作状态',20001);
         }
         if ($Status == 1 && empty($remarks)) return self::returnMsg([],'请填写通过原因',20001);
         if ($Status == 3 && empty($remarks)) return self::returnMsg([],'请填写驳回原因',20001);
         DB::beginTransaction();
         try{
             $up_data = [];
             $up_data["status"] = $Status;
             $up_data["remarks"] = $remarks;
             $up_data["update_time"]=date('Y-m-d H:i:s');
             DB::table("ecology_order")->where("id", $Id)->update($up_data);
             if ($Status!==1){
                 $Money=DB::table('membercoin')->where('MemberId',$user_id)->where('CoinId',5)->value('Money');
                 $Moneys=$Money+$data['deduction_amount'];
                 $up_member_money = [
                     "Money"=>$Moneys
                 ];
                 DB::table("membercoin")->where('MemberId',$user_id)->where('CoinId',5)->update($up_member_money);
                 $sort = $user_id % 20;
                 if($sort < 10) $sort = '0'.$sort;
                 $table = 'FinancingList_'.$sort;
                 $data = [
                     'MemberId' => $user_id,
                     'Money' => $data['deduction_amount'],
                     'CoinId' => 5,
                     'CoinName' => 'EB',
                     'Mold' => 33,
                     'MoldTitle' => '充值驳回',
                     'Remark' => '充值驳回',
                     'AddTime' => time(),
                     'Balance' => $Moneys
                 ];
                 DB::table($table)->insert($data);
             }
             DB::commit();
         }
         catch(\Exception $e){
             echo $e->getMessage();
             DB::rollBack();
         }
         return self::returnMsg(["id" => $Id]);
     }
     //会员常规等级
    public function RegularGrade(){
        $lists=DB::table('other_conventional')->get();
        foreach ($lists as $item){
            $rule=json_decode($item->rule);
            $item->direct_push=$rule->direct_push;
            $item->mu=$rule->mu;
        }
        return self::returnMsg($lists);

    }
    public function RegularGradeEdit(Request $request)
    {
        $data = $request->post();
//        dd($data);
        $Id=$data['id'];
        if (empty($Id)) return self::returnMsg([],'参数错误',20001);
        if (empty($data['level_name'])) return self::returnMsg([],'名称必填',20001);
        if (!is_numeric($data['rate_of_return']) || $data['rate_of_return']<0) return self::returnMsg([],'收益率必填且大于等于0',20001);
        if (!is_numeric($data['direct_push'])) return self::returnMsg([],'直推人数必填',20001);
        if (!is_numeric($data['mu'])) return self::returnMsg([],'花田亩数必填',20001);
       $item=[
           'direct_push'=>$data['direct_push'],
           'mu'=>$data['mu'],
       ];
       $rule=json_encode($item,true);

        $data=[
            'level_name'=>$data['level_name'],
            'rate_of_return'=>$data['rate_of_return'],
            'rule'=>$rule,
        ];
     DB::table('other_conventional')->where('id',$Id)->update($data);
    return self::returnMsg([]);
    }
    //会员签到记录
    public function signList(Request $request)
    {
        $where = [];
        $count = intval($request->input('limit', 20));
        $Phone = $request->input('Phone');
        if (!empty($Phone)) {
            $where[] = ["Phone", "like", "%$Phone%"];
        }
        $nickname = $request->input('nickname');
        if (!empty($nickname)) {
            $where[] = ["s.nickname", "like", "%$nickname%"];
        }
        $lists = DB::table("members_sign")
            ->leftjoin('members as m', 'm.Id', '=', 'members_sign.user_id')
            ->where($where)
            ->select('members_sign.*', 'm.NickName', 'm.Phone')
            ->orderBy('id', 'desc')
            ->paginate($count);
        foreach ($lists as $v){
            $v->create_time = date('Y-m-d H:i:s', $v->create_time);
        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
}
