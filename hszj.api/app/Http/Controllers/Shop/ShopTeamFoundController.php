<?php


namespace App\Http\Controllers\Shop;


use App\Exceptions\ArException;
use App\Http\Controllers\Controller;
use App\Models\Shop\TeamActivity;
use App\Models\Shop\TeamFound;
use App\Services\Shop\ShopGoodService;
use App\Services\Shop\ShopTeamFollowService;
use App\Services\Shop\ShopTeamFoundService;
use Illuminate\Http\Request;

class ShopTeamFoundController extends Controller
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
     * ShopTeamFoundController constructor.
     * @param ShopTeamFoundService $teamFoundService
     * @param Request $request
     * @param ShopTeamFollowService $teamFollowService
     */
    public function __construct(ShopTeamFoundService $teamFoundService,
                                Request $request,
                                ShopTeamFollowService $teamFollowService
    )
    {
        $this->teamFoundService = $teamFoundService;
        $this->request = $request;
        $this->teamFollowService = $teamFollowService;
    }


    /**
     * 开团
     * @throws \Exception
     */
    public function openActivity()
    {
        $goodId = $this->request->input('goodId');
        $userNote = $this->request->input('userNote');
        $activityId = $this->request->input('activityId');
        $spikeId = $this->request->input('spikeId');
        $teamType = 2;
        $userId = $this->request->get('uid');
        $goodService = new ShopGoodService();
        $activity = TeamActivity::query()->where('activity_id', $activityId)->first();
        if (empty($activity)) {
            throw new ArException(ArException::SHOP_FOUND_NOT_EXISTS);
        }
        $open = $goodService->open($goodId, $userId, $activity);
        if (!empty($open)) {
            self::returnMsg('您还有未支付开团的订单', 50007, $open);
        }

        $open = $goodService->openIsFound($goodId, $userId, $activity);
        if (!empty($open)) {
            self::returnMsg('您还有未完成的团', 50008, $open);
        }

        return $this->success($this->teamFoundService->specialOpenActivity($userId, $goodId, $userNote, $activityId, $spikeId, $teamType));
    }


    /**
     * 参与拼购
     */
    public function ginsengFound()
    {
        $foundId = $this->request->input('foundId');
        $userNote = $this->request->input('userNote');

        $userId = $this->request->get('uid');

        $found = TeamFound::query()->where('found_id', $foundId)->first();
        if (empty($found)) {
            throw new ArException(ArException::SHOP_FOUND_NOT_EXISTS);
        }
        $goodService = new ShopGoodService();
        $activity = TeamActivity::query()->where('activity_id', $found->activity_id)->first();
        if (empty($activity)) {
            if (empty($activity)) {
                throw new ArException(ArException::SHOP_FOUND_NOT_EXISTS);
            }
        }

        $join = $goodService->join($found->good_id, $userId, $activity);
        if (!empty($join)) {
            self::returnMsg('您还有未支付参团订单', 50007, $join);
        }

        $open = $goodService->openIsFound($found->good_id, $userId, $activity);
        if (!empty($open)) {
            self::returnMsg('您还有未完成的团', 50008, $open);
        }

        return $this->success($this->teamFoundService->ginsengActivity($userId, $foundId, $userNote));
    }


    /**
     * 参团成员
     */
    public function followPeoples()
    {
        $foundId = $this->request->input('foundId');
        return $this->success($this->teamFollowService->followPeoples($foundId));
    }


    /**
     * 支付后查看团信息
     */
    public function foundPyNotify()
    {
        $orderSn = $this->request->input('orderSn');
        return $this->success(['found' => $this->teamFoundService->foundPyNotify($orderSn)]);
    }


    /**
     * 根据口令获取团编号
     */
    public function getFoundByCode()
    {
        $code = $this->request->input('code');
        $foundId = $this->teamFoundService->getFoundByInvitationCode($code);
        if (!empty($foundId)) {
            $result = $this->teamFoundService->userFoundCollages($foundId);
            return $this->success($result);
        }
        throw new ArException(ArException::SELF_ERROR, '口令无效');
    }

}
