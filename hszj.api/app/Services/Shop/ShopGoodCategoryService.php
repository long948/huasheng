<?php


namespace App\Services\Shop;


use App\Services\Service;
use Illuminate\Support\Facades\DB;

class ShopGoodCategoryService extends Service
{

    public function category()
    {
        return DB::table('shop_good_category')
            ->where('is_show', 1)
            ->where('level', 1)
            ->select('id', 'name')->get();
    }


}
