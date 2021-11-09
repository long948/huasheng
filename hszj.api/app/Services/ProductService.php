<?php


namespace App\Services;

use App\Exceptions\ArException;
use App\Models\ProductModel as Product;
use Illuminate\Support\Facades\DB;
use App\Models\MembersModel as Members;
use App\Models\MemberCoinModel as MemberCoin;
use App\Models\CoinModel as Coin;
use App\Models\MemberProductModel as MemberProduct;
use App\Models\ProductRushModel as ProductRush;

class ProductService extends Service
{

    /**
     * @method 中奖名单
     * @param $date 中奖日期
     * @param $id 抢购Id
     */
    public function RushSuccess(int $date, int $id, int $count){
        if($count <= 0) throw new ArException(ArException::PARAM_ERROR);
        $data = DB::table('RushAndFake')->where('AddDate', $date)->where('RushId', $id)->paginate($count);
        $list = [];
        foreach($data as $item){
            $name = '';
            $avatar = '';
            $phone = '';
            if($item->MemberId != 0){
                $member = Members::where('Id', $item->MemberId)->first();
                $name = $member->NickName;
                $avatar = empty($avatar) ? $avatar : $this->QiniuDomain().'/'.$member->Avatar;
                $phone = replaceMobile($member->Phone);
            } else {
                $member = DB::table('FakeMember')->where('Id', $item->FakeMemberId)->first();
                $name = $member->Name;
                $avatar = $this->QiniuDomain().'/'.$member->Logo;
                $phone = replaceMobile($member->Phone);
            }

            if(empty($member)) continue;
            $list[] = [
                'Name' => $name,
                'Avatar' => $avatar,
                'Phone' => $phone
            ];
        }
        return ['List' => $list, 'Total' => $data->total()];
    }

    /**
     * @method 往期记录
     */
    public function RushLog(int $count){
        $data = DB::table('MemberRush')->select('AddDate', 'RushId')->orderBy('AddDate','desc')->groupBy('AddDate','RushId')->paginate($count);
        $list = [];
        foreach($data as $item){
            $item->Period = $item->AddDate.$item->RushId;
            $rush = DB::table('ProductRush')->where('Id', $item->RushId)->first();
            $product = Product::where('Id',$rush->ProductId)->first();

            $item->Price = bcmul($product->Price, $rush->Ratio, 10);
            $item->Period = $product->Period;
            $item->Ratio = $product->Ratio;
            $item->Name = $product->Name;

            $list[] = $item;
        }
        return ['List' => $list, 'Total' => $data->total()];
    }

    /**
     * @method 产出记录
     */
    public function MyOutput(int $uid, int $id, int $count){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        if($id <= 0) throw new ArException(ArException::PARAM_ERROR);

        $memberProduct = MemberProduct::where('MemberId', $uid)->where('Id', $id)->first();
        if(empty($memberProduct)) throw new ArException(ArException::SELF_ERROR,'您没有此矿机');

        $output = DB::table('RewardRecord')
            ->where('MemberProductId', $id)
            ->where('MemberId', $uid)
            ->where('Type', 1)
            ->paginate($count);

        $name = '';
        $product = Product::where('Id', $memberProduct->ProductId)->first();
        if(!empty($product)) $name = $product->Name;

        $list = [];
        foreach($output as $item){
            $list[] = [
                'Id' => $item->Id,
                'Name' => $name,
                'Number' => $item->Number,
                'Balance' => $item->Balance,
                'Lock' => $item->Lock,
                'AddTime' => $item->AddTime
            ];
        }
        return ['List' => $list, 'Total' => $output->total()];
    }

    /**
     * @method 我的订单详情
     */
    public function MyProductDetail(int $uid, int $id){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        if($id <= 0) throw new ArException(ArException::PARAM_ERROR);

        $memberProduct = MemberProduct::where('MemberId', $uid)->where('Id', $id)->first();
        if(empty($memberProduct)) throw new ArException(ArException::SELF_ERROR,'您没有此矿机');

        $product = Product::where('Id', $memberProduct->ProductId)->first();
        if(empty($product)) throw new ArException(ArException::SELF_ERROR,'此购买记录已失效');
        $res = [
            'Id' => $memberProduct->Id,
            'Name' => $product->Name,
            'Price' => $memberProduct->Price,
            'Ratio' => $memberProduct->Ratio,
            'Period' => $memberProduct->Period,
            'AddTime' => $memberProduct->AddTime,
            'EffectTime' => strtotime(date('Y-m-d',$memberProduct->AddTime)) + 86400,
            'ExpireTime' => $memberProduct->ExpireTime,
            'OrderNumber' => $memberProduct->OrderNumber
        ];
        return $res;
    }

    /**
     * @method 我的矿机
     * @param int $type 0全部 1在线 2过期 3新人
     */
    public function MyProduct(int $uid, int $type, int $count){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        if($count <= 0) throw new ArException(ArException::PARAM_ERROR);

        if($type == 0){
            $data = MemberProduct::where('MemberId', $uid)
                ->orderBy('Id','desc')
                ->paginate($count);
        } else if($type == 1){
            $data = MemberProduct::where('MemberId', $uid)
                ->where('SurplusDay','>', 0)
                ->where('Type','<>', 3)
                ->paginate($count);
        } else if($type == 2){
            $data = MemberProduct::where('MemberId', $uid)
                ->where('SurplusDay','<=', 0)
                ->where('Type','<>', 3)
                ->paginate($count);
        } else if($type == 3){
            $data = MemberProduct::where('MemberId', $uid)
                ->where('SurplusDay','>', 0)
                ->where('Type', 3)
                ->paginate($count);
        }
        $list = [];
        foreach($data as $item){
            if(empty($item->product)) continue;
            $output = DB::table('RewardRecord')->where('MemberProductId', $item->Id)->where('MemberId', $uid)->count();
            $list[] = [
                'Id' => $item->Id,
                'Name' => $item->product->Name,
                'Ratio' => $item->Ratio,
                'State' => $item->SurplusDay > 0 ? 1 : 0,
                'Period' => $item->Period,
                'OutputNumber' => $output
            ];
        }
        return ['List' => $list, 'Total' => $data->total()];
    }

    /**
     * @method 预约记录
     * @param int $type 0全部 1中奖 2未中奖
     */
    public function PlanRecord(int $uid, int $type, int $count){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        if($count <= 0) throw new ArException(ArException::PARAM_ERROR);

        if(empty($type)){
            $data = DB::table('MemberRush')
                ->select('AddDate', 'RushId')
                ->where('MemberId', $uid)
                ->orderBy('AddDate','desc')
                ->groupBy('AddDate','RushId')
                ->paginate($count);
        } else if($type == 1){
            $data = DB::table('MemberRush')
                ->select('AddDate', 'RushId')
                ->where('IsBuy', 1)
                ->where('MemberId', $uid)
                ->orderBy('AddDate','desc')
                ->groupBy('AddDate','RushId')
                ->paginate($count);
        } else if($type == 2){
            $data = DB::table('MemberRush')
                ->select('AddDate', 'RushId')
                ->where('IsReturn', 1)
                ->where('MemberId', $uid)
                ->orderBy('AddDate','desc')
                ->groupBy('AddDate','RushId')
                ->paginate($count);
        }
        $list = [];
        foreach($data as $item){
            $item->Period = $item->AddDate.$item->RushId;
            $rush = DB::table('ProductRush')->where('Id', $item->RushId)->first();
            $product = Product::where('Id',$rush->ProductId)->first();

            $item->Price = bcmul($product->Price, $rush->Ratio, 10);
            $item->Period = $product->Period;
            $item->Ratio = $product->Ratio;
            $item->Name = $product->Name;

            $list[] = $item;
        }
        return ['List' => $list, 'Total' => $data->total()];
    }

    /**
     * @method 总产出
     */
    public function OutputSum(int $uid){
        if($uid <= 0) throw new ArException(ArException::UNKONW);

        $sum = DB::table('RewardRecord')->where('MemberId', $uid)->sum('Number');
        $member = Members::where('Id', $uid)->first();
        if(empty($member)) throw new ArException(ArException::USER_NOT_FOUND);

        return ['Sum' => $sum, 'Balance' => $member->Balance, 'LockBalance' => $member->LockBalance];
    }

    /**
     * @method 产出记录
     */
    public function Output(int $uid, int $type, int $count){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        if($count <= 0) throw new ArException(ArException::PARAM_ERROR);

        if($type == 0){
            $data = DB::table('RewardRecord')->where('MemberId', $uid)->orderBy('Id','desc')->paginate($count);
        } else {
            $data = DB::table('RewardRecord')->where('MemberId', $uid)->where('Type', $type)->orderBy('Id','desc')->paginate($count);
        }

        $list = [];
        foreach($data as $item){
            //矿机要展示名字
            $name = '';
            $lowLv = '';
            $memberProduct = MemberProduct::where('Id', $item->MemberProductId)->first();
            if($item->Type == 1){
                if(!empty($memberProduct)){
                    $product = Product::where('Id', $memberProduct->ProductId)->first();
                    if(!empty($product)) $name = $product->Name;
                }
                $item->Name = $name;
            } else {
                if(!empty($memberProduct)){
                    $member = Members::where('Id', $memberProduct->MemberId)->first();
                    if(!empty($member)) $lowLv = replaceMobile($member->Phone);
                }
                $item->LowLevel = $lowLv;
            }
            $list[] = $item;
        }
        return ['List' => $list, 'Total' => $data->total()];
    }

    /**
     * @method 产出提现页面数据
     */
    public function WithdrawDetail(int $uid){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        $setting = DB::table('PlatformSetting')->first();
        if(empty($setting)) throw new ArException(ArException::SELF_ERROR,'暂时不能提现');
        $member = Members::where('Id', $uid)->first();
        if(empty($member)) throw new ArException(ArException::USER_NOT_FOUND);

        $data = [
            'Enable' => $member->Balance,
            'Least' => $setting->MinWithdraw,
            'Fee' => $setting->WithdrawFee
        ];
        return $data;
    }

    /**
     * @method 收益余额提现
     */
    public function Withdraw(int $uid, $number){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        if(!is_numeric($number)) throw new ArException(ArException::PARAM_ERROR);

        $setting = DB::table('PlatformSetting')->first();
        if(empty($setting)) throw new ArException(ArException::SELF_ERROR,'暂时不能提现');

        DB::beginTransaction();
        try{
            $coin = Coin::GetByEnName('USDT');
            if(empty($coin)) throw new ArException(ArException::SELF_ERROR,'不存在币种USDT');
            if(bccomp($setting->MinWithdraw, $number, 10) > 0)
                throw new ArException(ArException::SELF_ERROR, '最小提现数量'.$setting->MinWithdraw);

            $member = Members::where('Id', $uid)->first();
            if(empty($member)) throw new ArException(ArException::USER_NOT_FOUND);

            if(bccomp($member->Balance, $number, 10) < 0)
                throw new ArException(ArException::SELF_ERROR,'余额不足');

            //减少收益余额
            DB::table('Members')->where('Id', $uid)->decrement('Balance', $number);

            //扣掉手续费
            $fee = bcmul($setting->WithdrawFee, $number, 10);
            $real = bcsub($number, $fee, 10);
            $memberCoin = DB::table('MemberCoin')
                ->where('MemberId', $member->Id)
                ->where('CoinId', $coin->Id)->first();
            if(empty($memberCoin)){
                DB::table('MemberCoin')->insert([
                    'MemberId' => $member->Id,
                    'CoinId' => $coin->Id,
                    'Money' => $real,
                    'CoinName' => $coin->EnName
                ]);
            } else {
                DB::table('MemberCoin')
                    ->where('MemberId', $member->Id)
                    ->where('CoinId', $coin->Id)
                    ->increment('Money', $real);
            }
            self::AddLog($uid, $real, $coin, 'balance_withdraw');
            $has = DB::table('Members')->where('Id', $uid)->where('Balance', '<', 0)->first();
            if(!empty($has)) throw new ArException(ArException::SELF_ERROR,'账户余额不足');
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
    }

    /**
     * @method 释放记录
     */
    public function FreeList(int $uid, int $count){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        if($count <= 0) throw new ArException(ArException::PARAM_ERROR);

        $data = DB::table('FreeLog')->where('MemberId', $uid)->orderBy('Id','desc')->paginate($count);
        $list = [];
        foreach($data as $item){
            $list[] = $item;
        }
        $member = Members::where('Id', $uid)->first();
        if(empty($member)) throw new ArException(ArException::USER_NOT_FOUND);

        $date = strtotime(date('Y-m-d'));
        $zDay = DB::table('FreeLog')
            ->where('MemberId', $uid)
            ->where('AddTime', '>', $date - 86400)
            ->where('AddTime','<', $date)
            ->sum('Number');
        return ['List' => $list, 'Total' => $data->total(), 'Lock' => $member->LockBalance, 'Yesterday' => $zDay];
    }

    /**
     * @method 抢购
     */
    public function RushPurchase(int $uid, int $id){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        if($id <= 0) throw new ArException(ArException::PARAM_ERROR);

        $rush = ProductRush::where('Id', $id)->where('IsClose', 0)->first();
        if(empty($rush)) throw new ArException(ArException::SELF_ERROR,'不存在此抢购活动');

        $start = strtotime(date('Y-m-d '.$rush->PlanStartTime));
        $end = strtotime(date('Y-m-d '.$rush->PlanEndTime));
        $time = time();
        if($time < $start)
            throw new ArException(ArException::SELF_ERROR,'此活动还未开始');
        if($time > $end)
            throw new ArException(ArException::SELF_ERROR,'此活动已结束');

        DB::beginTransaction();
        try{
            $coin = Coin::GetByEnName('USDT');
            if(empty($coin)) throw new ArException(ArException::SELF_ERROR,'币种USDT不存在');

            $memberCoin = MemberCoin::where('MemberId', $uid)->where('CoinId', $coin->Id)->first();
            if(empty($memberCoin)) throw new ArException(ArException::SELF_ERROR,'您未持有USDT');

            if(empty($rush->product)) throw new ArException(ArException::SELF_ERROR,'抢购活动未指定抢购矿机');

            $price = bcmul($rush->product->Price, $rush->Ratio, 8);
            if(bccomp($price, $memberCoin->Money, 10) > 0) throw new ArException(ArException::SELF_ERROR,'USDT余额不足');

            DB::table('MemberCoin')->where('MemberId', $uid)->where('CoinId', $coin->Id)->update([
                'Money' => DB::raw("Money-{$price}"),
                'Forzen' => DB::raw("Forzen+{$price}")
            ]);
            self::AddLog($uid, -$price, $coin, 'rush_product');
            DB::table('MemberRush')->insert([
                'MemberId' => $uid,
                'RushId' => $id,
                'AddTime' => time(),
                'AddDate' => intval(date('Ymd')),
                'Price' => $price,
                'ProductId' => $rush->ProductId
            ]);
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }
    }

    /**
     * @method 领取注册矿机
     */
    public function Draw(int $uid){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        $setting = DB::table('PlatformSetting')->first();
        if(empty($setting)) throw new ArException(ArException::SELF_ERROR,'平台配置错误');
        $pid = $setting->RegProductId;
        DB::beginTransaction();
        try{
            //实名认证通过才能领取
            $member = Members::find($uid);
            if($member->IsAuth != 1) throw new ArException(ArException::SELF_ERROR,'实名认证后才能领取');
            $has = MemberProduct::where('MemberId', $uid)->where('Type', 3)->first();
            if(!empty($has)) throw new ArException(ArException::SELF_ERROR,'您已领取过,请勿重复领取');

            $product = Product::where('Id', $pid)->first();
            if(empty($product)) throw new ArException(ArException::SELF_ERROR,'注册送的矿机不存在');

            DB::table('MemberProduct')->insert([
                'MemberId' => $uid,
                'ProductId' => $pid,
                'SurplusDay' => $product->Period,
                'Period' => $product->Period,
                'Price' => $product->Price,
                'Ratio' => $product->Ratio,
                'IsValid' => $product->IsValid,
                'AddTime' => time(),
                'Type' => 3
            ]);
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR,$e->getMessage());
        }
    }

    /**
     * @method 购买
     */
    public function Purchase(int $uid, int $id, int $number){
        if($uid <= 0) throw new ArException(ArException::UNKONW);
        if($id <= 0) throw new ArException(ArException::PARAM_ERROR);
        if($number <= 0) throw new ArException(ArException::SELF_ERROR,'请填写购买数量');

        DB::beginTransaction();
        try{
            $coin = Coin::GetByEnName('USDT');
            if(empty($coin)) throw new ArException(ArException::SELF_ERROR,'币种USDT不存在');

            $product = Product::GetOneByCond(['Id' => $id, 'IsSell' => 1]);
            if(empty($product)) throw new ArException(ArException::SELF_ERROR,'不存在此矿机');

            $member = Members::where('Id', $uid)->where('IsBan', 0)->first();
            if(empty($member)) throw new ArException(ArException::USER_NOT_FOUND);

            $memberCoin = MemberCoin::where('CoinId', $coin->Id)->where('MemberId', $uid)->first();
            if(empty($memberCoin)) throw new ArException(ArException::SELF_ERROR,'您未持有USDT');

            $price = bcmul($number, $product->Price, 8);
            if(bccomp($price, $memberCoin->Money, 8) > 0)
                throw new ArException(ArException::SELF_ERROR,'USDT余额不足');

            $setting = DB::table('PlatformSetting')->first();
            if(empty($setting)) throw new ArException(ArException::SELF_ERROR, '平台设置错误');
            $has = MemberProduct::where('MemberId', $uid)->where('SurplusDay', '>', 0)->where('Type', 1)->count();
            $in = $has + $number;
            if($in > $setting->LimitNumber) throw new ArException(ArException::SELF_ERROR,'当前矿机数量达到上限');


            DB::table('MemberCoin')->where('CoinId', $coin->Id)->where('MemberId', $uid)->decrement('Money', $price);
            self::AddLog($uid, -$price, $coin, 'purchase_product');
            $time = time();
            for($i=0; $i<$number; ++$i){
                DB::table('MemberProduct')->insert([
                    'MemberId' => $uid,
                    'ProductId' => $id,
                    'SurplusDay' => $product->Period,
                    'Period' => $product->Period,
                    'Price' => $product->Price,
                    'Ratio' => $product->Ratio,
                    'IsValid' => $product->IsValid,
                    'AddTime' => $time,
                    'Type' => 1
                ]);
            }
            //如果抢购的矿机为有效 增加上级的业绩 只加九代内 把用户更新为有效
            if($product->IsValid == 1){
                $root = $this->Get9Root($member->Root);
                DB::table('Members')->whereIn('Id', $root)->increment('TeamAchievement', $price);
                DB::table('Members')->where('Id', $uid)->update(['IsValid' => 1]);
            }
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR,$e->getMessage());
        }
    }

    /**
     * @method 矿机详情
     */
    public function Detail(int $id){
        $setting = DB::table('PlatformSetting')->first();
        if(empty($setting)) throw new ArException(ArException::SELF_ERROR,'平台配置错误');
        $pid = $setting->RegProductId;
        $product = Product::where('Id', $id)->first();
        //if(empty($product)) throw new ArException(ArException::SELF_ERROR,'没有此矿机');
        $data = [
            'Id' => $product->Id,
            'Name' => $product->Name,
            'Price' => $product->Price,
            'PriceCny' => $product->Price * 7,
            'Ratio' => $product->Ratio,
            'Period' => $product->Period
        ];
        return $data;
    }

    /**
     * @method 新人福利
     */
    public function NewReg(int $uid){
        $has = MemberProduct::where('MemberId', $uid)->where('Type', 3)->first();
        if(!empty($has)) throw new ArException(ArException::SELF_ERROR,'您已领取过,请勿重复领取');
        $setting = DB::table('PlatformSetting')->first();
        if(empty($setting)) throw new ArException(ArException::SELF_ERROR,'平台配置错误');
        $product = Product::where('Id', $setting->RegProductId)->first();
        if(empty($product)) throw new ArException(ArException::SELF_ERROR,'没有此矿机');
        $data = [
            'Id' => $product->Id,
            'Name' => $product->Name,
            'Price' => $product->Price,
            'PriceCny' => $product->Price * 7,
            'Ratio' => $product->Ratio,
            'Period' => $product->Period
        ];
        return $data;
    }

    /**
     * @method 矿机列表
     */
    public function List($count){
        if($count <= 0) throw new ArException(ArException::PARAM_ERROR);

        $data = Product::where('IsSell', 1)->paginate($count);
        $list = [];
        foreach($data as $item){
            $list[] = [
                'Id' => $item->Id,
                'Name' => $item->Name,
                'Ratio' => $item->Ratio,
                'Period' => $item->Period,
                'Price' => $item->Price
            ];
        }
        return ['List' => $list, 'Total' => $data->total()];
    }

    /**
     * @method 限时抢购
     */
    public function Rush(){
        $rush = ProductRush::where('IsClose', 0)->get();
        $list = [];
        foreach($rush as $item){
            if(empty($item->product)) continue;
            $list[] = [
                'Id' => $item->Id,
                'ProductId' => $item->product->Id,
                'Name' => $item->product->Name,
                'Period' => $item->product->Period,
                'Price' => bcmul($item->product->Price, $item->Ratio, 8),
                'Ratio' => $item->product->Ratio,
                'PlanStartTime' => $item->PlanStartTime,
                'PlanEndTime' => $item->PlanEndTime
            ];
        }
        return $list;
    }

}
