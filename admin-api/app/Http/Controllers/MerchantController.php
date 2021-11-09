<?php
//
//namespace App\Http\Controllers;
//
//use App\Exceptions\ArException;
//use Illuminate\Support\Facades\DB;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Validator;
//
//class MerchantController extends Controller
//{
//
//    //商户列表
//    public function List(){
//        $res = DB::table('VirtualMerchant')->get();
//        $list = [];
//        foreach($res as $item){
//            $wxAccount = '';
//            $weixin = DB::table('BindPay')->where('Type',2)->where('PayType', 1)->where('MemberId', $item->Id)->first();
//            if(!empty($weixin)) $wxAccount = $weixin->Account;
//            $item->Weixin = $wxAccount;
//
//            $alipayAccount = '';
//            $alipay = DB::table('BindPay')->where('Type',2)->where('PayType', 2)->where('MemberId', $item->Id)->first();
//            if(!empty($alipay)) $alipayAccount = $alipay->Account;
//            $item->Alipay = $alipayAccount;
//
//            $bankCard = '';
//            $createBank = '';
//            $bank = DB::table('BankCard')->where('Type', 2)->where('MemberId', $item->Id)->first();
//            if(!empty($bank)){
//                $bankCard = $bank->CardNo;
//                $createBank = $bank->Bank;
//            }
//            $item->BankCard = $bankCard;
//            $item->Bank = $createBank;
//            $item->IsClose = intval($item->IsClose);
//            $list[] = $item;
//        }
//        return self::returnMsg($list);
//    }
//
//    public function Detail(Request $request){
//        $id = intval($request->input('Id'));
//        $merchant = DB::table('VirtualMerchant')->where('Id', $id)->first();
//        if(empty($merchant)) throw new ArException(ArException::SELF_ERROR,'不存在的商户');
//
//        $wechat = DB::table('BindPay')->where('Type',2)->where('PayType', 1)->where('MemberId', $id)->first();
//        $alipay = DB::table('BindPay')->where('Type',2)->where('PayType', 2)->where('MemberId', $id)->first();
//        $bank = DB::table('BankCard')->where('Type', 2)->where('MemberId', $id)->first();
//        $data = [
//            'Id' => $merchant->Id,
//            'Name' => $merchant->Name,
//            'WechatAccount' => empty($wechat) ? '' : $wechat->Account,
//            'WechatName' => empty($wechat) ? '' : $wechat->NickName,
//            'AlipayAccount' => empty($alipay) ? '' : $alipay->Account,
//            'AlipayName' => empty($alipay) ? '' : $alipay->NickName,
//            'BankCard' => empty($bank) ? '' : $bank->CardNo,
//            'Bank' => empty($bank) ? '' : $bank->Bank,
//            'BankName' => empty($bank) ? '' : $bank->Name,
//            'BankPhone' => empty($bank) ? '' : $bank->Phone,
//            'WechatQrCode' => empty($wechat) ? '' : $wechat->QrCode,
//            'AlipayQrCode' => empty($alipay) ? '' : $alipay->QrCode,
//            'IsClose' => intval($merchant->IsClose)
//        ];
//        return self::returnMsg($data);
//    }
//
//    public function Edit(Request $request){
//        $id = intval($request->input('Id'));
//        if($id <= 0)
//            throw new ArException(ArException::SELF_ERROR,'参数错误');
//
//        $rules = [
//            'Name' => 'required|string',
//            'WechatAccount' => 'required|string',
//            'WechatName' => 'required|string',
//            'AlipayAccount' => 'required|string',
//            'AlipayName' => 'required|string',
//            'BankCard' => 'required|string',
//            'Bank' => 'required|string',
//            'BankName' => 'required|string',
//            'BankPhone' => 'required|string',
//            'WechatQrCode' => 'required|string',
//            'AlipayQrCode' => 'required|string',
//            'IsClose' => 'required|integer',
//        ];
//        $valid = Validator::make($request->all(), $rules,[
//            'Name.required' => '请填写商户名字',
//            'WechatAccount.required' => '请填写微信账号',
//            'WechatName.required' => '请填写微信昵称',
//            'AlipayAccount.required' => '请填写支付宝账号',
//            'AlipayName.required' => '请填写支付宝昵称',
//            'BankCard.required' => '请填写银行卡号',
//            'Bank.required' => '请填写开户行',
//            'BankName.required' => '请填写持卡人',
//            'BankPhone.required' => '请填写预留手机号',
//            'WechatQrCode.required' => '请上传微信收款码',
//            'AlipayQrCode.required' => '请上传支付宝收款码',
//            'IsClose.required' => '请选择是否禁用',
//        ]);
//        if($valid->fails())
//            return self::errorMsg($valid->errors()->first());
//        $data = $valid->validated();
//        DB::beginTransaction();
//        try{
//            DB::table('BindPay')->where('MemberId', $id)->where('Type', 2)->where('PayType', 1)->update([
//                'Account' => $data['WechatAccount'],
//                'NickName' => $data['WechatName'],
//                'QrCode' => $data['WechatQrCode']
//            ]);
//            DB::table('BindPay')->where('Type',2)->where('PayType', 2)->where('MemberId', $id)->update([
//                'Account' => $data['AlipayAccount'],
//                'NickName' => $data['AlipayName'],
//                'QrCode' => $data['AlipayQrCode']
//            ]);
//            DB::table('BankCard')->where('Type', 2)->where('MemberId', $id)->update([
//                'Name' => $data['BankName'],
//                'Phone' => $data['BankPhone'],
//                'Bank' => $data['Bank'],
//                'CardNo' => $data['BankCard']
//            ]);
//            DB::table('VirtualMerchant')->where('Id', $id)->update([
//                'IsClose' => $data['IsClose']
//            ]);
//            DB::commit();
//            return self::returnMsg();
//        } catch(\Exception $e){
//            DB::rollBack();
//            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
//        }
//    }
//
//    public function Add(Request $request){
//        $rules = [
//            'Name' => 'required|string',
//            'WechatAccount' => 'required|string',
//            'WechatName' => 'required|string',
//            'AlipayAccount' => 'required|string',
//            'AlipayName' => 'required|string',
//            'BankCard' => 'required|string',
//            'Bank' => 'required|string',
//            'BankName' => 'required|string',
//            'BankPhone' => 'required|string',
//            'WechatQrCode' => 'required|string',
//            'AlipayQrCode' => 'required|string',
//            'IsClose' => 'required|integer',
//        ];
//        $valid = Validator::make($request->all(), $rules,[
//            'Name.required' => '请填写商户名字',
//            'WechatAccount.required' => '请填写微信账号',
//            'WechatName.required' => '请填写微信昵称',
//            'AlipayAccount.required' => '请填写支付宝账号',
//            'AlipayName.required' => '请填写支付宝昵称',
//            'BankCard.required' => '请填写银行卡号',
//            'Bank.required' => '请填写开户行',
//            'BankName.required' => '请填写持卡人',
//            'BankPhone.required' => '请填写预留手机号',
//            'WechatQrCode.required' => '请上传微信收款码',
//            'AlipayQrCode.required' => '请上传支付宝收款码',
//            'IsClose' => 'required|integer',
//        ]);
//        if($valid->fails())
//            return self::errorMsg($valid->errors()->first());
//        $data = $valid->validated();
//        DB::beginTransaction();
//        try{
//            $id = DB::table('VirtualMerchant')->insertGetId([
//                'Name' => $data['Name'],
//                'IsClose' => $data['IsClose']
//            ]);
//            DB::table('BindPay')->insert([
//                'MemberId' => $id,
//                'Type' => 2,
//                'PayType' => 1,
//                'Account' => $data['WechatAccount'],
//                'NickName' => $data['WechatName'],
//                'QrCode' => $data['WechatQrCode']
//            ]);
//            DB::table('BindPay')->insert([
//                'MemberId' => $id,
//                'Type' => 2,
//                'PayType' => 2,
//                'Account' => $data['AlipayAccount'],
//                'NickName' => $data['AlipayName'],
//                'QrCode' => $data['AlipayQrCode']
//            ]);
//            DB::table('BankCard')->insert([
//                'MemberId' => $id,
//                'Type' => 2,
//                'Name' => $data['BankName'],
//                'Phone' => $data['BankPhone'],
//                'Bank' => $data['Bank'],
//                'CardNo' => $data['BankCard'],
//                'AddTime' => time()
//            ]);
//            DB::commit();
//            return self::returnMsg();
//        } catch(\Exception $e){
//            DB::rollBack();
//            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
//        }
//    }
//     //商店树苗
//}
