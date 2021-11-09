<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class MarketController extends Controller
{
//    商品列表
    public function goodsList(Request $request)
    {
        $where = [];
        $count = intval($request->input('limit', 20));
        $goods_name = $request->input('goods_name');
        if (!empty($goods_name)){
            $where[] = ["goods_name", "like", "%$goods_name%"];
        }
        $goods_state = $request->input('goods_state');
        if ($goods_state != '') {
            $where[] = ["goods_state", $goods_state];
        }
        $lists = DB::table("shop_good")
            ->orderBy('goods_id','desc')
            ->where($where)
            ->paginate($count);
        foreach ($lists as $v) {
            $v->ImgList = json_decode($v->carousel_img,true);
            $v->on_time = date('Y-m-d H:i:s',$v->on_time);
        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
//    添加商品
    public function GoodsAdd(Request $request){
        $data = $request->post();
        if (empty($data['goods_name'])) return self::returnMsg([],'请输入商品名称',20001);
        if (empty($data['market_price'])) return self::returnMsg([],'请输入市场价',20001);
        if (empty($data['original_img'])) return self::returnMsg([],'请上传商品主图',20001);
        if (empty($data['ImgList'])) return self::returnMsg([],'请上传商品轮播图',20001);
        $item = [
            'goods_name' => $data['goods_name'],
            'market_price' => $data['market_price'],
            'original_img' => $data['original_img'],
            'goods_content' => $data['goods_content'],
            'carousel_img' => json_encode($data['ImgList'],true),
            'is_on_sale' => 1,
            'prom_type' => 2,
            'goods_state' => 0,
            'on_time' => time()
        ];
        DB::table('shop_good')->insert($item);
        return self::returnMsg();
    }
//    修改商品
    public function GoodsEdit(Request $request){
        $data = $request->post();
        if (empty($data['goods_id'])) return self::returnMsg([],'参数错误',20001);
        if (empty($data['goods_name'])) return self::returnMsg([],'请输入商品名称',20001);
        if (empty($data['market_price'])) return self::returnMsg([],'请输入市场价',20001);
        if (empty($data['original_img'])) return self::returnMsg([],'请上传商品主图',20001);
        if (empty($data['ImgList'])) return self::returnMsg([],'请上传商品轮播图',20001);
        $item = [
            'goods_name' => $data['goods_name'],
            'market_price' => $data['market_price'],
            'original_img' => $data['original_img'],
            'goods_content' => $data['goods_content'],
            'carousel_img' => json_encode($data['ImgList'],true),
        ];
        DB::table('shop_good')->where('goods_id',$data['goods_id'])->update($item);
        return self::returnMsg();
    }
    //上架商品
    public function upShelf(Request $request){
        $data = $request->post();
        if (empty($data['goodsId'])) return self::returnMsg([],'商品参数错误',20001);
        $activity = DB::table('shop_team_activity')->where('goods_id',$data['goodsId'])->first();
        if (empty($activity)) return $info = ['info'=>1];
        DB::table('shop_good')->where('goods_id',$data['goodsId'])->update(['goods_state'=>1,'is_on_sale'=>1]);
        DB::table('shop_team_activity')->where('goods_id',$data['goodsId'])->update(['status'=>1]);
        return self::returnMsg();
    }
    //下架商品
    public function downShelf(Request $request){
        $data = $request->post();
        if (empty($data['goodsId'])) return self::returnMsg([],'商品参数错误',20001);
        DB::table('shop_good')->where('goods_id',$data['goodsId'])->update(['goods_state'=>2,'is_on_sale'=>0]);
        DB::table('shop_time_slot_activity')->where('good_id',$data['goodsId'])->update(['is_delete'=>1]);
        DB::table('shop_team_activity')->where('goods_id',$data['goodsId'])->update(['status'=>2]);
        return self::returnMsg();

    }
    //拼购列表
    public function PgGoods(Request $request)
    {
        $count = intval($request->input('limit', 20));
        $goods_name = $request->input('goods_name');
        if (!empty($goods_name)) {
            $goods = DB::table('shop_good')->where('goods_name', 'like', '%' . $goods_name . '%')->get();
            $goods_id = array_column(json_decode(json_encode($goods), true), 'goods_id');
            if ($goods_id !=='') {
                $lists = DB::table("shop_team_activity")
                    ->leftjoin('shop_good as s', 's.goods_id', '=', 'shop_team_activity.goods_id')
                    ->whereIn('shop_team_activity.goods_id', $goods_id)
                    ->select('shop_team_activity.*', 's.goods_name', 's.market_price','s.goods_state', 's.original_img')
                    ->orderBy('activity_id', 'desc')
                    ->paginate($count);
            }
        } else {
                $lists = DB::table("shop_team_activity")
                    ->leftjoin('shop_good as s', 's.goods_id', '=', 'shop_team_activity.goods_id')
                    ->select('shop_team_activity.*', 's.goods_name', 's.market_price','s.goods_state', 's.original_img')
                    ->orderBy('activity_id', 'desc')
                    ->paginate($count);
            }
            foreach ($lists as $v) {
                $v->payCoin = DB::table('coin')->where('Id', $v->coin_id)->value('EnName'); //支付币种
                $v->getCoin = DB::table('coin')->where('Id', $v->luck_coin_id)->value('EnName'); //中奖得到币种
                $v->create_time = date('Y-m-d H:i:s', $v->create_time);
            }
            $res = [];
            $res["total"] = $lists->total();
            $res["list"] = $lists->items();
            return self::returnMsg($res);
    }
    //获取商品列表
    public function getGoodsList(Request $request){
        $where = [];
        $count = intval($request->input('limit', 20));
        $goods = DB::table('shop_team_activity')->get();
        $goods_id = array_column(json_decode(json_encode($goods), true), 'goods_id');
        $keyword = $request->input('keyword');
        if (!empty($keyword)){
            $where[] = ["goods_name", "like", "%$keyword%"];
        }
        $lists = DB::table("shop_good")
            ->where($where)
            ->whereNotIn('goods_id',$goods_id)
            ->paginate($count);
        foreach ($lists as $v) {
            $v->ImgList = json_decode($v->carousel_img,true);
            $v->on_time = date('Y-m-d H:i:s',$v->on_time);
        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    //添加拼购商品
    public function PgAdd(Request $request){
        $data = $request->post();
        if (empty($data['act_name'])) return self::returnMsg([],'请输入活动口号',20001);
        if (empty($data['team_price'])) return self::returnMsg([],'请输入拼购价格',20001);
        if (empty($data['needer'])) return self::returnMsg([],'请输入成团人数',20001);
        if (empty($data['stock_limit'])) return self::returnMsg([],'请输入中奖人数',20001);
        if (empty($data['return_amount'])) return self::returnMsg([],'请输入未中返还金额',20001);
        if (empty($data['luck_amount'])) return self::returnMsg([],'请输入获得金额',20001);
        if (empty($data['coin_id'])) return self::returnMsg([],'请选择支付币种',20001);
        if (empty($data['luck_coin_id'])) return self::returnMsg([],'请选择奖励币种',20001);
        if (empty($data['goods_id'])) return self::returnMsg([],'请选择商品',20001);
        if (!is_numeric($data['store_count'])) return self::returnMsg([],'请输入商品库存',20001);
        $data['team_type'] = 2;
        $data['status'] = 2;
        $data['create_time'] = time();
        DB::table('shop_team_activity')->insert($data);
        return self::returnMsg([],'添加成功',20000);
    }
    //编辑拼购商品
    public function PgEdit(Request $request){
        $data = $request->post();
        if (empty($data['act_name'])) return self::returnMsg([],'请输入活动口号',20001);
        if (empty($data['activity_id'])) return self::returnMsg([],'参数错误',20001);
        if (empty($data['team_price'])) return self::returnMsg([],'请输入拼购价格',20001);
        if (empty($data['needer'])) return self::returnMsg([],'请输入成团人数',20001);
        if (empty($data['stock_limit'])) return self::returnMsg([],'请输入中奖人数',20001);
        if (empty($data['return_amount'])) return self::returnMsg([],'请输入未中返还金额',20001);
        if (empty($data['luck_amount'])) return self::returnMsg([],'请输入获得金额',20001);
        if (empty($data['coin_id'])) return self::returnMsg([],'请选择支付币种',20001);
        if (empty($data['luck_coin_id'])) return self::returnMsg([],'请选择奖励币种',20001);
        if (!is_numeric($data['store_count'])) return self::returnMsg([],'请输入商品库存',20001);
        unset($data['goods_name']);
        unset($data['market_price']);
        unset($data['goods_state']);
        unset($data['original_img']);
        unset($data['payCoin']);
        unset($data['getCoin']);
        unset($data['create_time']);
        $data['update_time'] = time();
        DB::table('shop_team_activity')->where('activity_id',$data['activity_id'])->update($data);
        return self::returnMsg([],'添加成功',20000);
    }
    //拼购活动列表
    public function activity(Request $request){
        $where = [];
        $count = intval($request->input('limit', 20));
        $title = $request->input('title');
        if (!empty($title)){
            $where[] = ["title", "like", "%$title%"];
        }
        $lists = DB::table("shop_people_activities")
            ->where($where)
            ->paginate($count);
        foreach ($lists as $v) {
//            status : 1进行中 2未开始 3 已结束
            if ($v->end_time > time() && $v->begin_time < time()) $v->status = '1';
            if ($v->begin_time > time()) $v->status = '2';
            if ($v->end_time < time() && $v->begin_time < time()) $v->status = '3';

            $v->begin_time = date('Y-m-d H:i:s', $v->begin_time);
            $v->end_time = date('Y-m-d H:i:s', $v->end_time);
        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    //添加拼购活动时选择活动商品 列表
    public function checkGoods(Request $request){
        $where = [];
        $count = intval($request->input('limit', 20));
        $goods = DB::table('shop_time_slot_activity')->get();
        $goods_ids = array_column(json_decode(json_encode($goods), true), 'good_id');
        $keyword = $request->input('keyword');
        if (!empty($keyword)){
        $goods = DB::table('shop_good')->where('goods_name', 'like', '%' . $keyword . '%')->get();
        $goods_id = array_column(json_decode(json_encode($goods), true), 'goods_id');
        if ($goods_id !==''){
            $lists = DB::table("shop_team_activity")
                ->where($where)
                ->whereIn('shop_team_activity.goods_id',$goods_id)
                ->leftjoin('shop_good as s', 's.goods_id', '=', 'shop_team_activity.goods_id')
                ->select('shop_team_activity.*', 's.goods_name', 's.market_price','s.goods_state', 's.original_img')
                ->orderBy('activity_id', 'desc')
                ->paginate($count);
        }
        }else{
        $lists = DB::table("shop_team_activity")
            ->where($where)
            ->leftjoin('shop_good as s', 's.goods_id', '=', 'shop_team_activity.goods_id')
            ->select('shop_team_activity.*', 's.goods_name', 's.market_price','s.goods_state', 's.original_img')
            ->orderBy('activity_id', 'desc')
            ->paginate($count);
        }
        foreach ($lists as $v) {
            $v->payCoin = DB::table('coin')->where('Id', $v->coin_id)->value('EnName'); //支付币种
            $v->getCoin = DB::table('coin')->where('Id', $v->luck_coin_id)->value('EnName'); //中奖得到币种
        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
  //判断所选时间内是否已经有活动
    public function checkTime(Request $request){
        $data = $request->post();
        if (!empty($data['starTime']) && !empty($data['endTime'])){
            $one = DB::table('shop_people_activities')->whereBetween('end_time',[strtotime($data['starTime']),strtotime($data['endTime'])])->first();
//            $two = DB::table('shop_people_activities')->where('begin_time','>',time())->where('begin_time','<',strtotime($data['endTime']))->where('')->first();
            $three = DB::table('shop_people_activities')->whereBetween('begin_time',[strtotime($data['starTime']),strtotime($data['endTime'])])->first();
            $item = [];
            if (empty($one) && empty($three)){
                $item['is_exist'] = 1; //所选择时间不存在活动
            }else{
                $item['is_exist'] = 2; //存在
            }
            return $item;
        }
    }
    //添加活动
    public function activityAdd(Request $request){
        $data = $request->post();
        if (empty($data['title'])) return self::returnMsg([],'请输入活动标题',20001);
        if (empty($data['begin_time'])) return self::returnMsg([],'请选择活动开始时间',20001);
        if (empty($data['end_time'])) return self::returnMsg([],'请选择活动结束时间',20001);
        if (empty($data['tableList'])) return self::returnMsg([],'请选择商品',20001);
        $item=[
            'title' => $data['title'],
            'begin_time' => strtotime($data['begin_time']),
            'end_time' => strtotime($data['end_time']),
            'create_time' => time()
        ];
        DB::table('shop_people_activities')->insert($item);
        $time_slot_id = DB::table('shop_people_activities')->max('id');
        foreach ($data['tableList'] as $k){
            $value = [
                'time_slot_id' => $time_slot_id,
                'team_activity_id' => $k['activity_id'],
                'good_id' => $k['goods_id'],
                'create_time' => time()
            ];
            DB::table('shop_time_slot_activity')->insert($value);
        }
        return self::returnMsg([],'添加成功',20000);
    }
    //编辑活动 获取活动中的商品
    public function checkPgGoods(Request $request){
        $data = $request->post();
        $count = intval($request->input('limit', 20));
        if (empty($data['id'])) return self::returnMsg([],'参数错误',20001);
        $time_slot_id = DB::table('shop_time_slot_activity')->where('time_slot_id',$data['id'])->where('is_delete',0)->get();
        $activity_id = array_column(json_decode(json_encode($time_slot_id), true), 'team_activity_id');
        $activity = DB::table('shop_team_activity')
            ->leftjoin('shop_good as s', 's.goods_id', '=', 'shop_team_activity.goods_id')
            ->whereIn('activity_id',$activity_id)
            ->select('shop_team_activity.*', 's.goods_name', 's.market_price', 's.original_img','s.goods_state')
            ->orderBy('activity_id', 'desc')
            ->paginate($count);
        foreach ($activity as $v){
            $v->payCoin = DB::table('coin')->where('Id', $v->coin_id)->value('EnName'); //支付币种
            $v->getCoin = DB::table('coin')->where('Id', $v->luck_coin_id)->value('EnName'); //中奖得到币种
            $found = DB::table('shop_team_found')->where('good_id',$v->goods_id)->where('spike_id',$data['id'])->where('status',0)->first();
            if ($found) $v->is_found = '1';
        }
        $keyword = $request->input('keyword');
        if (!empty($keyword)) {
            $goods = DB::table('shop_good')->where("goods_name", "like", "%$keyword%")->get();
            $goods_id = array_column(json_decode(json_encode($goods), true), 'goods_id');
            $goodsLists = DB::table('shop_team_activity')->whereNotIn('activity_id',$activity_id)
                ->leftjoin('shop_good as s', 's.goods_id', '=', 'shop_team_activity.goods_id')
                ->whereIn('shop_team_activity.goods_id',$goods_id)
                ->select('shop_team_activity.*', 's.goods_name', 's.market_price', 's.original_img','s.goods_state')
                ->orderBy('activity_id', 'desc')
                ->get();
        } else {
        $goodsLists = DB::table('shop_team_activity')->whereNotIn('activity_id',$activity_id)
            ->leftjoin('shop_good as s', 's.goods_id', '=', 'shop_team_activity.goods_id')
            ->select('shop_team_activity.*', 's.goods_name', 's.market_price', 's.original_img','s.goods_state')
            ->orderBy('activity_id', 'desc')
            ->get();
        }
        foreach ($goodsLists as $v){
            $v->payCoin = DB::table('coin')->where('Id', $v->coin_id)->value('EnName'); //支付币种
            $v->getCoin = DB::table('coin')->where('Id', $v->luck_coin_id)->value('EnName'); //中奖得到币种
        }
        $res = [];
        $res["total"] = $activity->total();
        $res["list"] = $activity->items();
        $res["goodsLists"] = $goodsLists;
        return self::returnMsg($res);
    }
    //为某个活动添加活动商品
    public function activityGoodsAdd(Request $request){
        $data = $request->post();
        if (empty($data['id'])) return self::returnMsg([],'参数错误 请退出该页面重新进入',20001);
        if(empty($data['list'])) return self::returnMsg([],'请至少选择一个商品后提交',20001);
        foreach ($data['list'] as $v){
            $item = [
                'time_slot_id' => $data['id'],
                'team_activity_id' =>$v['activity_id'],
                'good_id' => $v['goods_id'],
                'create_time' => time(),
                'is_delete' => 0
            ];
           DB::table('shop_time_slot_activity')->insert($item);
        }
        return self::returnMsg();
    }
    //删除某个活动中的商品
    public function activityGoodsDel(Request $request){
        $data = $request->post();
        if (empty($data['id'])) return self::returnMsg([],'参数错误 请退出该页面重新进入',20001);
        if(empty($data['list'])) return self::returnMsg([],'请至少选择一个商品后提交',20001);
        foreach ($data['list'] as $v){
            DB::table('shop_time_slot_activity')->where('time_slot_id',$data['id'])->where('good_id',$v['goods_id'])->where('team_activity_id',$v['activity_id'])->update(['is_delete'=>1]);
        }
        return self::returnMsg();

    }
    //开团记录
    public function teamFound(Request $request){
        $where = [];
        $count = intval($request->input('limit', 20));
        $Phone = $request->input('Phone');
        if (!empty($Phone)) {
            $where[] = ["Phone", "like", "%$Phone%"];
        }
        $status = $request->input('status');
        if ($status != '') {
            $where[] = ["status", $status];
        }
        $found_id = $request->input('found_id');
        if (!empty($found_id)){
            $where [] = ['found_id',$found_id];
        }
        $lists = DB::table("shop_team_found")
            ->where($where)
            ->leftjoin('shop_good as s', 's.goods_id', '=', 'shop_team_found.good_id')
            ->leftjoin('members as m', 'm.Id', '=', 'shop_team_found.user_id')
            ->select('shop_team_found.*', 'm.Phone', 'm.NickName','s.goods_name', 's.market_price', 's.original_img','s.goods_state')
            ->orderBy('found_id', 'desc')
            ->paginate($count);
        foreach ($lists as $v) {
            $v->payCoin = DB::table('coin')->where('Id', $v->coin_id)->value('EnName'); //支付币种
            $v->getCoin = DB::table('coin')->where('Id', $v->luck_coin_id)->value('EnName'); //中奖得到币种
            $v->found_time = date('Y-m-d H:i:s', $v->found_time);
            $v->found_end_time = date('Y-m-d H:i:s', $v->found_end_time);
            if ($v->open_found_time!=0){
            $v->open_found_time = date('Y-m-d H:i:s', $v->open_found_time);
            } else {
                $v->open_found_time = "未开奖";
            }
            $v->found_id = (string)$v->found_id;
        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    //参团记录
    public function teamFollow(Request $request){
        $where = [];
        $count = intval($request->input('limit', 20));
        $Phone = $request->input('Phone');
        if (!empty($Phone)) {
            $where[] = ["Phone", "like", "%$Phone%"];
        }
        $status = $request->input('status');
        if ($status != '') {
            $where[] = ["shop_team_follow.status", $status];
        }
        $is_lock_draw = $request->input('is_lock_draw');
        if ($is_lock_draw != '') {
            $where[] = ["shop_team_follow.is_lock_draw", $is_lock_draw];
        }
        $found_id = $request->input('found_id');
        if (!empty($found_id)){
            $where [] = ['found_id',$found_id];
        }
        $lists = DB::table("shop_team_follow")
            ->where($where)
            ->leftjoin('shop_good as s', 's.goods_id', '=', 'shop_team_follow.good_id')
            ->leftjoin('members as m', 'm.Id', '=', 'shop_team_follow.follow_user_id')
            ->leftjoin('shop_team_activity as a', 'a.activity_id', '=', 'shop_team_follow.activity_id')
            ->select('shop_team_follow.*', 'm.Phone', 'm.NickName','s.goods_name', 's.market_price','s.goods_state', 's.original_img','a.*')
            ->orderBy('follow_id', 'desc')
            ->paginate($count);
        foreach ($lists as $v) {
            $v->follow_time = date('Y-m-d H:i:s', $v->follow_time);
            $v->payCoin = DB::table('coin')->where('Id', $v->coin_id)->value('EnName'); //支付币种
            $v->getCoin = DB::table('coin')->where('Id', $v->luck_coin_id)->value('EnName'); //中奖得到币种
            $v->follow_id = (string)$v->follow_id;
            $v->found_id = (string)$v->found_id;
        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    //中奖记录
    public function teamLottery(Request $request){
        $where = [];
        $count = intval($request->input('limit', 20));
        $Phone = $request->input('Phone');
        if (!empty($Phone)) {
            $where[] = ["Phone", "like", "%$Phone%"];
        }
        $status = $request->input('status');
        if ($status != '') {
            $where[] = ["status", $status];
        }
        $found_id = $request->input('found_id');
        if (!empty($found_id)){
            $where [] = ['found_id',$found_id];
        }
        $lists = DB::table("shop_team_lottery")
            ->where($where)
            ->leftjoin('shop_good as s', 's.goods_id', '=', 'shop_team_lottery.good_id')
            ->leftjoin('members as m', 'm.Id', '=', 'shop_team_lottery.user_id')
            ->leftjoin('shop_team_activity as a', 'a.activity_id', '=', 'shop_team_lottery.activity_id')
            ->select('shop_team_lottery.*', 'm.Phone', 'm.NickName','s.goods_name','s.goods_state', 's.market_price', 's.original_img','a.*')
            ->orderBy('id', 'desc')
            ->paginate($count);
        foreach ($lists as $v) {
            $v->create_time = date('Y-m-d H:i:s', $v->create_time);
            $v->payCoin = DB::table('coin')->where('Id', $v->coin_id)->value('EnName'); //支付币种
            $v->getCoin = DB::table('coin')->where('Id', $v->luck_coin_id)->value('EnName'); //中奖得到币种
            $v->id = (string)$v->id;
            $v->follow_id = (string)$v->follow_id;
            $v->found_id = (string)$v->found_id;
        }
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
    //订单设置
    public function orderSetting(){
        $data = DB::table('setting')->whereIn('k',['activity_time_out','shop_reward_rule','activity_order_time_out','shop_rule'])->get();
//        $data[2]->v = json_decode($data[2]->v,true);
        $rule = DB::table('setting')->where('k','shop_reward_rule')->get();
        foreach ($rule as $k){
            $array = json_decode($k->v,true);
            $rule = array();
            $i = 0;
            foreach ($array as $key => $val)
            {
                $quantity = explode ('-',$key);
                $rule[$i]['id'] = $quantity[0];
                $rule[$i]['val'] = $val;
                $rule[$i]['isSet'] = false;
                $i++;
            }
        }
        $res = [];
        $res['rule'] = $rule;
        $res['data'] = $data;
        return self::returnMsg($res);
    }
    //更新订单设置
    public function updateOrderSetting(Request $request){
        $data = $request->post();

        $res = $data['k'];
        switch ($res) {
//        活动订单支付截止时间
            case 'activity_order_time_out':
                if (!is_numeric($data['data']['v']) || $data['data']['v']<0) return self::returnMsg([], '提交参数错误！', 20001);
                $activity_time_out = DB::table('setting')->where('k','activity_time_out')->value('v');
                if ($data['data']['v'] >= $activity_time_out) return self::returnMsg([], '订单支付截止时间不可大于成团有效时间！', 20001);
                DB::table('setting')->where('k', $data['data']['k'])->update(["v" => $data['data']['v']]);
                break;
//                成团有效期
            case 'activity_time_out':
                if (!is_numeric($data['data']['v']) || $data['data']['v']<0) return self::returnMsg([], '提交参数错误！', 20001);
                $activity_order_time_out = DB::table('setting')->where('k','activity_order_time_out')->value('v');
                if ($data['data']['v'] <= $activity_order_time_out) return self::returnMsg([], '成团有效时间不可小于订单支付截止时间！', 20001);
                DB::table('setting')->where('k', $data['data']['k'])->update(["v" => $data['data']['v']]);
                break;
//                拼购规则
            case 'shop_rule':
                if (empty($data['data']['v'])) return self::returnMsg([], '请输入拼购规则！', 20001);
                DB::table('setting')->where('k', $data['data']['k'])->update(["v" => $data['data']['v']]);
                break;
//                拼购推荐奖励
            case 'shop_reward_rule':
                foreach ($data['data'] as $k){
                    if (!is_numeric($k['val'])) return self::returnMsg([],'提交参数错误',20001);
                    if ($k['val'] < 0 || $k['val'] > 1) return self::returnMsg([],'提交参数错误，请输入0-1之间的数值',20001);
                }
                $val = array_column(json_decode(json_encode($data['data']),true),'val');
                $key = array_column(json_decode(json_encode($data['data']),true),'id');
                $rule =  json_encode(array_combine($key,$val),true);
                DB::table('setting')->where('k', $data['k'])->update(["v" => $rule]);
                break;
        }
        return self::returnMsg();

    }
}
