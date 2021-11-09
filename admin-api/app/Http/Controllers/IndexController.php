<?php

namespace App\Http\Controllers;

use App\Libraries\googleYzm\GoogleAuthenticator;
use Illuminate\Http\Request;
use App\Models\AdminUserModel as AdminUser;
use App\Models\AdminUserTokenModel as AdminToken;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public $List = [];

    public function Index(Request $request){}

    public function Statis(Request $request){
        $type = $request->input('type');
        switch ($type) {
            case 'members':
                $data = $this->members();
                break;
            case 'Admin':
                $data = $this->Admin();
                break;
            case 'GiveTree':
                $data = $this->GiveTree();
                break;
            case 'work':
                $data = $this->work();
                break;
            case 'h5Coin':
                $data = $this->h5Coin();
                //$data['IARechargeChainLog'] = $this->IARechargeChainLog();
                break;
            case 'order':
                $data = $this->order();
                break;
            default:
                break;
        }
        return self::returnMsg($data);
    }

    //用户信息
    public function userInfo(Request $request){
        $uid   = $request->get('uid');
        $admin = AdminUser::where('Id', $uid)->first();
        $data  = [
            'roles'        => $admin->roles_item,
            'avatar'       => $admin->Avatar,
            'introduction' => $admin->Introduction,
            'name'         => $admin->Name,
        ];
        return self::returnMsg($data, '', 20000);
    }

    //登录
    public function Login(Request $request){
        $name     = trim($request->input('username'));
        $password = trim($request->input('password'));
        $user     = AdminUser::GetByName($name);
        if (empty($user)) return self::returnMsg([], '不存在管理员', 20000);

        /**
         * 谷歌验证码
         */
//        $verification = trim($request->input('verification'));
//        if (empty($verification)) {
//            return self::returnMsg([], '请输入验证码', 20003);
//        } else {
//         $admin_user = AdminUser::where('Name','=',$name)->first();
//            if(empty($admin_user)) return self::returnMsg([],'没有找到后台管理员', 20003);
//            $google = new GoogleAuthenticator();
//            $checkResult = $google->verifyCode($admin_user->google_secret, $verification,1);
//            if(!$checkResult) return self::returnMsg(['code'=>$admin_user->google_secret],'验证码错误，请重新输入',20003);
//        }

        if (md5($password) != $user->Password) return self::returnMsg([], '账号或密码错误', 20000);

        //生成Token
        $token = self::MakeToken($user->Id);
        $has   = AdminToken::GetByUid($user->Id);
        if (empty($has)) {
            $res = DB::table('adminusertoken')->insert([
                'Token'      => $token,
                'ExpireTime' => (3600 * AdminToken::EXPIRE_HOUR) + time(),
                'FlushTime'  => time(),
                'AdminId'    => $user->Id,
            ]);
        } else {
            $res = DB::table('adminusertoken')->where('AdminId', $user->Id)->update([
                'Token'      => $token,
                'ExpireTime' => (3600 * AdminToken::EXPIRE_HOUR) + time(),
                'FlushTime'  => time(),
            ]);
        }
        if (empty($res)) return self::returnMsg([], '登录失败，请稍后再试', 20000);
        //Token加密
        $encryptToken = self::TokenEncrypt($user->Id, $token);
        return self::returnMsg(['Token' => $encryptToken], '', 20000);
    }

    //退出登录
    public function Logout(Request $request)
    {
        $uid = $request->get('uid');
        DB::table('adminusertoken')->where('AdminId', $uid)->delete();
        return self::returnMsg();
    }

    private function members()
    {
        $today             = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $last_day          = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));//昨天开始的
        $tomonth           = mktime(0, 0, 0, date('m'), 0, date('Y'));//上月结束
        $last_month        = mktime(0, 0, 0, date('m') - 1, 0, date('Y'));//上上月结束
        $toyear            = mktime(0, 0, 0, 1, 0, date('Y'));//去年结束
        $last_year         = mktime(0, 0, 0, 1, 0, date('Y') - 1);//前年结束
        $M                 = DB::table('members');
        $data['today']     = DB::table('members')->where('RegTime', '>', $today)->count();
        $data['tomonth']   = DB::table('members')->where('RegTime', '>', $tomonth)->count();
        $data['toyear']    = DB::table('members')->where('RegTime', '>', $toyear)->count();
        $data['all']       = DB::table('members')->count();
        $data['lastday']   = DB::table('members')->whereBetween('RegTime', [$last_day, $today])->count();
        $data['lastmonth'] = DB::table('members')->whereBetween('RegTime', [$last_month, $tomonth])->count();
        $data['lastyear']  = DB::table('members')->whereBetween('RegTime', [$last_year, $toyear])->count();
        $data['authenticated']=DB::table('members')->where('IsAuth',1)->where('RegTime', '>', $today)->count();//今日已经认证
        $data['unauthorized']=DB::table('members')->where('IsAuth',0)->where('RegTime', '>', $today)->count();//今日未认证
        $data['authenticatedall']       = DB::table('members')->where('IsAuth',1)->count();//已认证会员总数
        $data['unauthorizedall']       = DB::table('members')->where('IsAuth',0)->count();//未认证会员总数
        return $data;
    }
    private function Admin()
    {
        $today             = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $last_day          = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));//昨天开始的
        $tomonth           = mktime(0, 0, 0, date('m'), 0, date('Y'));//上月结束
        $last_month        = mktime(0, 0, 0, date('m') - 1, 0, date('Y'));//上上月结束
        $toyear            = mktime(0, 0, 0, 1, 0, date('Y'));//去年结束
        $last_year         = mktime(0, 0, 0, 1, 0, date('Y') - 1);//前年结束
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
        $sum1=DB::table('FinancingList_00')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum2=DB::table('FinancingList_01')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum3=DB::table('FinancingList_02')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum4=DB::table('FinancingList_03')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum5=DB::table('FinancingList_04')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum6=DB::table('FinancingList_05')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum7=DB::table('FinancingList_06')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum8=DB::table('FinancingList_07')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum9=DB::table('FinancingList_08')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum10=DB::table('FinancingList_09')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum11=DB::table('FinancingList_10')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum12=DB::table('FinancingList_11')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum13=DB::table('FinancingList_12')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum14=DB::table('FinancingList_13')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum15=DB::table('FinancingList_14')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum16=DB::table('FinancingList_15')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum17=DB::table('FinancingList_16')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum18=DB::table('FinancingList_17')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum19=DB::table('FinancingList_18')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $sum20=DB::table('FinancingList_19')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_day, $today])->sum('Money');
        $LastdaySUM=$sum1+$sum2+$sum3+$sum4+$sum5+$sum6+$sum7+$sum8+$sum9+$sum10+$sum11+$sum12+$sum13+$sum14+$sum15+$sum16+$sum17+$sum18+$sum19+$sum20;//昨天充值EB
        $sum1=DB::table('FinancingList_00')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum2=DB::table('FinancingList_01')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum3=DB::table('FinancingList_02')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum4=DB::table('FinancingList_03')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum5=DB::table('FinancingList_04')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum6=DB::table('FinancingList_05')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum7=DB::table('FinancingList_06')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum8=DB::table('FinancingList_07')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum9=DB::table('FinancingList_08')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum10=DB::table('FinancingList_09')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum11=DB::table('FinancingList_10')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum12=DB::table('FinancingList_11')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum13=DB::table('FinancingList_12')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum14=DB::table('FinancingList_13')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum15=DB::table('FinancingList_14')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum16=DB::table('FinancingList_15')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum17=DB::table('FinancingList_16')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum18=DB::table('FinancingList_17')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum19=DB::table('FinancingList_18')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $sum20=DB::table('FinancingList_19')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$toyear)->sum('Money');
        $ToyearSUM=$sum1+$sum2+$sum3+$sum4+$sum5+$sum6+$sum7+$sum8+$sum9+$sum10+$sum11+$sum12+$sum13+$sum14+$sum15+$sum16+$sum17+$sum18+$sum19+$sum20;//今年充值EB
        $sum1=DB::table('FinancingList_00')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum2=DB::table('FinancingList_01')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum3=DB::table('FinancingList_02')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum4=DB::table('FinancingList_03')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum5=DB::table('FinancingList_04')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum6=DB::table('FinancingList_05')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum7=DB::table('FinancingList_06')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum8=DB::table('FinancingList_07')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum9=DB::table('FinancingList_08')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum10=DB::table('FinancingList_09')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum11=DB::table('FinancingList_10')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum12=DB::table('FinancingList_11')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum13=DB::table('FinancingList_12')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum14=DB::table('FinancingList_13')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum15=DB::table('FinancingList_14')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum16=DB::table('FinancingList_15')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum17=DB::table('FinancingList_16')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum18=DB::table('FinancingList_17')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum19=DB::table('FinancingList_18')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $sum20=DB::table('FinancingList_19')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime',[$last_year, $toyear])->sum('Money');
        $LastyearSUM=$sum1+$sum2+$sum3+$sum4+$sum5+$sum6+$sum7+$sum8+$sum9+$sum10+$sum11+$sum12+$sum13+$sum14+$sum15+$sum16+$sum17+$sum18+$sum19+$sum20;//去年充值EB
        $sum1=DB::table('FinancingList_00')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum2=DB::table('FinancingList_01')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum3=DB::table('FinancingList_02')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum4=DB::table('FinancingList_03')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum5=DB::table('FinancingList_04')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum6=DB::table('FinancingList_05')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum7=DB::table('FinancingList_06')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum8=DB::table('FinancingList_07')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum9=DB::table('FinancingList_08')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum10=DB::table('FinancingList_09')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum11=DB::table('FinancingList_10')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum12=DB::table('FinancingList_11')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum13=DB::table('FinancingList_12')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum14=DB::table('FinancingList_13')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum15=DB::table('FinancingList_14')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum16=DB::table('FinancingList_15')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum17=DB::table('FinancingList_16')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum18=DB::table('FinancingList_17')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum19=DB::table('FinancingList_18')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $sum20=DB::table('FinancingList_19')->where('Mold',8)->where('Money','>',0)->where('AddTime','>',$tomonth)->sum('Money');
        $TomonthSUM=$sum1+$sum2+$sum3+$sum4+$sum5+$sum6+$sum7+$sum8+$sum9+$sum10+$sum11+$sum12+$sum13+$sum14+$sum15+$sum16+$sum17+$sum18+$sum19+$sum20;//本月充值EB
        $sum1=DB::table('FinancingList_00')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum2=DB::table('FinancingList_01')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum3=DB::table('FinancingList_02')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum4=DB::table('FinancingList_03')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum5=DB::table('FinancingList_04')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum6=DB::table('FinancingList_05')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum7=DB::table('FinancingList_06')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum8=DB::table('FinancingList_07')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum9=DB::table('FinancingList_08')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum10=DB::table('FinancingList_09')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum11=DB::table('FinancingList_10')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum12=DB::table('FinancingList_11')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum13=DB::table('FinancingList_12')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum14=DB::table('FinancingList_13')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum15=DB::table('FinancingList_14')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum16=DB::table('FinancingList_15')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum17=DB::table('FinancingList_16')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum18=DB::table('FinancingList_17')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum19=DB::table('FinancingList_18')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $sum20=DB::table('FinancingList_19')->where('Mold',8)->where('Money','>',0)->whereBetween('AddTime', [$last_month, $tomonth])->sum('Money');
        $LastmonthSUM=$sum1+$sum2+$sum3+$sum4+$sum5+$sum6+$sum7+$sum8+$sum9+$sum10+$sum11+$sum12+$sum13+$sum14+$sum15+$sum16+$sum17+$sum18+$sum19+$sum20;//上月充值EB
//        dd($ToyearSUM);
        $data['today']     = $TodaySUM;//今天
        $data['lastday']   = $LastdaySUM;//昨天
        $data['tomonth']   = $TomonthSUM;//本月
        $data['lastmonth'] =$LastmonthSUM;//上月
        $data['toyear']    =$ToyearSUM;//今年
        $data['lastyear']  = $LastyearSUM;//去年
        $data['all']       = $SUM;//总计
//
        return $data;
    }

    private function h5Coin()
    {
        $today      = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $today1=$this->dateFormat($today);
        $last_day   = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));//昨天开始的
        $last_day1=$this->dateFormat($last_day);
        $tomonth    = mktime(0, 0, 0, date('m'), 0, date('Y'));
        $tomonth1=$this->dateFormat($tomonth);
        $last_month = mktime(0, 0, 0, date('m') - 1, 0, date('Y'));
        $last_month1=$this->dateFormat($last_month);
        $toyear     = mktime(0, 0, 0, 1, 0, date('Y'));
        $toyear1=$this->dateFormat($toyear);
        $last_year  = mktime(0, 0, 0, 1, 0, date('Y') - 1);
        $last_year1=$this->dateFormat($last_year);
        $data       = [];
        $data['CoinList'] = DB::table('miner_sapling')->where('is_disable', 0)->get();
        foreach ($data['CoinList'] as $v) {
            $whereIn[] = $v->id;
        }
        /**
         * 赠送统计
         */
        $this->handleStatis(DB::table('miner_user_sapling')->where('type',5)->where('create_time', '>=', $today1)
            ->whereIn('sapling_id', $whereIn)->select('sapling_id', 'type')->get(), 'miner_user_sapling', 'today');

        $this->handleStatis(DB::table('miner_user_sapling')->where('type',5)->whereBetween('create_time', [$last_day1, $today1])
            ->whereIn('sapling_id', $whereIn)->select('sapling_id', 'type')->get(), 'miner_user_sapling', 'lastday');

        $this->handleStatis(DB::table('miner_user_sapling')->where('type',5)->where('create_time', '>=', $tomonth1)
            ->whereIn('sapling_id', $whereIn)->select('sapling_id', 'type')->get(), 'miner_user_sapling', 'tomonth');

        $this->handleStatis(DB::table('miner_user_sapling')->where('type',5)->whereBetween('create_time', [$last_month1, $tomonth1])
            ->whereIn('sapling_id', $whereIn)->select('sapling_id', 'type')->get(), 'miner_user_sapling', 'lastmonth');

        $this->handleStatis(DB::table('miner_user_sapling')->where('type',5)->where('create_time', '>=', $toyear1)
            ->whereIn('sapling_id', $whereIn)->select('sapling_id', 'type')->get(), 'miner_user_sapling', 'toyear');

        $this->handleStatis(DB::table('miner_user_sapling')->where('type',5)->whereBetween('create_time', [$last_year1, $toyear1])
            ->whereIn('sapling_id', $whereIn)->select('sapling_id', 'type')->get(), 'miner_user_sapling', 'lastyear');

        $this->handleStatis(DB::table('miner_user_sapling')->where('type',5)
            ->whereIn('sapling_id', $whereIn)->select('sapling_id', 'type')->get(), 'miner_user_sapling', 'all');
        $data['List'] = $this->List;
        foreach ($data['CoinList'] as $k => $v) {
            if (!key_exists($v->id, $data['List'])) $data['List'][$v->id] = [];
        }
        foreach ($data['List'] as $k => $v) {
            if (!key_exists('miner_user_sapling', $v)) $data['List'][$k]['miner_user_sapling'] = [];
        }
        return $data;
    }

    private function handleStatis($list, $listName, $dateName)
    {
        error_reporting(0);
        if (empty($list) || count($list) < 1) return;
        foreach ($list as $k => $v) {
            $this->List[$v->sapling_id][$listName][$dateName]['Total'] += 1;
            $this->List[$v->sapling_id][$listName][$dateName]['Total'] = $this->List[$v->sapling_id][$listName][$dateName]['Total'] ?? 0;
        }
    }

}
