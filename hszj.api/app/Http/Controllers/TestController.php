<?php

namespace App\Http\Controllers;

use App\Common\DTO\UserDeductionDTO;
use App\Events\PushMsgEvent;
use App\Exceptions\ArException;
use App\Http\Controllers\Shop\ShopRewardService;
use App\Models\CoinModel as Coin;
use App\Models\SettingModel;
use App\Models\User\UserTotalIncome;
use App\Services\Dog\DogUserService;
use App\Services\MinerUserLevelService;
use App\Services\Shop\ShopTeamFoundService;
use App\Services\SmsService;
use App\Services\User\MinerUserSaplingTotalReleaseService;
use App\Services\User\UserTotalIncomeService;
use App\Utils\CreateReward;
use App\Utils\RedisLock;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Self_;


/**
 * 测试
 * Class TestController
 * @package App\Http\Controllers
 */
class TestController extends Controller
{

    /**
     * @var Request
     */
    private $request;

    /**
     * @var DogUserService
     */
    private $dogUserService;


    /**
     * @var MinerUserLevelService
     */
    private $userLevelService;

    /**
     * @var ShopRewardService
     */
    private $shopRewardService;

    /**
     * TestController constructor.
     * @param Request $request
     * @param DogUserService $dogUserService
     * @param MinerUserLevelService $userLevelService
     * @param ShopRewardService $shopRewardService
     */
    public function __construct(Request $request, DogUserService $dogUserService, MinerUserLevelService $userLevelService, ShopRewardService $shopRewardService)
    {
        $this->request = $request;
        $this->dogUserService = $dogUserService;
        $this->userLevelService = $userLevelService;
        $this->shopRewardService = $shopRewardService;
    }


    public function test()
    {
        $tes = RedisLock::lock('test', function () {
            throw  new ArException(ArException::TOKEN_ERROR);
        });
        return self::success($tes);
    }


    public function test1()
    {

        $sql = 'SELECT Id,Address,Status,Hash,1 as Type,Money,CoinName,AddTime FROM recharge where Status = 1 UNION ALL SELECT Id,Address,Status,Hash,2 as Type,Money,FeeCoinEname as CoinName,AddTime FROM withdraw;';
        $builder = DB::table(DB::raw($sql));
        $data = $builder->paginate(10);
//        self::success($data);

        $userId = [1, 1001,
            1002,
            1003,
            1004,
            1005,
            1006,
            1007,
            1008,
            1009,
            1010,
            1011,
            1012,
            1013,
            1014,
            1015,
            1016,
            1017,
            1018,
            1019,
            1020,
            1021,
            1022,
            1023,
            1024,
            1025,
            1026,
            1027,
            1028,
            1029,
            1030,
            1031,
            1032,
            1033,
            1034,
            1035,
            1036,
            1037,
            1038,
            1039,
            1040,
            1041,
            1042,
            1043,
            1044,
            1045,
            1046,
            1047,
            1048,
            1049,
            1050
        ];
        $foundService = new ShopTeamFoundService();
        $goodId = $this->request->input('goodId', 1);
        $userNote = $this->request->input('userNote', 1);
        $activityId = $this->request->input('activityId', 1);
        $spikeId = $this->request->input('spikeId', 1);
        $teamType = 2;

        $result = [];
        foreach ($userId as $item) {
            $result[] = $foundService->specialOpenActivity($item, $goodId, $userNote, $activityId, $spikeId, $teamType);
        }
        self::success($result);

    }

    public function missingMethod($parameters = array())
    {
        throw new ArException(ArException::SELF_ERROR, '请求错误');
    }
}
