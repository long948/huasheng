<?php

namespace App\Http\Controllers;

use App\Exceptions\ArException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LevelController extends Controller
{

    //等级配置
    public function List(){
        $res = DB::table('Level')->get();
        return self::returnMsg($res);
    }

    //等级详情
    public function Detail(Request $request){
        $id = intval($request->input('Id'));
        if($id <= 0) throw new ArException(ArException::SELF_ERROR,'参数错误');
        $product = DB::table('Level')->where('Id', $id)->first();
        if(empty($product)) throw new ArException(ArException::SELF_ERROR,'等级');
        return self::returnMsg($product);
    }

    //更新等级
    public function Edit(Request $request){
        $id = intval($request->input('Id'));
        if($id <= 0) 
            throw new ArException(ArException::SELF_ERROR,'参数错误');

        $rules = [
            'Name' => 'required|string',
            'Level' => 'required|integer',
            'Ratio' => 'required|numeric',
            'Achievement' => 'required|numeric',
            'LowNumber' => 'required|integer',
            'LowLevel' => 'required|integer'
        ];
        $valid = Validator::make($request->all(), $rules,[
            'Name.required' => '请填写等级名字',
            'Level.required' => '请填写等级序号',
            'Ratio.required' => '请填写收益',
            'Achievement.required' => '请填写九代内业绩',
            'LowNumber.required' => '请填写伞下数量',
            'LowLevel.required' => '请填写用户达到的指定等级'
        ]);
        if($valid->fails())
            return self::errorMsg($valid->errors()->first());
        $data = $valid->validated();
        if(bccomp($data['Ratio'], 1, 10) > 0)
            throw new ArException(ArException::SELF_ERROR,'收益比例不能大于1');

        DB::table('Level')->where('Id', $id)->update([
            'Name' => $data['Name'],
            'Level' => $data['Level'],
            'Ratio' => $data['Ratio'],
            'Achievement' => $data['Achievement'],
            'LowNumber' => $data['LowNumber'],
            'LowLevel' => $data['LowLevel']
        ]);
        return self::returnMsg();
    }

    public function Add(Request $request){
        $rules = [
            'Name' => 'required|string',
            'Level' => 'required|integer',
            'Ratio' => 'required|numeric',
            'Achievement' => 'required|numeric',
            'LowNumber' => 'required|integer',
            'LowLevel' => 'required|integer'
        ];
        $valid = Validator::make($request->all(), $rules,[
            'Name.required' => '请填写等级名字',
            'Level.required' => '请填写等级序号',
            'Ratio.required' => '请填写收益',
            'Achievement.required' => '请填写九代内业绩',
            'LowNumber.required' => '请填写伞下数量',
            'LowLevel.required' => '请填写用户达到的指定等级'
        ]);
        if($valid->fails())
            return self::errorMsg($valid->errors()->first());
        $data = $valid->validated();
        if(bccomp($data['Ratio'], 1, 10) > 0)
            throw new ArException(ArException::SELF_ERROR,'收益比例不能大于1');

        DB::table('Level')->insert([
            'Name' => $data['Name'],
            'Level' => $data['Level'],
            'Ratio' => $data['Ratio'],
            'Achievement' => $data['Achievement'],
            'LowNumber' => $data['LowNumber'],
            'LowLevel' => $data['LowLevel']
        ]);
        return self::returnMsg();
    }

}
