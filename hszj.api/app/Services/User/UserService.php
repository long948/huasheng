<?php


namespace App\Services\User;


use App\Exceptions\ArException;
use App\Models\CoinModel as Coin;
use App\Services\Dog\DogUserService;
use App\Services\MinerUserLevelService;
use App\Services\MinerUserSaplingService;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class UserService extends Service
{
    /**
     * @var MinerUserLevelService
     */
    private $levelService;

    /**
     * @var MinerUserSaplingService
     */
    private $userSaplingService;

    /**
     * @var UserTotalIncomeService
     */
    private $userTotalIncomeService;

    /**
     * @var DogUserService
     */
    private $userDogService;

    /**
     * UserService constructor.
     * @param MinerUserLevelService $levelService
     * @param MinerUserSaplingService $userSaplingService
     * @param UserTotalIncomeService $userTotalIncomeService
     * @param DogUserService $userDogService
     */
    public function __construct(MinerUserLevelService $levelService,
                                MinerUserSaplingService $userSaplingService,
                                UserTotalIncomeService $userTotalIncomeService,
                                DogUserService $userDogService)
    {
        $this->levelService = $levelService;
        $this->userSaplingService = $userSaplingService;
        $this->userTotalIncomeService = $userTotalIncomeService;
        $this->userDogService = $userDogService;
    }


    /**
     * 用户土地首页
     * @param $userId
     * @param int $type 1普通土地 2逍遥王土地
     * @return array
     * @throws \Exception
     */
    public function index($userId, $type = 1)
    {
        $result = [];

        if ($type == 1) {
            $list = $this->userSaplingService->userSaplingList($userId);
            $team = [];
            foreach ($list as $item) {
                if ($item->type != 7) {
                    $team[] = $item;
                }
            }
            //用户树苗
            $result['sapling'] = $team;
        }
        if ($type == 2) {
            //用户树苗
            $result['sapling'] = $this->userSaplingService->userSaplingXiaoYaoWang($userId);
        }

        $userInfo = DB::table('members')->where('id', $userId)->select(['Avatar', 'NickName'])->first();
        if ($userInfo->Avatar) {
            $userInfo->Avatar = $this->QiniuDomain() . $userInfo->Avatar;
        }
        $result['user']['info'] = $userInfo;

        //用户等级
        $result['user']['level'] = $this->levelService->getUserLevel($userId);

        $result['user']['balance'] = $this->userTotalIncomeService->getUserAmount($userId);
        $result['user']['current'] = bcadd(DB::table('miner_user_sapling')->where('user_id', $userId)->sum('release_amount'), 0);
        $result['user']['max_current'] = bcadd(DB::table('miner_user_sapling')->where('user_id', $userId)->sum('total_amount'), 0);
        $result['user']['proportion'] = min(1, bcdiv($result['user']['current'], $result['user']['max_current'] ?: 1, 4));
        $result['user']['dog'] = $this->userDogService->getStandGuard($userId);
        $result['other']['rule'] = DB::table('setting')->where('k', 'sapling_rule')->value('v') ?? '暂无规则';
        return $result;
    }
}
