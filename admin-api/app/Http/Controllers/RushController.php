<?php

namespace App\Http\Controllers;

use App\Exceptions\ArException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RushController extends Controller
{
    //设为中签
    public function SetSuccess(Request $request){
        $id = intval($request->input('Id'));
        if(empty($id)) throw new ArException(ArException::PARAM_ERROR);

        DB::beginTransaction();
        try{
            $rush = DB::table('MemberRush')->where('Id', $id)->first();
            if(empty($rush)) throw new ArException(ArException::SELF_ERROR,'抢购记录不存在');
            $has = DB::table('MemberRush')->where('Id', $id)->where('IsSuccess', 1)->first();
            if(!empty($has)) throw new ArException(ArException::SELF_ERROR,'抢购状态错误');
            $has = DB::table('MemberRush')->where('Id', $id)->where('IsBuy', 1)->first();
            if(!empty($has)) throw new ArException(ArException::SELF_ERROR,'此抢购已中签');
            $has = DB::table('MemberRush')->where('Id', $id)->where('IsReturn', 1)->first();
            if(!empty($has)) throw new ArException(ArException::SELF_ERROR,'此抢购已公布');
            DB::table('MemberRush')->where('Id', $id)->update(['IsSuccess' => 1]);
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
        return self::returnMsg();
    }

    //抢购记录
    public function MemberRush(Request $request){
        $limit = intval($request->input('limit'));

        $data = DB::table('MemberRush');
        $buy = DB::table('MemberRush');
        if(!empty($request->input('Phone'))){
            $members = DB::table('members')->where('Phone', trim($request->input('Phone')))->get()->toArray();
            $mids = array_column($members, 'Id');
            $data = $data->whereIn('MemberId', $mids);
            $buy = $buy->whereIn('MemberId', $mids);
        }
        $data = $data->orderBy('Id','desc')->paginate($limit);
        $buyNumber = $buy->sum('Price');
        $list = [];
        foreach($data as $item){
            $nickName = '';
            $phone = '';
            $name = '';
            $member = DB::table('members')->where('Id', $item->MemberId)->first();
            if(!empty($member)){
                $nickName = $member->NickName;
                $phone = $member->Phone;
            }
            $product = DB::table('Products')->where('Id', $item->ProductId)->first();
            if(!empty($product)) $name = $product->Name;
            $item->Name = $name;
            $item->NickName = $nickName;
            $item->Phone = $phone;
            $list[] = $item;
        }
        return self::returnMsg(['list' => $list, 'total' => $data->total(), 'number' => $buyNumber]);
    }

    //产品配置
    public function List(){
        $res = DB::table('ProductRush')->get();
        $list = [];
        foreach($res as $item){
            $name = '';
            $product = DB::table('Products')->where('Id', $item->ProductId)->first();
            if(!empty($product)) $name = $product->Name;

            $item->ProductName = $name;

            $list[] = $item;
        }
        return self::returnMsg($res);
    }

    //产品详情
    public function Detail(Request $request){
        $id = intval($request->input('Id'));
        if($id <= 0) throw new ArException(ArException::SELF_ERROR,'参数错误');
        $product = DB::table('ProductRush')->where('Id', $id)->first();
        if(empty($product)) throw new ArException(ArException::SELF_ERROR,'抢购不存在');
        $product->PlanStartTime = date('Y-m-d '. $product->PlanStartTime);
        $product->PlanEndTime = date('Y-m-d '. $product->PlanEndTime);
        $product->PromulgateTime = date('Y-m-d '. $product->PromulgateTime);

        $product->IsClose = intval($product->IsClose);
        return self::returnMsg($product);
    }

    //更新产品
    public function Edit(Request $request){
        $id = intval($request->input('Id'));
        if($id <= 0)
            throw new ArException(ArException::SELF_ERROR,'参数错误');

        $rules = [
            'PlanStartTime' => 'required|string',
            'PlanEndTime' => 'required|string',
            'PromulgateTime' => 'required|string',
            'ProductId' => 'required|integer',
            'Ratio' => 'required|numeric',
            'IsClose' => 'required|integer'
        ];
        $valid = Validator::make($request->all(), $rules,[
            'PlanStartTime.required' => '请选择开始时间',
            'PlanEndTime.required' => '请选择结束时间',
            'PromulgateTime.required' => '请选择公布时间',
            'ProductId.required' => '请选择抢购矿机',
            'Ratio.required' => '请填写定金比例',
            'IsClose.required' => '请选择是否禁用'
        ]);
        if($valid->fails())
            return self::errorMsg($valid->errors()->first());
        $data = $valid->validated();
        if(bccomp($data['Ratio'], 1, 10) > 0)
            throw new ArException(ArException::SELF_ERROR,'收益比例不能大于1');

        DB::table('ProductRush')->where('Id', $id)->update([
            'PlanStartTime' => date('H:i:s',strtotime($data['PlanStartTime'])),
            'PlanEndTime' => date('H:i:s',strtotime($data['PlanEndTime'])),
            'PromulgateTime' => date('H:i:s',strtotime($data['PromulgateTime'])),
            'ProductId' => $data['ProductId'],
            'Ratio' => $data['Ratio'],
            'IsClose' => $data['IsClose'],
        ]);
        return self::returnMsg();
    }

    public function Add(Request $request){
        $rules = [
            'PlanStartTime' => 'required|string',
            'PlanEndTime' => 'required|string',
            'PromulgateTime' => 'required|string',
            'ProductId' => 'required|integer',
            'Ratio' => 'required|numeric',
            'IsClose' => 'required|integer'
        ];
        $valid = Validator::make($request->all(), $rules,[
            'PlanStartTime.required' => '请选择开始时间',
            'PlanEndTime.required' => '请选择结束时间',
            'PromulgateTime.required' => '请选择公布时间',
            'ProductId.required' => '请选择抢购矿机',
            'Ratio.required' => '请填写定金比例',
            'IsClose.required' => '请选择是否禁用'
        ]);
        if($valid->fails())
            return self::errorMsg($valid->errors()->first());
        $data = $valid->validated();
        if(bccomp($data['Ratio'], 1, 10) > 0)
            throw new ArException(ArException::SELF_ERROR,'收益比例不能大于1');

        DB::table('ProductRush')->insert([
            'PlanStartTime' => date('H:i:s',strtotime($data['PlanStartTime'])),
            'PlanEndTime' => date('H:i:s',strtotime($data['PlanEndTime'])),
            'PromulgateTime' => date('H:i:s',strtotime($data['PromulgateTime'])),
            'ProductId' => $data['ProductId'],
            'Ratio' => $data['Ratio'],
            'IsClose' => $data['IsClose'],
            'AddTime' => time()
        ]);
        return self::returnMsg();
    }

}
