<?php

namespace App\Http\Controllers;

use App\Exceptions\ArException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    //产品配置
    public function List(){
        $res = DB::table('Products')->get();
        return self::returnMsg($res);
    }

    //产品详情
    public function Detail(Request $request){
        $id = intval($request->input('Id'));
        if($id <= 0) throw new ArException(ArException::SELF_ERROR,'参数错误');
        $product = DB::table('Products')->where('Id', $id)->first();
        if(empty($product)) throw new ArException(ArException::SELF_ERROR,'产品不存在');
        $product->IsReturn = intval($product->IsReturn);
        $product->IsSell = intval($product->IsSell);
        $product->IsValid = intval($product->IsValid);
        return self::returnMsg($product);
    }

    //更新产品
    public function Edit(Request $request){
        $id = intval($request->input('Id'));
        if($id <= 0) 
            throw new ArException(ArException::SELF_ERROR,'参数错误');

        $rules = [
            'Name' => 'required|string',
            'Price' => 'required|numeric',
            'Period' => 'required|integer',
            'Ratio' => 'required|numeric',
            'IsSell' => 'required|integer',
            'IsReturn' => 'required|integer',
            'IsValid' => 'required|integer',
        ];
        $valid = Validator::make($request->all(), $rules,[
            'Name.required' => '请填写商品名字',
            'Price.required' => '请填写矿机价格',
            'Period.required' => '请填写有效期',
            'Ratio.required' => '请填写日利',
            'IsSell.required' => '请选择是否可购买',
            'IsReturn.required' => '请选择是否退本金',
            'IsValid.required' => '请选择是否有效',
        ]);
        if($valid->fails())
            return self::errorMsg($valid->errors()->first());
        $data = $valid->validated();
        if(bccomp($data['Ratio'], 1, 10) > 0)
            throw new ArException(ArException::SELF_ERROR,'收益比例不能大于1');

        DB::table('Products')->where('Id', $id)->update([
            'Name' => $data['Name'],
            'Price' => $data['Price'],
            'Period' => $data['Period'],
            'Ratio' => $data['Ratio'],
            'IsSell' => $data['IsSell'],
            'IsReturn' => $data['IsReturn'],
            'IsValid' => $data['IsValid']
        ]);
        return self::returnMsg();
    }

    public function Add(Request $request){
        $rules = [
            'Name' => 'required|string',
            'Price' => 'required|numeric',
            'Period' => 'required|integer',
            'Ratio' => 'required|numeric',
            'IsSell' => 'required|integer',
            'IsReturn' => 'required|integer',
            'IsValid' => 'required|integer',
        ];
        $valid = Validator::make($request->all(), $rules,[
            'Name.required' => '请填写商品名字',
            'Price.required' => '请填写矿机价格',
            'Period.required' => '请填写有效期',
            'Ratio.required' => '请填写日利',
            'IsSell.required' => '请选择是否可购买',
            'IsReturn.required' => '请选择是否退本金',
            'IsValid.required' => '请选择是否有效',
        ]);
        if($valid->fails())
            return self::errorMsg($valid->errors()->first());
        $data = $valid->validated();
        if(bccomp($data['Ratio'], 1, 10) > 0)
            throw new ArException(ArException::SELF_ERROR,'收益比例不能大于1');

        DB::table('Products')->insert([
            'Name' => $data['Name'],
            'Price' => $data['Price'],
            'Period' => $data['Period'],
            'Ratio' => $data['Ratio'],
            'IsSell' => $data['IsSell'],
            'IsReturn' => $data['IsReturn'],
            'IsValid' => $data['IsValid'],
            'AddTime' => time()
        ]);
        return self::returnMsg();
    }

}
