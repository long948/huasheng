<?php


namespace App\Http\Controllers;


use App\Exceptions\ArException;
use App\Services\MinerUserLevelService;
use App\Services\MinerSaplingService;
use App\Services\MinerUserSaplingService;
use App\Utils\RedisLock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * 等级管理
 * Class MinerLevelController
 * @package App\Http\Controllers
 */
class MinerUserLevelController extends Controller
{

    /**
     * @var Request
     */
    private $request;

    /**
     * @var MinerUserLevelService
     */
    private $minerLevelService;

    /**
     * MinerUserLevelController constructor.
     * @param Request $request
     * @param MinerUserLevelService $minerLevelService
     */
    public function __construct(Request $request,
                                MinerUserLevelService $minerLevelService)
    {
        $this->request = $request;
        $this->minerLevelService = $minerLevelService;
    }


    /**
     * 获取等级列表
     */
    public function levelList()
    {
        $user_id = $this->request->get('uid');
        self::success($this->minerLevelService->levelList($user_id));
    }

    /**
     * 获取用户等级
     */
    public function getUserLevel()
    {
        $user_id = $this->request->get('uid');
        self::success($this->minerLevelService->getUserLevel($user_id));
    }

}
