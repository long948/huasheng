<?php

namespace App\Http\Controllers;

use App\Services\AdminLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SmsTemplateController extends Controller
{
    // 列表
    public function lists(Request $request) {

        $where = [];
        $count = intval($request->input('limit', 20));
        $lists = DB::table("smstemplate")
            ->where($where)
            ->paginate($count);

        return self::returnList($lists->items(), $lists->total());

    }


    // 添加
    public function add(Request $request) {

        $data = $request->post();
        if (empty($data['Title'])){
            return self::returnMsg([],'名称不能为空',20001);
        }
        if (empty($data['CallIndex'])){
            return self::returnMsg([],'索引不能为空',20001);
        }
        if (empty($data['TemplateId'])){
            return self::returnMsg([],'模板Id不能为空',20001);
        }
        if (empty($data['Template'])){
            return self::returnMsg([],'模板参数不能为空',20001);
        }

        $info = DB::table("smstemplate")
            ->where("CallIndex", $data["CallIndex"])
            ->first();

        if (!empty($info)) {
            return self::returnMsg([],'该索引已存在',20001);
        }

        $data = [
            "Title" => $data["Title"],
            "CallIndex" => $data["CallIndex"],
            "TemplateId" => $data["TemplateId"],
            "Template" => $data["Template"],
            "IsValid" => !empty($data["IsValid"]) ? 1 : 0,
            "IsPhone" => !empty($data["IsPhone"]) ? 1 : 0,
        ];

        $Id = DB::table("smstemplate")->insertGetId($data);


        return self::returnMsg(["Id" => $Id]);

    }

    // 修改
    public function edit(Request $request) {

        $data = $request->post();

        $Id = !empty($data["Id"]) ? intval($data["Id"]) : 0;
        if (empty($Id)) {
            return self::returnMsg([],'请选择需要操作的记录',20001);
        }
        if (empty($data['Title'])){
            return self::returnMsg([],'名称不能为空',20001);
        }
        if (empty($data['CallIndex'])){
            return self::returnMsg([],'索引不能为空',20001);
        }
        if (empty($data['TemplateId'])){
            return self::returnMsg([],'模板Id不能为空',20001);
        }
        if (empty($data['Template'])){
            return self::returnMsg([],'模板参数不能为空',20001);
        }



        $data = [
            "Title" => $data["Title"],
            "CallIndex" => $data["CallIndex"],
            "TemplateId" => $data["TemplateId"],
            "Template" => $data["Template"],
            "IsValid" => !empty($data["IsValid"]) ? 1 : 0,
            "IsPhone" => !empty($data["IsPhone"]) ? 1 : 0,
        ];

        DB::table("smstemplate")->where("Id", $Id)->update($data);


        return self::returnMsg();

    }

    // 修改
    public function delete(Request $request) {

        $data = $request->post();

        $Id = !empty($data["Id"]) ? intval($data["Id"]) : 0;
        if (empty($Id)) {
            return self::returnMsg([],'请选择需要操作的记录',20001);
        }

        DB::table("smstemplate")->where("Id", $Id)->delete();


        return self::returnMsg();

    }
}
