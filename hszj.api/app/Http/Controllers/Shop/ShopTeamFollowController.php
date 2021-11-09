<?php


namespace App\Http\Controllers\Shop;


use App\Http\Controllers\Controller;
use App\Services\Shop\ShopTeamFollowService;
use App\Services\Shop\ShopTeamFoundService;
use Illuminate\Http\Request;

class ShopTeamFollowController extends Controller
{

    /**
     * @var ShopTeamFoundService
     */
    private $teamFoundService;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ShopTeamFollowService
     */
    private $teamFollowService;

    /**
     * ShopTeamFollowController constructor.
     * @param ShopTeamFoundService $teamFoundService
     * @param Request $request
     * @param ShopTeamFollowService $teamFollowService
     */
    public function __construct(ShopTeamFoundService $teamFoundService,
                                Request $request,
                                ShopTeamFollowService $teamFollowService)
    {
        $this->teamFoundService = $teamFoundService;
        $this->request = $request;
        $this->teamFollowService = $teamFollowService;
    }


    /**
     * 我的拼购记录
     */
    public function follows()
    {
        $userId = $this->request->get('uid');
        $status = $this->request->input('status');
        $page = $this->request->input('page');
        $limit = $this->request->input('limit');
        $searchName = $this->request->input('search');
        return $this->success($this->teamFollowService->userFollow($userId, $status, $page, $limit, $searchName));
    }


    /**
     * 我的拼购详情
     */
    public function details()
    {
        $userId = $this->request->get('uid');
        $followId = $this->request->input('followId');
        return $this->success($this->teamFollowService->userFollowDetails($userId, $followId));
    }


    /**
     * 我的拼购邀请好友拼单
     */
    public function collages()
    {
        $foundId = $this->request->input('foundId');
        return $this->success($this->teamFoundService->userFoundCollages($foundId));
    }

}
