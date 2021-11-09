<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// 文章列表
class ArticleListController extends Controller
{

    /**
     * @OA\Get(
     *     path="/article-list",
     *     operationId="/article-list",
     *     tags={"文章"},
     *     summary="文章列表",
     *     description="文章列表",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/keyword"),
     *     @OA\Parameter(ref="#/components/parameters/ArticleCallIndex"),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Parameter(ref="#/components/parameters/count"),
     * )
     */
    public function lists(Request $request){
        $CallIndex = $request->input('CallIndex');
        $keyword = $request->input('keyword');
        $page = intval($request->input('page', 1));
        $count = intval($request->input('count', 20));
        $offset = $page <= 0 ? 1 : $page;
        $limit = $count > 20 || $count <= 0 ? 20 : $count;
        $offset = ($offset - 1) * $limit;

        $where = [];
        $where[] = ['IsDel','=',0];
        if ($CallIndex) {
            $where[] = ["ArticleCallIndex", "=", $CallIndex];
        }
        if ($keyword) {
            $where[] = ["ArticleTitle", "like", '%'.$keyword.'%'];
        }

        $list = DB::table("ArticleList")
            ->where($where)
            ->offset($offset)
            ->limit($limit)
            ->orderBy("IsStick", "DESC")
            ->orderBy("Sort", "DESC")
            ->get();
        $domain = "";
        if (count($list) > 0) {
            $qiniu = DB::table('QiniuConfig')->first();
            if(!empty($qiniu)) {
                $domain = $qiniu->Domain;
            }
        }

        foreach ($list as $v) {
            $ImgList = explode(",", $v->ImgList);
            $temp = [];
            foreach ($ImgList as $pic_key => $pic) {
                if ($pic) {
                    $temp[] = $domain . $pic;
                }
            }
            $v->ImgList = $temp;
            $v->MainImg = !empty($v->MainImg) ? $domain . $v->MainImg : "";
        }

        self::success($list);
    }

    /**
     * @OA\Get(
     *     path="/article-index",
     *     operationId="/article-index",
     *     tags={"文章"},
     *     summary="文章索引",
     *     description="文章索引",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     )
     * )
     */
    public function index(Request $request){
        $index = DB::table('ArticleType')->get();

        self::success($index);
    }


    /**
     * @OA\Get(
     *     path="/article-list-detail",
     *     operationId="/article-list-detail",
     *     tags={"文章"},
     *     summary="文章明细",
     *     description="文章明细",
     *     @OA\Response(
     *         response=200,
     *         description="操作成功",
     *         @OA\JsonContent(ref="#/components/schemas/success")
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/ArticleId"),
     * )
     */
    public function detail(Request $request){
        $id = intval($request->input('Id'));

        DB::table("ArticleList")
            ->where("Id", $id)
            ->increment("ReadNum");

        $model = DB::table("ArticleList")
            ->where("Id", $id)
            ->first();

        self::success($model);
    }

}
