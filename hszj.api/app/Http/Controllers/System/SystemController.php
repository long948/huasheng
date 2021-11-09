<?php
/**
 * Description：
 *
 * User: admin
 * Date: 2020/11/15
 * Time: 15:00
 */

namespace App\Http\Controllers\System;


use App\Common\DTO\UserDeductionDTO;
use App\Exceptions\ArException;
use App\Http\Controllers\Controller;
use App\Models\CoinModel as Coin;
use App\Models\SettingModel;
use App\Services\MemberService;
use App\Services\System\SystemService;
use App\Services\UserGiveAwayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{

    /**
     * @var Request
     */
    private $request;

    /**
     * @var SystemService
     */
    private $systemService;


    /**
     * SystemController constructor.
     * @param Request $request
     * @param SystemService $systemService
     */
    public function __construct(Request $request, SystemService $systemService)
    {
        $this->request = $request;
        $this->systemService = $systemService;
    }


    /**
     * 获取解冻金额
     */
    public function frozenAmount()
    {
        self::success(SettingModel::getValueByKey('unfrozen_amount'));
    }


    /**
     *  解冻
     */
    public function userFrozen()
    {
        $user_id = $this->request->get('uid');
        //1 花生 2备用斤
        $type = $this->request->input('type');
        self::success($this->systemService->userFrozen($user_id, $type));
    }

    
    /**
     * 下载和分享链接
     */
    public function appAndShareInfo()
    {
        self::success([
            'shareUrl' => SettingModel::getValueByKey('share_url') ?? '',
            'androidUrl' => SettingModel::getValueByKey('android_url') ?? '',
            'iosUrl' => SettingModel::getValueByKey('ios_url') ?? ''
        ]);
    }

}
