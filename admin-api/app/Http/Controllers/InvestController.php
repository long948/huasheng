<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class InvestController extends Controller
{

    //产品配置
    public function List(Request $request){
        $limit = intval($request->input('limit'));
        $res = DB::table('MemberProduct');
        $buy = DB::table('MemberProduct');
        if(!empty(trim($request->input('NickName')))){
            $member = DB::table('members')->where('NickName', trim($request->input('NickName')))->get()->toArray();
            $mid = array_column($member, 'Id');
            $res = $res->whereIn('MemberId', $mid);
            $buy = $buy->whereIn('MemberId', $mid);
        }
        if(!empty(trim($request->input('Phone')))){
            $member = DB::table('members')->where('Phone', trim($request->input('Phone')))->get()->toArray();
            $mid = array_column($member, 'Id');
            $res = $res->whereIn('MemberId', $mid);
            $buy = $buy->whereIn('MemberId', $mid);
        }
        $res = $res->orderBy('Id','desc')->paginate($limit);
        $buyNumber = $buy->sum('Price');
        $list = [];
        foreach($res as $item){
            $name = '';
            $phone = '';
            $member = DB::table('members')->where('Id', $item->MemberId)->first();
            if(!empty($member)){
                $name = $member->NickName;
                $phone = $member->Phone;
            }
            $pName = '';
            $product = DB::table('Products')->where('Id', $item->ProductId)->first();
            if(!empty($product)) $pName = $product->Name;
            $item->Name = $pName;
            $item->Phone = $phone;
            $item->NickName = $name;
            $list[] = $item;
        }

        $rushMember = DB::table('MemberProduct')->where('SurplusDay', '>', 0)->where('Type', 2)->sum('Price');
        $giveNumber = DB::table('MemberProduct')->where('SurplusDay', '>', 0)->where('Type', 3)->sum('Price');
        $buySumNumber = DB::table('MemberProduct')->where('SurplusDay', '>', 0)->where('Type', 1)->sum('Price');
        $res = [
            'list' => $list,
            'total' => $res->total(),
            'number' => $buyNumber,
            'RushNumber' => $rushMember,
            'GiveNumber' => $giveNumber,
            'BuySumNumber' => $buySumNumber,
        ];
        return self::returnMsg($res);
    }
}
