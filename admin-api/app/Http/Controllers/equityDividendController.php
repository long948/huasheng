<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class equityDividendController extends Controller
{
    /**
     * @var $request Request
     */
    private $request;

    /**
     * equityDividendController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    public function equityDividend()
    {
        $where = [];
        $count = intval($this->request->input('limit', 20));
        $Phone = $this->request->input('Phone');
        if (!empty($Phone)) {
            $where[] = ["Phone", "like", "%$Phone%"];
        }
        $NickName = $this->request->input('NickName');
        if (!empty($NickName)) {
            $where[] = ["m.NickName", "like", "%$NickName%"];
        }
        $lists = DB::table("other_equity_dividend")
            ->leftjoin('members as m', 'm.Id', '=', 'other_equity_dividend.user_id')
            ->where($where)
            ->select('other_equity_dividend.*', 'm.NickName', 'm.Phone')
            ->orderBy('id', 'desc')
            ->paginate($count);
        
        $res = [];
        $res["total"] = $lists->total();
        $res["list"] = $lists->items();
        return self::returnMsg($res);
    }
}
