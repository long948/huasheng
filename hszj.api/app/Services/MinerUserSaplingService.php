<?php


namespace App\Services;

use App\Common\DTO\UserDeductionDTO;
use App\Exceptions\ArException;
use App\Jobs\SaplingJob;
use App\Jobs\UserIncomeJob;
use App\Models\CoinModel;
use App\Models\CoinModel as Coin;
use App\Models\Vo\UserSaplingReceive;
use App\Services\User\MinerUserSaplingTotalReleaseService;
use App\Services\User\MinerUserTeamInfoService;
use App\Utils\DateUtil;
use App\Utils\Enum\Enums;
use App\Utils\RedisLock;
use App\Utils\Snowflake;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * 用户树苗管理
 * Class MinerUserSaplingService
 * @package App\Services
 */
class MinerUserSaplingService extends Service
{

    /**
     * 用户的树苗列表
     * @param $user_id
     * @param int $is_experience
     * @return Collection
     */
    public function userSaplingList($user_id, $is_experience = 0)
    {
        $where = [
            ['g.is_disable', '=', 0],
            ['g.is_delete', '=', 0],
            ['g.is_release_complete', '=', 0],
            ['g.user_id', '=', $user_id],
        ];
        if ($is_experience) {
            $where[] = ['g.is_experience', '=', 0];
            $where[] = ['g.is_gave_away', '=', 0];
        }
        $list = DB::table('miner_user_sapling as g')
            ->join('miner_sapling as gg', 'g.sapling_id', 'gg.id')
            ->where($where)->orderBy('g.update_time', 'desc') //以最后一次操作 作为第一次展示的树
            ->select([
                'g.id as user_sapling_id',
                'g.is_gave_away',
                'g.sapling_id',
                'g.total_amount',
                'g.release_amount',
                'g.type',
                'g.freed',
                'gg.nickname',
                'gg.icon'])
            ->get();
        $domain = getDomain();
        $day = getDay();
        foreach ($list as $item) {
            $item->user_sapling_id .= '';
            if ($item->icon) {
                $item->icon = $domain . $item->icon;
            }
            //今日是否浇水和领取
            $child = DB::table('miner_user_sapling_release')
                ->whereBetween('create_time', [dateFormat($day['beginTime']), dateFormat($day['endTime'])])
                ->where('user_sapling_id', $item->user_sapling_id)
                ->where('user_id', $user_id)
                ->first(['id as day_user_sapling_id', 'sapling_id', 'is_receive', 'is_watering']);
            //是否生成能量球
            $item->is_generate = 0;
            //是否浇水
            $item->is_watering = 0;
            //是否领取
            $item->is_receive = 0;
            //进度条
            $item->proportion = bcdiv($item->release_amount, $item->total_amount, 4);
            if ($child) {
                $item->is_watering = $child->is_watering ?? 0;
                $item->is_receive = $child->is_receive ?? 0;
                $item->day_user_sapling_id = $child->day_user_sapling_id . '' ?? 0;
                $item->is_generate = 1;
            }
            $item->status = $this->calculationSaplingStatus($item->total_amount, $item->release_amount);
        }
        return $list;
    }


    /**
     * 逍遥王土地
     * @param $user_id
     * @return Collection
     */
    public function userSaplingXiaoYaoWang($user_id)
    {
        $where = [
            ['g.is_disable', '=', 0],
            ['g.is_delete', '=', 0],
            ['g.is_release_complete', '=', 0],
            ['g.user_id', '=', $user_id],
            ['g.type', '=', 7],
        ];
        $list = DB::table('miner_user_sapling as g')
            ->join('miner_sapling as gg', 'g.sapling_id', 'gg.id')
            ->where($where)->orderBy('g.update_time', 'desc') //以最后一次操作 作为第一次展示的树
            ->select([
                'g.id as user_sapling_id',
                'g.is_gave_away',
                'g.sapling_id',
                'g.total_amount',
                'g.release_amount',
                'g.freed',
                'gg.nickname',
                'gg.icon'])
            ->get();
        $domain = getDomain();
        $day = getDay();
        foreach ($list as $item) {
            $item->user_sapling_id .= '';
            if ($item->icon) {
                $item->icon = $domain . $item->icon;
            }
            //今日是否浇水和领取
            $child = DB::table('miner_user_sapling_release')
                ->whereBetween('create_time', [dateFormat($day['beginTime']), dateFormat($day['endTime'])])
                ->where('user_sapling_id', $item->user_sapling_id)
                ->where('user_id', $user_id)
                ->first(['id as day_user_sapling_id', 'sapling_id', 'is_receive', 'is_watering']);
            //是否生成能量球
            $item->is_generate = 0;
            //是否浇水
            $item->is_watering = 0;
            //是否领取
            $item->is_receive = 0;
            //进度条
            $item->proportion = bcdiv($item->release_amount, $item->total_amount, 4);
            if ($child) {
                $item->is_watering = $child->is_watering ?? 0;
                $item->is_receive = $child->is_receive ?? 0;
                $item->day_user_sapling_id = $child->day_user_sapling_id . '' ?? 0;
                $item->is_generate = 1;
            }
            $item->status = $this->calculationSaplingStatus($item->total_amount, $item->release_amount);
        }
        return $list;
    }

    /**
     * 获取用户小树苗详情
     * @param $user_id
     * @param $sapling_id 用户树苗编号
     * @return Builder|mixed|null
     * @throws ArException
     */
    public function userSaplingDetail($user_id, $sapling_id)
    {
        $userSapling = DB::table('miner_user_sapling as g')
            ->join('miner_sapling as gg', 'g.sapling_id', 'gg.id')
            ->where([
                ['g.user_id', '=', $user_id],
                ['g.id', '=', $sapling_id],
                ['g.is_disable', '=', 0],
                ['g.is_delete', '=', 0],
                ['g.is_release_complete', '=', 0]
            ])->first(['g.id as user_sapling_id', 'g.*', 'gg.id as sapling_id', 'gg.nickname', 'gg.icon', 'gg.background_image']);
        if (empty($userSapling)) {
            return collect([]);
            throw new ArException(ArException::SELF_ERROR, '花田不存在');
        }
        unset($userSapling->id);
        unset($userSapling->is_disable);
        unset($userSapling->is_delete);
        unset($userSapling->begin_receive_time);
        unset($userSapling->release_complete_time);
        unset($userSapling->is_superior_reward);
        unset($userSapling->is_experience);
        unset($userSapling->is_release_complete);
        unset($userSapling->release_complete_time);
        unset($userSapling->type);
        unset($userSapling->update_time);
        unset($userSapling->remarks);
        $userSapling->user_sapling_id .= '';
        $userSapling->sapling_id .= '';
        $domain = getDomain();
        $userSapling->icon = $domain . $userSapling->icon;
        $userSapling->background_image = $domain . $userSapling->background_image;

        $day = getDay();
        //今日是否浇水和领取
        $child = DB::table('miner_user_sapling_release')
            ->where('user_id', $user_id)
            ->where('user_sapling_id', $userSapling->user_sapling_id)
            ->whereBetween('create_time', [dateFormat($day['beginTime']), dateFormat($day['endTime'])])
            ->where('begin_receive_time', '>=', dateFormat($day['beginTime']))
            ->first(['id', 'sapling_id', 'is_receive', 'is_watering', 'total_count', 'surplus_count']);
        $userSapling->is_generate = 0;
        $userSapling->is_watering = 0;
        $userSapling->is_receive = 0;
        $userSapling->day_user_sapling_id = 0;
        $userSapling->total_count = 0;
        $userSapling->surplus_count = 0;
        $userSapling->status = 1;

        if ($child) {
            $userSapling->status = $this->calculationSaplingStatus($userSapling->total_amount, $userSapling->release_amount);;
            $userSapling->is_generate = 1;
            $userSapling->is_watering = $child->is_watering ?? 0;
            $userSapling->is_receive = $child->is_receive ?? 0;
            $userSapling->day_user_sapling_id = $child->id . '' ?? 0;
            $userSapling->total_count = $child->total_count . '' ?? 0;
            $userSapling->surplus_count = $child->surplus_count . '' ?? 0;
        }
        return $userSapling;
    }

    /**
     * 用户实名认证后赠送小树苗
     * @param $user_id
     * @return bool|mixed
     * @throws \Throwable
     */
    public function giveAwayByAuth($user_id)
    {
        return RedisLock::lock('giveAwayByAuth_' . $user_id, function () use ($user_id) {
            $sapling = $this->userAuthIsGiveAway($user_id);
            if ($sapling->isEmpty()) {
                throw new ArException(ArException::SELF_ERROR, '您已经领取过小花田了');
            }
            return $this->giveAway($user_id, $sapling['id'], 1, '认证赠送体验花田');
        }, 5);
    }

    /**
     * 用户认证后是否领取小树苗
     * @param $user_id
     * @return Collection
     */
    public function userAuthIsGiveAway($user_id)
    {
        $sapling = DB::table('miner_sapling')->where([
            ['is_experience', '=', 1],
            ['is_disable', '=', 0],
            ['is_delete', '=', 0],
        ])->orderBy('sort', 'asc')->first();
        if (empty($sapling)) {
            return collect([]);
        }
        $is = DB::table('miner_user_sapling')
            ->where('user_id', $user_id)
            ->where('sapling_id', $sapling->id)
            ->where('is_gave_away', 1)->count('id');
        if ($is > 0) {
            return collect([]);
        }
        return collect($sapling);
    }

    /**
     * 赠送树苗
     * @param $user_id 用户编号
     * @param $sapling_id 树苗编号
     * @param int $type 算力来源类型 1.认证赠送 2购买 3分红奖励 4。分享奖励
     * @param string $remarks
     * @return mixed
     * @throws \Throwable
     */
    public function giveAway($user_id, $sapling_id, $type = 1, $remarks = '系统赠送')
    {
        $saplingService = new MinerSaplingService();
        $sapling = $saplingService->saplingDetail($sapling_id);

        if (empty($sapling)) {
            return;
        }

        $sn = new Snowflake();
        $data['id'] = $sn->nextId();
        $data['user_id'] = $user_id;
        $data['sapling_id'] = $sapling_id;
        $data['total_amount'] = $sapling->total_profit;
        $data['type'] = $type;
        $data['yield'] = $sapling->yield;
        $data['total_price'] = $sapling->price;
        $data['rate_of_return'] = $sapling->rate_of_return;
        $data['surplus_amount'] = $sapling->total_profit;
        $data['computing_power'] = $sapling->computing_power;
        $data['total_freed'] = ($sapling->cycle);
        $data['freed'] = ($sapling->cycle);
        $data['is_experience'] = $sapling->is_experience;
        $data['is_gave_away'] = 1;
        $data['is_superior_reward'] = $sapling->is_superior_reward;
        //$data['begin_receive_time'] = date("Y-m-d 00:00:00", strtotime("+1 day"));//明天开始时间
        $data['begin_receive_time'] = dateFormat();
        $data['release_complete_time'] = dateFormat(strtotime('+' . $sapling->cycle . 'day'));
        $data['remarks'] = $remarks;
        $data['create_time'] = dateFormat();
        return DB::transaction(function () use ($data, $sapling, $type) {
            DB::table('miner_user_sapling')->insert($data);
            if ($sapling->computing_power > 0) { //需要赠送算力
                $userComputingPowerService = new  MinerUserComputingPowerService();
                $userComputingPowerService->add($data['user_id'], $sapling->computing_power,
                    $data['id'], $type, 1, $data['begin_receive_time'],
                    $data['release_complete_time'], $data['remarks']);
            }
            return true;
        });
    }

    /**
     * 购买树苗
     * @param $user_id
     * @param $sapling_id
     * @return mixed
     * @throws ArException
     * @throws \Throwable
     */
    public function buySapling($user_id, $sapling_id)
    {
        return RedisLock::lock(Enums::REDIS_LOCK_KEY['USER_BUY_SAPLING'] . $user_id, function () use ($sapling_id, $user_id) {
            $sapling = DB::table('miner_sapling')->find($sapling_id);
            if (empty($sapling)) {
                throw new ArException(ArException::SELF_ERROR, '该花田不存在');
            }
            if ($sapling->is_experience) {
                throw new ArException(ArException::SELF_ERROR, '体验花田不支持购买');
            }

            $effective_people_count = DB::table('miner_user_team_info')->where('user_id', $user_id)->value('effective_people_count');
//            if ($sapling->direct_push > 0) {
//                if ($effective_people_count < $sapling->direct_push){
//                    throw new ArException(ArException::SELF_ERROR, "需直推{$sapling->direct_push}人才能购买此花田");
//                }
//            } else {
//                //需要到达对应的常规等级
//                $userLevelId = getUserConventionalLevelId($user_id);
//                if ($userLevelId < $sapling->user_level) {
//                    $levelName = getConventionalLevelName($sapling->user_level) ?? '相应的';
//                    throw new ArException(ArException::SELF_ERROR, "需达到{$levelName}等级才能购买此花田");
//                }
//            }

            $userLevelId = getUserConventionalLevelId($user_id);
            $isBuy = false;
            if ($userLevelId >= $sapling->user_level) {
                $isBuy = true;
            }

            if ($sapling->direct_push > 0) {
                if ($effective_people_count >= $sapling->direct_push) {
                    $isBuy = true;
                }
            }

            if (!$isBuy) {
                throw new ArException(ArException::SELF_ERROR, "等级或推荐人数不足");
            }

            //此书需要加入直推人数限制
            $userSaplingCount = DB::table('miner_user_sapling')
                ->where([
                    ['user_id', '=', $user_id],
                    ['sapling_id', '=', $sapling_id],
                    ['is_disable', '=', 0],
                    ['is_delete', '=', 0],
                    ['is_release_complete', '=', 0],
                ])->count('id');
            if ($sapling->is_shop_sapling == 0) {
                if ($userSaplingCount >= $sapling->max_hold) {
                    throw new ArException(ArException::SELF_ERROR, '该花田您已购买到上限');
                }

                $userSaplingTotalCount = DB::table('miner_user_sapling')
                    ->where([
                        ['user_id', '=', $user_id],
                        ['is_disable', '=', 0],
                        ['is_delete', '=', 0],
                        ['is_release_complete', '=', 0],
                        ['type', '!=', 7],
                    ])->count('id');
                if ($userSaplingTotalCount >= 9) {
                    throw new ArException(ArException::SELF_ERROR, '您的田已经种满了');
                }
                
            }

            return DB::transaction(function () use ($user_id, $sapling_id, $sapling) {

                $userGiveAwayService = new UserGiveAwayService();
                $userGiveAwayAmount = $userGiveAwayService->getUserAmount($user_id);
                //余额足够 且支持备用斤支付
                if ($sapling->is_spare == 1 && $userGiveAwayAmount >= $sapling->recommend_price) {

                    $userDTO = new UserDeductionDTO();
                    $userDTO->setUserId($user_id);
                    $userDTO->setChildId(0);
                    $userDTO->setBusinessId(0);
                    $userDTO->setMethod(2);
                    $userDTO->setType(2);
                    $userDTO->setRemarks('购买花田');
                    $userDTO->setAmount($sapling->recommend_price);
                    $userDTO->setStatus(1);
                    $userDTO->setCoinId(0);
                    $result = $userGiveAwayService->changeUserAmount($userDTO);
                } else {
                    $coin = CoinModel::GetById($sapling->coin_id);
                    $userAmount = getUserAmountByCoin($user_id, $coin->Id);
                    if ($userAmount->Money < $sapling->recommend_price) {
                        throw new ArException(ArException::SELF_ERROR, '您的余额不足');
                    }
                    //扣钱
                    DB::table('MemberCoin')->where('MemberId', $user_id)->where('CoinId', $coin->Id)->update([
                        'Money' => DB::raw("Money-{$sapling->recommend_price}")
                    ]);
                    //添加账单记录
                    self::AddLog($user_id, (-$sapling->recommend_price), $coin, 'buy_sapling');
                }

                $type = 2;
                if ($sapling->is_shop_sapling == 1) {
                    $type = 7;
                }
                $sn = new Snowflake();
                $data = [
                    'id' => $sn->nextId(),
                    'user_id' => $user_id,
                    'sapling_id' => $sapling_id,
                    'total_amount' => $sapling->total_profit,
                    'type' => $type,
                    'yield' => bcdiv($sapling->total_profit, ($sapling->cycle + basieEvent()), 4),
                    'total_price' => $sapling->recommend_price,
                    'rate_of_return' => $sapling->rate_of_return,
                    'surplus_amount' => $sapling->total_profit,
                    'total_freed' => ($sapling->cycle + basieEvent()),
                    'computing_power' => $sapling->computing_power,
                    'freed' => $sapling->cycle + basieEvent(),
                    'is_experience' => $sapling->is_experience,
                    'is_superior_reward' => $sapling->is_superior_reward,
                    // 'begin_receive_time' => date("Y-m-d 00:00:00", strtotime("+1 day")),//明天开始时间
                    'begin_receive_time' => dateFormat(),
                    'release_complete_time' => dateFormat(strtotime('+' . $sapling->cycle . 'day')),
                    'remarks' => '自主购买',
                    'create_time' => dateFormat()
                ];
                DB::table('miner_user_sapling')->insert($data);

                //增加算力
                $userComputingPowerService = new MinerUserComputingPowerService();
                $userComputingPowerService->add($user_id, $sapling->computing_power, $data['id'], $type, 1,
                    $data['begin_receive_time'], $data['release_complete_time'], '自主购买');

                //增加奖励(已废弃)
                $this->referralReward($user_id, $sapling->price);

                //埋点信息
                (new MinerUserTeamInfoService())->exchangePeopleCount($user_id, $sapling->computing_power);

                return true;
            });
        }, 3, 5);
    }


    /**
     * 计算所有树苗的总售价
     * @param $user_id
     * @return int
     * @throws ArException
     */
    public function userTotalProfit($user_id)
    {
        $userSaplingList = $this->userSaplingList($user_id);
        $totalProfit = 0;
        foreach ($userSaplingList as $item) {
            $userSaplingDetails = ($this->userSaplingDetail($user_id, $item->user_sapling_id));
            if (array_key_exists('total_price', $userSaplingDetails)) {
                $totalProfit += $userSaplingDetails->total_price;
            }
        }
        return $totalProfit;
    }

    /**
     * 购买树苗后上级奖励
     * @param $user_id
     * @param $amount
     * @return bool
     * @throws ArException
     */
    public function referralReward($user_id, $amount)
    {
        if (!bccomp($amount, '0', 4) == 1) {
            return;
        }
        $user = DB::table('Members')->find($user_id);
        if (empty($user->ParentId)) {
            return true;
        }

        $proportion = 0.03; //默认
        $coin = Coin::GetByEnName();
        $price = bcmul($amount, $proportion, 4);
        DB::table('MemberCoin')->where('MemberId', $user->ParentId)->where('CoinId', $coin->Id)->update([
            'Money' => DB::raw("Money+{$price}")
        ]);

        //记录直推邀请的EB
        DB::table('Members')->where('Id', $user->ParentId)->update([
            'invite_amount' => DB::raw("invite_amount+{$price}")
        ]);

        $this->AddLog($user->ParentId, $price, $coin, 'buy_sapling_reward');
    }

    /**
     * 获取树苗的收益
     * @param $user_id
     * @param $day_user_sapling_id
     * @return array|Collection
     * @throws ArException
     */
    public function getUserSaplingIncome($user_id, $day_user_sapling_id)
    {
        $details = $this->userSaplingDetail($user_id, $day_user_sapling_id);
        if (empty($details)) {
            return [];
        }
        if (!$details->is_watering) { //还未浇水不展示具体
            return [];
        }
        $day = getDay();
        $list = DB::table('miner_user_sapling_receive')->where([
            ['user_id', '=', $user_id],
            ['user_day_sapling_id', '=', $details->day_user_sapling_id],
            ['is_receive', '=', 0]
        ])->whereBetween('create_time', [dateFormat($day['beginTime']), dateFormat($day['endTime'])])
            ->select(['id', 'user_id', 'user_sapling_id', 'user_day_sapling_id', 'amount', 'is_receive'])
            ->get();
        foreach ($list as $item) {
            $item->id .= '';
            $item->user_sapling_id .= '';
            $item->user_day_sapling_id .= '';
        }
        return $list;
    }

    /**
     * 是否需要发放奖励
     * @param $user_id
     * @return bool
     */
    public function isUserShareReward($user_id)
    {
        $parent_count = DB::table('Members')->where('ParentId', $user_id)->count('Id') ?? 0;
        $userComputingPowerService = new MinerUserComputingPowerService();
        $user_computing_power = $userComputingPowerService->userComputingPower($user_id) ?? 0;
        $list = DB::table('miner_sapling_share_reward')->where([
            ['is_disable' => 0],
            ['is_delete' => 0]
        ])->orderBy('id', 'desc')->get();
        $share_reward = null;
        foreach ($list as $item) {
            if ($parent_count >= $item->direct_push && $user_computing_power >= $item->computing_power) {
                $share_reward = $item;
                break;
            }
        }
        if ($share_reward) {
            $is_reward = DB::table('miner_sapling_share_reward_record')->where([
                ['user_id', '=', $user_id],
                ['user_id', '=', $user_id],
            ])->first();
            if ($is_reward) {
                return null;
            }
            return $share_reward;
        }
    }

    /**
     * 用户花生田释放(定时任务)
     * @param $user_id
     * @return bool
     * @throws \Throwable
     */
    public function dayUserSaplingRelease($user_id)
    {
        echo "开始释放花生田,用户编号:{$user_id}...\n";
        try {
            $userSaplingList = $this->userSaplingList($user_id);
            if (empty($userSaplingList)) {
                return true;
            }
            $sn = new Snowflake();
            $day = getDay();

            $totalAmount = 0;
            foreach ($userSaplingList as $item) {

                $sapling = $this->userSaplingDetail($user_id, $item->user_sapling_id);
                if (empty($sapling)) {
                    continue;
                }

                //每日只产生一次
                $is_day_sapling_out = DB::table('miner_user_sapling_release')->where([
                    ['user_id', '=', $sapling->user_id],
                    ['user_sapling_id', '=', $sapling->user_sapling_id]
                ])->whereBetween('create_time', [dateFormat($day['beginTime']), dateFormat($day['endTime'])])->count('id');
                if ($is_day_sapling_out >= 1) {
                    continue;
                }

                //没有剩余天数
                if ($sapling->freed < 1 || $sapling->surplus_amount <= 0) {
                    //释放完成
                    DB::table('miner_user_sapling')->where('id', $sapling->user_sapling_id)->update([
                        'is_release_complete' => 1,
                        'is_disable' => 1,
                        'is_delete' => 1,
                        'release_complete_time' => dateFormat()
                    ]);
                    continue;
                }

                $data = [];
                DB::transaction(function () use ($sapling, $sn, $day, $item, $data, &$totalAmount) {

                    //更新树苗本身
                    if ($sapling->surplus_amount < $sapling->yield) {
                        DB::table('miner_user_sapling')->where('id', $sapling->user_sapling_id)->update([
                            'freed' => DB::raw('freed - 1'),
                            'release_amount' => DB::raw('release_amount + ' . $sapling->yield),
                            'surplus_amount' => 0,
                            'update_time' => dateFormat()
                        ]);
                    } else {
                        DB::table('miner_user_sapling')->where('id', $sapling->user_sapling_id)->update([
                            'freed' => DB::raw('freed - 1'),
                            'release_amount' => DB::raw('release_amount + ' . $sapling->yield),
                            'surplus_amount' => DB::raw('surplus_amount - ' . $sapling->yield),
                            'update_time' => dateFormat()
                        ]);
                    }


                    $data['id'] = $sn->nextId();
                    $data['user_id'] = $sapling->user_id;
                    $data['user_sapling_id'] = $item->user_sapling_id;
                    $data['sapling_id'] = $sapling->sapling_id;
                    $data['amount'] = $sapling->yield;
                    $data['total_count'] = 7;
                    $data['surplus_count'] = 7;
                    $data['is_give_away'] = $sapling->is_gave_away;
                    $data['begin_receive_time'] = dateFormat($day['beginTime']);
                    $data['expire_time'] = dateFormat($day['endTime']);
                    $data['create_time'] = dateFormat();
                    $data['is_out_put'] = 1;
                    $totalAmount += $data['amount'];
                    DB::table('miner_user_sapling_release')->insert($data);

                });
            }

            if ($totalAmount > 0) {
                $dayTime = getDay();
                $dayArray = DateUtil::randomDate(dateFormat(time()), dateFormat($dayTime['endTime']), 1, false);
                $dayArray[0] = time() + 30;//测试时 30秒到仓库
                $userTotalReleaseService = new  MinerUserSaplingTotalReleaseService();
                $userTotalReleaseService->initTotalRelease($user_id, $totalAmount, time() + 60);
//                $second = $dayArray[0] - time();
//                $second = $second < 1 ? 60 : $second;
//                $second = rand(60, 21600);
//                dispatch(new UserIncomeJob($user_id))->delay(now()->addSeconds($second))->onQueue(Enums::QUEUE_NAME['release']);
                $userSaplingTotalReleaseService = new MinerUserSaplingTotalReleaseService();
                $userSaplingTotalReleaseService->cumulativeIncome($user_id);
            }

        } catch (\Exception $exception) {
            logger("释放花田出错,具体原因：{$exception->getMessage()}");
        }
    }

    /**
     * 计算树苗显示状态
     * @param $total_amount
     * @param $release_amount
     * @return int|mixed
     */
    private function calculationSaplingStatus($total_amount, $release_amount)
    {
        $level = 3; //最高状态
        $data = [1, 2, 3];
        $total = $total_amount;
        $tem = $release_amount;
        if ($total <= 0 || $tem <= 0) {
            return 1;
        }
        $status = $data[(ceil(($tem / ($total / $level))) - 1)];
        return $status > $level ? $level : $status;
    }
}
