<?php


namespace App\Http\Controllers\System;


use App\Http\Controllers\Controller;
use App\Services\System\MembersSignService;
use Illuminate\Http\Request;

class MembersSignController extends Controller
{

    /**
     * @var Request
     */
    private $request;

    /**
     * @var MembersSignService
     */
    private $memberSignService;

    /**
     * MembersSignController constructor.
     * @param Request $request
     * @param MembersSignService $memberSignService
     */
    public function __construct(Request $request, MembersSignService $memberSignService)
    {
        $this->request = $request;
        $this->memberSignService = $memberSignService;
    }


    /**
     * 签到
     */
    public function sign()
    {
        $user_id = $this->request->get('uid');
        $key = $this->request->input('key');
        self::success($this->memberSignService->sign($user_id, $key));
    }

}
