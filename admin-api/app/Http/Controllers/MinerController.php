<?php

namespace App\Http\Controllers;
use App\Models\DogListModel;
use App\Models\FlowerFieldModel;
use App\Models\MouseModel;
use App\Models\SaplingPackageModel;
use App\Models\UserDogModel;
use App\Models\UserSaplingModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MinerController extends Controller
{

    //环保等级
    public function List(Request $request)
    {
        $where = [];
        $count = intval($request->input('limit', 20));
        $Level = DB::table("miner_level")
            ->where($where)
            ->paginate($count);
        foreach ($Level as &$item){
            $rule=json_decode($item->rule,true);
            $reward=json_decode($item->reward,true);
            if(is_numeric($reward['sapling_id'])){
          $res=DB::table('miner_sapling')->where('id',$reward['sapling_id'])->first();
              $item->sapling_id=$res->nickname;//树苗名称
            }
                $item->level_id=$rule['level_id'];//等级
                $item->direct_push=$rule['direct_push'];//直推人数
                $item->algebra=$rule['algebra'];//代数
                $item->computing_power=$rule['computing_power'];//亩数
        }
        $SaplingList = DB::table("miner_sapling")
            ->select("id", "nickname")
            ->get();
        $res = [];
        $res["total"] = $Level->total();
        $res["list"] = $Level->items();
        $res["SaplingList"] = $SaplingList;
        return self::returnMsg($res);
    }
    //修改
    public function miner_levelEdit(Request $request){
        $data = $request->post();
//        dd($data);
        $id = !empty($data["id"]) ? intval($data["id"]) : 0;
        if (empty($id)) {
            return self::returnMsg([],'请选择需要操作的记录',20001);
        }
        $is_pop_up = !empty($data["is_pop_up"]) ? $data["is_pop_up"] : 0;
        $is_disable = !empty($data["is_disable"]) ? $data["is_disable"] : 0;
        $icon = !empty($data["icon"]) ? $data["icon"] : 0;
        if (empty($data["nickname"])){
            return self::returnMsg([],'请输入名称',20001);
        }
        if (empty($data["icon"])){
            return self::returnMsg([],'请上传图标',20001);
        }
//        if (empty($data["sapling_id"])){
//            return self::returnMsg([],'请选择奖励树苗',20001);
//        }
//        if (!is_numeric($data["dividend_ratio"]) || $data['dividend_ratio']<0){
//            return self::returnMsg([],'请输入分红比例且分红比例需大于0',20001);
//        }
        if (!is_numeric($data["direct_push"]) || $data['direct_push']<0){
            return self::returnMsg([],'请输入直推人数且直推人数需大于0',20001);
        }
        if (!is_numeric($data["algebra"]) || $data['algebra']<0){
            return self::returnMsg([],'请输入代数且代数需大于0',20001);
        }
        $Id=DB::table('miner_level')->max('id');
        if (!is_numeric($data["level_id"]) || $data['level_id']<0){
            return self::returnMsg([],'请输入等级且等级需大于0',20001);
        }
        if ($data['level_id']>$Id ){
            return self::returnMsg([],'当前输入的等级不存在',20001);
        }
         if (!is_numeric($data["computing_power"]) || $data['computing_power']<0){
             return self::returnMsg([],'请输入亩数需大于0',20001);
         }
//        if (empty($data["reward"])){
//            return self::returnMsg([],'请输入奖励规则',20001);
//        }
        if (!empty($data['sapling_id'])){
            $sapling=DB::table('miner_sapling')->where('nickname',$data['sapling_id'])->first();
            $rewards=[
                'sapling_id'=>$sapling->id,
            ];

        $reward=json_encode($rewards,true);


        $level = [
            'direct_push'=>$data['direct_push'],
            'algebra'=>$data['algebra'],
            'level_id'=>$data['level_id'],
            'computing_power'=>$data['computing_power'],
        ];
        $rules=json_encode($level,true);
        $datas=[
            'is_pop_up'=>$is_pop_up,
            'is_disable'=>$is_disable,
            'nickname'=>$data['nickname'],
            'icon'=>$icon,
            'rule'=>$rules,
            'dividend_ratio'=>$data['dividend_ratio'],
            'reward'=>$reward,
            'is_audit'=>$data['is_audit'],
            'update_time'=>date('Y-m-d H:i:s'),
        ];
            DB::table("miner_level")->where("Id", $id)->update($datas);

        return self::returnMsg();
        }else{

            $level = [
                'direct_push'=>$data['direct_push'],
                'algebra'=>$data['algebra'],
                'level_id'=>$data['level_id'],
                'computing_power'=>$data['computing_power'],
            ];
            $rules=json_encode($level,true);
            $datas=[
                'is_pop_up'=>$is_pop_up,
                'is_disable'=>$is_disable,
                'nickname'=>$data['nickname'],
                'icon'=>$icon,
                'rule'=>$rules,
                'dividend_ratio'=>$data['dividend_ratio'],
                'reward'=>null,
                'is_audit'=>$data['is_audit'],
                'update_time'=>date('Y-m-d H:i:s'),
            ];
            DB::table("miner_level")->where("Id", $id)->update($datas);

            return self::returnMsg();
        }
    }
    //删除
    public function miner_levelDel(Request $request){
        $data = $request->post();
        $id = !empty($data["id"]) ? intval($data["id"]) : 0;
        if (empty($id)) {
            return self::returnMsg([],'请选择需要操作的记录',20001);
        }
        $data=[
            "is_delete"=>1,
            'delete_time'=>date('Y-m-d H:i:s'),
        ];
        DB::table("miner_level")->where("id", $id)->update($data);
        return self::returnMsg();
    }
//    //添加环保等级
//    public function miner_levelAdd(Request $request){
//        $data = $request->post();
//        $level=$this->level();
//        $is_pop_up = !empty($data["is_pop_up"]) ? $data["is_pop_up"] : 0;
//        $is_disable = !empty($data["is_disable"]) ? $data["is_disable"] : 0;
//        $icon = !empty($data["icon"]) ? $data["icon"] : 0;
//        $is_delete = !empty($data["is_delete"]) ? $data["is_delete"] : 0;
////            $level=intval($request->input('level'));//等级
//        if (empty($data["nickname"])){
//            return self::returnMsg([],'请输入名称',20001);
//        }
//        if (empty($data["icon"])){
//            return self::returnMsg([],'请上传图标',20001);
//        }
//        if (empty($data["rule"])){
//            return self::returnMsg([],'请输入升级规则',20001);
//        }
//        if (empty($data["dividend_ratio"])){
//            return self::returnMsg([],'请输入分红比例',20001);
//        }
//        if (empty($data["reward"])){
//            return self::returnMsg([],'请输入奖励规则',20001);
//        }
//        $data = [
//            'is_pop_up'=>$is_pop_up,
//            'is_disable'=>$is_disable,
//            'nickname'=>$data['nickname'],
//            'icon'=>$icon,
//            'rule'=>$data['rule'],
//            'dividend_ratio'=>$data['dividend_ratio'],
//            'reward'=>$data['dividend_ratio'],
//            'level'=>$level,
//            'is_delete'=>$is_delete,
//            'create_time'=>date('Y-m-d H:i:s'),
//        ];
//        $Id = DB::table("miner_level")->insertGetId($data);
//        return self::returnMsg(["id" => $Id]);
//    }
//    public function level(){
//        $level = DB::table('miner_level')->max('level');
//        $levels=$level+1;
//        return $levels;
//    }
    //花田
    public function miner_saplingList(Request $request){
        $where = [];
        $nickname = $request->input('nickname');
        if (!empty($nickname)) {
            $where[] = ["nickname", "like", "%$nickname%"];
        }
        $count = intval($request->input('limit', 20));
        $lists = FlowerFieldModel::GetList($count, $where);
        foreach ($lists as $v){
            $v->iconUrl = !empty($v->icon) ? $this->config["Domain"] . $v->icon : "";
            $v->EnName = DB::table('coin')->where('Id',$v->coin_id)->value('EnName');
        }
        $coinList = DB::table('coin')->select('Id','EnName')->get();
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        $res["coinList"] = $coinList;
        return self::returnMsg($res);
    }
//    //花田配置
//    public function SettingMinerSapling(Request $request){
//        $data=$request->post();
//        dd($data);
//        if (empty($data['basie_event'])) return self::returnMsg([],'请选择基础时间',20001);
//        if (!is_numeric($data['delay_period'])) return self::returnMsg([],'延迟周期天数输入格式有误',20001);
//        if (($data['delay_period'])<0) return self::returnMsg([],'延迟周期天数需大于0',20001);
//        $data1=[
//          'v'=>$data['basie_event']
//        ];
//        $data2=[
//            'v'=>$data['delay_period']
//        ];
//        DB::table('Setting')->where('k','basie_event')->update($data1);
//        DB::table('Setting')->where('k','delay_period')->update($data2);
//        return self::returnMsg([],'操作成功！',20000);
//
//
//    }
    //花田修改
    public function MinerSaplingEdit(Request $request){
        $data = $request->post();
        $Id = !empty($data["id"]) ? $data["id"] : 0;
        if (empty($Id)) {
            return self::returnMsg([],'请选择需要操作的记录',20001);
        }

        if (empty($data['nickname'])) return self::returnMsg([],'请输入花田名称',20001);
        if (!is_numeric($data['price']) || $data['price']<0) return self::returnMsg([],'请输入花田售价且树苗售价需大于0',20001);
        if (!is_numeric($data['rate_of_return']) || $data['rate_of_return']<0) return self::returnMsg([],'请输入花田收益率且树苗收益需大于0',20001);
        $total_profit=bcadd(bcmul($data['price'],$data['rate_of_return']),$data['price']);
        $yield= bcdiv($total_profit,$data['cycle'],4);
//        if (!is_numeric($data['yield']) || $data['yield']<0) return self::returnMsg([],'请输入树苗日产量且树苗日产量需大于0',20001);
        if (!is_numeric($data['cycle']) || $data['cycle']<0) return self::returnMsg([],'请输入花田生产周期且树苗生产周期需大于0',20001);
        if (!is_numeric($data['is_disable'])) return self::returnMsg([],'请选择是否禁用',20001);
        if (!is_numeric($data['is_experience'])) return self::returnMsg([],'请选择是否属于体验',20001);
        if (empty($data['coin_id'])) return self::returnMsg([],'请选择支付币种',20001);
        if (!is_numeric($data['max_hold']) || $data['max_hold']<0) return self::returnMsg([],'请输入花田最大持有量且树苗最大持有量需大于0',20001);
//        if (!is_numeric($data['extend_cycle'])) return self::returnMsg([],'请输入树苗延迟周期',20001);
        if (empty($data['explanation'])) return self::returnMsg([],'请输入花田说明',20001);
        if (!is_numeric($data['computing_power']) || $data['computing_power']<0) return self::returnMsg([],'请输入亩数',20001);
        if (empty($data['icon'])) return self::returnMsg([],'请上传图标',20001);
        if (!is_numeric($data['is_shop_sapling'])) return self::returnMsg([],'请选择是否为拼购土地',20001);
        if (!is_numeric($data['recommend_price'])) return self::returnMsg([],'请输入推荐价格',20001);
        if (!is_numeric($data['sort'])) return self::returnMsg([],'请输入排序',20001);
        $data=[
            'nickname'=>$data['nickname'],
            'price'=>$data['price'],
            'rate_of_return'=>$data['rate_of_return'],
            'cycle'=>$data['cycle'],
            'is_disable'=>$data['is_disable'],
            'is_experience'=>$data['is_experience'],
            'explanation'=>$data['explanation'],
            'max_hold'=>$data['max_hold'],
            'icon'=>$data['icon'],
            'total_profit'=>$total_profit,
            'computing_power' =>$data['computing_power'],
            'yield'=>$yield,
            'coin_id'=>$data['coin_id'],
            'is_spare'=>$data['is_spare'],
            'is_shop_sapling'=>$data['is_shop_sapling'],
            'recommend_price'=>$data['recommend_price'],
            'sort'=>(int)$data['sort'],
            'update_time'=>date('Y-m-d H:i:s'),
        ];
//        $datas=[
//            'v'=>$data['extend_cycle'],
//        ];
//        DB::table('Setting')->where('k','delay_period')->update($datas);
        DB::table('miner_sapling')->where('id',$Id)->update($data);
      return self::returnMsg();

    }
    //树苗类型
    public function miner_saplingType(Request $request){
        $where = [];
        $count = intval($request->input('limit', 20));
        $NickName = $request->input('nickname');
        if (!empty($NickName)) {
            $where[] = ["nickname", "like", "%$NickName%"];
        }
        $lists = DB::table("miner_sapling_type")
            ->where($where)
            ->orderBy("id", "DESC")
            ->paginate($count);
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    //老鼠列表
    public function sapling_Package(Request $request){
        $where = [];
        $count = intval($request->input('limit', 20));
        $NickName = $request->input('nickname');
        if (!empty($NickName)) {
            $where[] = ["nickname", "like", "%$NickName%"];
        }
        $lists =MouseModel::GetList($count, $where);
        foreach ($lists as $v){
            $v->iconUrl = !empty($v->icon) ? $this->config["Domain"] . $v->icon : "";
            $v->create_time=date('Y-m-d H:i:s',$v->create_time);
            $v->delete_time=date('Y-m-d H:i:s',$v->delete_time);
            $v->update_time=date('Y-m-d H:i:s',$v->update_time);
            $v->EnName = DB::table('coin')->where('Id',$v->coin_id)->value('EnName');
        }
        $coinList = DB::table('coin')->select('Id','EnName')->get();
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        $res["coinList"] = $coinList;
        return self::returnMsg($res);
    }


//    //删除矿机机器人
//    public function sapling_packageDel(Request $request){
//        $data = $request->post();
//        $id=$data['id'];
//        if(empty($id)){
//            return self::returnMsg([], '请选择需要操作的记录', 20001);
//        }
//        SaplingPackageModel::DelById($id);
//        return self::returnMsg();
//    }
//    public function sapling_packageAdd(Request $request){
//        $data = $request->post();
//        if (empty($data['nickname'])){
//            return self::returnMsg([], '请输入套餐名称', 20001);
//        }
//        if (empty($data['cycle'])) {
//            return self::returnMsg([], '请输入持续时间', 20001);
//        }
//        if (empty($data['price'])) {
//            return self::returnMsg([], '请输入售价', 20001);
//        }
//        if (empty($data['daily_average'])) {
//            return self::returnMsg([], '请输入日均价格', 20001);
//        }
//        $is_disable = !empty($data["is_disable"]) ? $data["is_disable"] : 0;
//        $is_delete = !empty($data["is_delete"]) ? $data["is_delete"] : 0;
//        $data = [
//            "nickname" => $data["nickname"],
//            "cycle" => $data['cycle'],
//            "price" => $data['price'],
//            "daily_average" => $data['daily_average'],
//            "is_disable" => $is_disable,
//            "is_delete" => $is_delete,
//        ];
//        DB::table("miner_sapling_package")->insertGetId($data);
//
//        return self::returnMsg();
//    }
//修改老鼠
    public function sapling_packageEdit(Request $request){
        $data = $request->post();
        $Id=$data['id'];
        if (empty($Id)){
            return self::returnMsg([],'参数错误！');
        }
        if (empty($data['nickname'])) return self::returnMsg([], '请输入老鼠名称', 20001);
        if (!is_numeric($data['price']) || $data['price']<0) return self::returnMsg([], '请输入售价', 20001);
        if (!is_numeric($data['max_frequency']) || $data['max_frequency']<0) return self::returnMsg([], '请输入最大偷取次数', 20001);
//        if (!is_numeric($data['is_healing'])) return self::returnMsg([], '请选择是否需要疗伤', 20001);
        if ($data['is_healing'] == 1){
        if (empty($data['healing_time'])) return self::returnMsg([], '请输入疗伤时间', 20001);
        }
        $healing_time= !empty($data["healing_time"]) ? $data["healing_time"] : 0;
        if (!is_numeric($data['min_steal']) || $data['min_steal']<0 || $data['min_steal']>1) return self::returnMsg([], '请输入最小偷取率且在0-1之间', 20001);
        if (!is_numeric($data['max_steal']) || $data['max_steal']<0 || $data['max_steal']>1) return self::returnMsg([], '请输入最大偷取率且在0-1之间', 20001);
        if ($data['max_steal'] < $data['min_steal']) return self::returnMsg([], '最小偷取率应小于最大偷取率', 20001);
        if (empty($data['explanation'])) return self::returnMsg([], '请输入说明', 20001);
        if (empty($data['coin_id'])) return self::returnMsg([], '请选择支付币种', 20001);
//        if (!is_numeric($data['is_experience'])) return self::returnMsg([], '请选择是否属于体验', 20001);
        if (!is_numeric($data['is_disable'])) return self::returnMsg([], '请选择是否禁用', 20001);
        if (empty($data['icon'])) return self::returnMsg([], '请上传图标', 20001);

        $data = [
            "nickname" => $data["nickname"],
            "price" => $data['price'],
            "max_frequency" => $data['max_frequency'],
//            "is_healing" => 0,
            "healing_time" => $data['healing_time'],
            "min_steal" => $data['min_steal'],
            "max_steal" => $data['max_steal'],
            "explanation" => $data['explanation'],
//            "is_experience" => $data['is_experience'],
            "is_disable" => $data['is_disable'],
            "icon" => $data['icon'],
            "coin_id" => $data['coin_id'],
            "update_time"=> time(),
        ];
        DB::table("mouse_list")->where('id',$Id)->update($data);

        return self::returnMsg();
    }
    //狗
    public function DogList(){
        $lists=DogListModel::GetPageList();
        foreach ($lists as $v){
            $v->iconUrl = !empty($v->icon) ? $this->config["Domain"] . $v->icon : "";
            $v->create_time=date('Y-m-d H:i:s',$v->create_time);
            $v->delete_time=date('Y-m-d H:i:s',$v->delete_time);
            $v->update_time=date('Y-m-d H:i:s',$v->update_time);
            $v->EnName = DB::table('coin')->where('Id',$v->coin_id)->value('EnName');
        }
        $coinList = DB::table('coin')->select('Id','EnName')->get();
        $res = [];
        $res["lists"] = $lists;
        $res["coinList"] = $coinList;
        return self::returnMsg($res);
    }
//修改狗
    public function DogEdit(Request $request){
        $data = $request->post();
        $Id=$data['id'];
        if (empty($Id)){
            return self::returnMsg([],'参数错误！');
        }
        if (empty($data['nickname'])) return self::returnMsg([], '请输入小狗名称', 20001);
        if (!is_numeric($data['price']) || $data['price']<0) return self::returnMsg([], '请输入售价', 20001);
        if (!is_numeric($data['max_defense_count']) || $data['max_defense_count']<0) return self::returnMsg([], '请输入最大防御次数', 20001);

        if (!is_numeric($data['is_defense_interval'])) return self::returnMsg([], '请选择站岗后是否需要休息', 20001);
        if ($data['is_defense_interval'] == 1){
        if (!is_numeric($data['defense_interval_time'])) return self::returnMsg([], '请输入站岗后休息时间', 20001);
        }
        $defense_interval_time= !empty($data["defense_interval_time"]) ? $data["defense_interval_time"] : 0;
        if (!is_numeric($data['stand_guard_time_ltt'])) return self::returnMsg([], '请输入站岗有效时间（分钟）', 20001);
        if (!is_numeric($data['max_hold'])) return self::returnMsg([], '请输入最大持有量', 20001);

        if (!is_numeric($data['min_defense']) || $data['min_defense']<0 || $data['min_defense']>1) return self::returnMsg([], '请输入最小防御比例且在0-1之间', 20001);
        if (!is_numeric($data['max_defense']) || $data['max_defense']<0 || $data['max_defense']>1) return self::returnMsg([], '请输入最大防御比例且在0-1之间', 20001);
        if ($data['max_defense'] < $data['min_defense']) return self::returnMsg([], '最小防御比例应小于最大防御比例', 20001);
        if (empty($data['explanation'])) return self::returnMsg([], '请输入说明', 20001);
        if (empty($data['coin_id'])) return self::returnMsg([], '请选择支付币种', 20001);
//        if (!is_numeric($data['is_experience'])) return self::returnMsg([], '请选择是否属于体验', 20001);
        if (!is_numeric($data['is_disable'])) return self::returnMsg([], '请选择是否禁用', 20001);
        if (empty($data['icon'])) return self::returnMsg([], '请上传图标', 20001);

        $data = [
            "nickname" => $data["nickname"],
            "price" => $data['price'],
            "max_defense_count" => $data['max_defense_count'],
            "is_defense_interval" => $data['is_defense_interval'],
            "defense_interval_time" => $defense_interval_time,
            "stand_guard_time_ltt" => $data['stand_guard_time_ltt'],
            "max_hold" => $data['max_hold'],
            "min_defense" => $data['min_defense'],
            "max_defense" => $data['max_defense'],
            "explanation" => $data['explanation'],
            "is_disable" => $data['is_disable'],
            "coin_id" => $data['coin_id'],
            "icon" => $data['icon'],
            "update_time"=> time(),
        ];
        DB::table("dog_list")->where('id',$Id)->update($data);

        return self::returnMsg();
    }

    //用户拥有的狗
    public function UserDogList(Request $request){
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
        $lists = DB::table("dog_user")
            ->leftjoin('members as m','m.Id','=','dog_user.user_id')
            ->leftjoin('dog_list as p','p.id','=','dog_user.dog_list_id')
            ->where($where)
            ->select('dog_user.*','m.NickName','m.Phone','p.nickname')
            ->orderBy('id','desc')
            ->paginate($count);
        foreach ($lists as $v){
            $v->create_time=date('Y-m-d H:i:s',$v->create_time);
            $v->delete_time=date('Y-m-d H:i:s',$v->delete_time);
            $v->update_time=date('Y-m-d H:i:s',$v->update_time);
            $v->defense_time_ltt=date('Y-m-d H:i:s',$v->defense_time_ltt);
            if ($v->defense_time_ltt=='1970-01-01 08:00:00'){
                $v->defense_time_ltt='';
            }
        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    //删除用户拥有的狗
    public function UserDogDel(Request $request){
        $data = $request->post();
        $id=$data['id'];
        if(empty($id)){
            return self::returnMsg([], '请选择需要操作的记录', 20001);
        }
        UserDogModel::DelById($id);
        return self::returnMsg();
    }






    //分享奖励规则
    public function share_reward(Request $request){
        $where = [];
        $count = intval($request->input('limit', 20));
        $lists = DB::table("miner_sapling_share_reward")
            ->where($where)
            ->orderBy("id", "DESC")
            ->paginate($count);
        foreach ($lists as &$v){
            $arr=json_decode($v->reward);
            if (!empty($arr->miner_sapling_id)){
            $v->number=$arr->number;
            $v->sapling_id=$arr->miner_sapling_id;
            $sapling_name= DB::table('miner_sapling')->where('id',$arr->miner_sapling_id)->select('nickname')->first();
            $v->nicknames=$sapling_name->nickname;
            }
            unset($v->reward);
        }
            $SaplingList = DB::table("miner_sapling")
                ->select("id", "nickname")
                ->get();
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        $res["SaplingList"] = $SaplingList;
        return self::returnMsg($res);
    }
    //编辑分享奖励规则
    public function share_rewardEdit(Request $request){

        $data = $request->post();
//        dd($data);
        $nicknames=$data['nicknames'];
//        dd($miner_sapling_id);
        $res=DB::table('miner_sapling')->where('nickname',$nicknames)->first();
        $miner_sapling_id=$res->id;
//        if(empty($miner_sapling_id)){
//            return self::returnMsg([], '请选择奖励树苗', 20001);
//        }
//        dd($miner_sapling_id);
        $Id = !empty($data["id"]) ? intval($data["id"]) : 0;
        if (empty($Id)) {
            return self::returnMsg([], '请选择需要操作的记录', 20001);
        }
        if (empty($data['direct_push'])) {
            return self::returnMsg([], '请输入直推人数', 20001);
        }
        $computing_power = !empty($data["computing_power"]) ? ($data["computing_power"]) : 0;
        $arr=[
            'miner_sapling_id'=>$miner_sapling_id,
            'number'=>$data['number'],
        ];
        $reward =json_encode($arr);
        $data = [
            "direct_push" => $data["direct_push"],
            "is_simultaneously" => $data['is_simultaneously'],
            "is_disable" => $data['is_disable'],
            "is_delete" => $data['is_delete'],
            "computing_power" => $computing_power,
            "reward" => $reward,
            "update_time" => date("Y-m-d H:i:s"),
        ];


        DB::table("miner_sapling_share_reward")->where("id", $Id)->update($data);

        return self::returnMsg();
    }
    //添加分享奖励规则
    public function share_rewardAdd(Request $request){

        $data = $request->post();

        $nicknames=$data['nicknames'];
//        dd($miner_sapling_id);
        $res=DB::table('miner_sapling')->where('nickname',$nicknames)->first();
        $miner_sapling_id=$res->id;
//        if(empty($miner_sapling_id)){
//            return self::returnMsg([], '请选择奖励树苗', 20001);
//        }
//        dd($miner_sapling_id);
        if (empty($data['direct_push'])) {
            return self::returnMsg([], '请输入直推人数', 20001);
        }
        if (empty($data['direct_push'])) {
            return self::returnMsg([], '请输入', 20001);
        }
        if (empty($data['direct_push'])) {
            return self::returnMsg([], '请输入直推人数', 20001);
        }
        $computing_power = !empty($data["computing_power"]) ? ($data["computing_power"]) : 0;
        $arr=[
            'miner_sapling_id'=>$miner_sapling_id,
            'number'=>$data['number'],
        ];
        $reward =json_encode($arr);
        $data = [
            "direct_push" => $data["direct_push"],
            "is_simultaneously" => 1,
            "is_disable" => $data['is_disable'],
            "is_delete" => $data['is_delete'],
            "computing_power" => $computing_power,
            "reward" => $reward,
        ];
        DB::table("miner_sapling_share_reward")->insertGetId($data);

        return self::returnMsg();
    }
    //删除分享奖励规则
    public function share_rewardDel(Request $request){

        $data = $request->post();
        $Id = !empty($data["id"]) ? intval($data["id"]) : 0;
        if (empty($Id)) {
            return self::returnMsg([], '请选择需要操作的记录', 20001);
        }
        $data = [
            "is_delete" => 1,
            "delete_time" => date("Y-m-d H:i:s"),
        ];
        DB::table("miner_sapling_share_reward")->where("id", $Id)->update($data);
        return self::returnMsg();
    }

    //用户花田释放记录
    public function user_sapling_release(Request $request)
    {
        $where = [];
        $count = intval($request->input('limit', 20));
        $Phone = $request->input('Phone');
        if (!empty($Phone)) {
            $where[] = ["m.Phone", "like", "%$Phone%"];
        }
        $nickname = $request->input('nickname');
        if (!empty($nickname)) {
            $where[] = ["s.nickname", "like", "%$nickname%"];
        }
        $NickName = $request->input('NickName');
        if (!empty($NickName)) {
            $where[] = ["m.Nickname", "like", "%$NickName%"];
        }
        $user_sapling_id = $request->input('user_sapling_id');
        if (!empty($user_sapling_id)) {
            $where[] = ["user_sapling_id", "like", "%$user_sapling_id%"];
        }
        $lists = DB::table("miner_user_sapling_total_release")
            ->leftjoin('members as m', 'm.Id', '=', 'miner_user_sapling_total_release.user_id')
//            ->Leftjoin('miner_sapling as s', 's.id', '=', 'miner_user_sapling_total_release.sapling_id')
            ->where($where)
            ->select('miner_user_sapling_total_release.*','m.NickName', 'm.Phone')
            ->orderBy('id', 'desc')
            ->paginate($count);
        foreach ($lists as $key => &$item) {
            $item->begin_receive_time=date("Y-m-d H:i:s",$item->begin_receive_time);
            $item->issue_time=date("Y-m-d H:i:s",$item->issue_time);
            $item->create_time=date("Y-m-d H:i:s",$item->create_time);
            $item->update_time=date("Y-m-d H:i:s",$item->update_time);
        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);

    }
    //用户拥有的花田
    public function miner_user_sapling(Request $request){
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
        $lists = UserSaplingModel::GetList($count,$where);
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    public function user_saplingEdit(Request $request){
        $data=$request->post();
        $id=$data['id'];
        if(empty($id)){
            return self::returnMsg([], '请选择需要操作的记录', 20001);
        }
        UserSaplingModel::DelById($id);
        return self::returnMsg();
//        dd($data);
    }
    //用户树苗收益可领取表
    public function user_sapling_receive(Request $request){
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
        $user_sapling_id = $request->input('user_sapling_id');
        if (!empty($user_sapling_id)) {
            $where[] = ["user_sapling_id", "like", "%$user_sapling_id%"];
        }
        $user_day_sapling_id = $request->input('user_day_sapling_id');
        if (!empty($user_day_sapling_id)) {
            $where[] = ["user_day_sapling_id", "like", "%$user_day_sapling_id%"];
        }
        $lists = DB::table("miner_user_sapling_receive")
            ->leftjoin('Members as m','m.Id','=','miner_user_sapling_receive.user_id')
            ->where($where)
            ->select('miner_user_sapling_receive.*','m.NickName','m.Phone')
            ->orderBy('id','desc')
            ->paginate($count);
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    //分红汇总
    public function miner_dividend(Request $request){
        $where = [];
        $count = intval($request->input('limit', 20));
        $lists = DB::table("miner_dividend")
            ->where($where)
            ->orderBy("id", "DESC")
            ->paginate($count);
//        dd($lists);
        foreach ($lists as &$item) {
            $res = json_decode($item->rule, true);
            $item->level1 = $res['miner_level_1'];
            $item->level2 = $res['miner_level_2'];
            $item->level3 = $res['miner_level_3'];
            $item->level4 = $res['miner_level_4'];
            $item->level5 = $res['miner_level_5'];
            $item->level6 = $res['miner_level_6'];
            $arr = json_decode($item->level_dividend_json, true);
            $item->level_dividend = $arr;
            $item->LevelName1=DB::table('miner_level')->where('id',1)->value('nickname');
            $item->LevelName2=DB::table('miner_level')->where('id',2)->value('nickname');
            $item->LevelName3=DB::table('miner_level')->where('id',3)->value('nickname');
            $item->LevelName4=DB::table('miner_level')->where('id',4)->value('nickname');
            $item->LevelName5=DB::table('miner_level')->where('id',5)->value('nickname');
            $item->LevelName6=DB::table('miner_level')->where('id',6)->value('nickname');
            unset($item->level_dividend_json);
        }

        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    //分红配置
    public function miner_dividendEdit(Request $request){

        $data=$request->post();

        $Id = !empty($data["id"]) ? intval($data["id"]) : 0;
        if (empty($Id)) {
            return self::returnMsg([],'请选择需要操作的记录',20001);
        }
        $begin_dividend_time=$data['begin_dividend_time'];
        $time=date("Y-m-d H:i:s");
//        $times=date("Y-m-d H:i:s",strtotime("+7days",strtotime($time)));
        if ($begin_dividend_time<$time){
            return self::returnMsg([],'分红开始时间应大于当前时间',20001);
        }
//        if ($begin_dividend_time<$times){
//            return self::returnMsg([],'分红每周开始一次',20001);
//        }
        if (!is_numeric($data['level1']) || $data['level1']<0 || $data['level1']>1) return self::returnMsg([],'分红规则比例填写错误',20001);
        if (!is_numeric($data['level2']) || $data['level2']<0 || $data['level2']>1) return self::returnMsg([],'分红规则比例填写错误',20001);
        if (!is_numeric($data['level3']) || $data['level3']<0 || $data['level3']>1) return self::returnMsg([],'分红规则比例填写错误',20001);
        if (!is_numeric($data['level4']) || $data['level4']<0 || $data['level4']>1) return self::returnMsg([],'分红规则比例填写错误',20001);
        if (!is_numeric($data['level5']) || $data['level5']<0 || $data['level5']>1) return self::returnMsg([],'分红规则比例填写错误',20001);
        if (!is_numeric($data['level6']) || $data['level6']<0 || $data['level6']>1) return self::returnMsg([],'分红规则比例填写错误',20001);
        if (!is_numeric($data['amount']) || $data['amount']<0 ) return self::returnMsg([],'请填写分红金额',20001);

        $level1 = !empty($data["level1"]) ? (string)$data["level1"] : 0;
        $level2 = !empty($data["level2"]) ? (string)($data["level2"]) : 0;
//        dd($level2);
        $level3 = !empty($data["level3"]) ? (string)$data["level3"] : 0;
        $level4 = !empty($data["level4"]) ? (string)$data["level4"] : 0;
        $level5 = !empty($data["level5"]) ? (string)$data["level5"] : 0;
        $level6 = !empty($data["level6"]) ? (string)$data["level6"] : 0;
        $arr = [
            'miner_level_1'=>$level1,
            'miner_level_2'=>$level2,
            'miner_level_3'=>$level3,
            'miner_level_4'=>$level4,
            'miner_level_5'=>$level5,
            'miner_level_6'=>$level6,
        ];
        $rule =json_encode($arr);
        $datas=[
            'amount'=>$data['amount'],
            'rule'=>$rule,
            'update_time'=>$time,
            'begin_dividend_time'=>$begin_dividend_time,
        ];
         DB::table('miner_dividend')->where('id',$Id)->update($datas);
        return self::returnMsg();

    }
    //新增分红配置
    public function miner_dividendAdd(Request $request){
        $data=$request->post();
//        dd($data);
        $begin_dividend_time=$data['begin_dividend_time'];
        $time=date("Y-m-d H:i:s");
//        $times=date("Y-m-d H:i:s",strtotime("+7days",strtotime($time)));
        if ($begin_dividend_time<$time){
            return self::returnMsg([],'分红开始时间应大于当前时间',20001);
        }
//        if ($begin_dividend_time<$times){
//            return self::returnMsg([],'分红每周开始一次',20001);
//        }
        if (!is_numeric($data['level1']) || $data['level1']<0 || $data['level1']>1) return self::returnMsg([],'分红规则比例填写错误',20001);
        if (!is_numeric($data['level2']) || $data['level2']<0 || $data['level2']>1) return self::returnMsg([],'分红规则比例填写错误',20001);
        if (!is_numeric($data['level3']) || $data['level3']<0 || $data['level3']>1) return self::returnMsg([],'分红规则比例填写错误',20001);
        if (!is_numeric($data['level4']) || $data['level4']<0 || $data['level4']>1) return self::returnMsg([],'分红规则比例填写错误',20001);
        if (!is_numeric($data['level5']) || $data['level5']<0 || $data['level5']>1) return self::returnMsg([],'分红规则比例填写错误',20001);
        if (!is_numeric($data['level6']) || $data['level6']<0 || $data['level6']>1) return self::returnMsg([],'分红规则比例填写错误',20001);
        if (!is_numeric($data['amount']) || $data['amount']<0 ) return self::returnMsg([],'请填写分红金额',20001);

        $level1 = !empty($data["level1"]) ? (string)$data["level1"] : 0;
        $level2 = !empty($data["level2"]) ? (string)($data["level2"]) : 0;
//        dd($level2);
        $level3 = !empty($data["level3"]) ? (string)$data["level3"] : 0;
        $level4 = !empty($data["level4"]) ? (string)$data["level4"] : 0;
        $level5 = !empty($data["level5"]) ? (string)$data["level5"] : 0;
        $level6 = !empty($data["level6"]) ? (string)$data["level6"] : 0;
        $arr = [
            'miner_level_1'=>$level1,
            'miner_level_2'=>$level2,
            'miner_level_3'=>$level3,
            'miner_level_4'=>$level4,
            'miner_level_5'=>$level5,
            'miner_level_6'=>$level6,
        ];
        $rule =json_encode($arr);
        $datas=[
            'amount'=>$data['amount'],
            'rule'=>$rule,
            'create_time'=>$time,
            'begin_dividend_time'=>$begin_dividend_time,
        ];
        DB::table('miner_dividend')->insertGetId($datas);
        return self::returnMsg();
    }
}
