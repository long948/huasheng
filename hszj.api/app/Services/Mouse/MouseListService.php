<?php


namespace App\Services\Mouse;


use App\Models\CoinModel;
use App\Models\Mouse\MouseList;
use App\Services\Service;

class MouseListService extends Service
{

    /**
     * @param $userId
     * @return Collection
     */
    public function list($userId)
    {

        return MouseList::query()->get()->each(function (&$key, $val) use ($userId) {
            $coin = CoinModel::GetById($key->coin_id);
            $userAmountInfo = getUserAmountByCoin($userId, $coin->Id);
            $userAmount = $userAmountInfo->Money;
            /**
             * @var $key MouseList
             */
            $proportion = bcdiv($userAmount, $key->price, 4);
            $key->proportion = $proportion > 1 ? 1 : $proportion;
            $key->payCoinName = $coin->Name;
            $key->payCoinLogo = $coin->Logo;
            $key->payCoinId = $coin->Id;
        });
    }

    /**
     * @param $userId
     * @return Collection
     */
    public function userList($userId)
    {
        return MouseList::query()->get();
    }


    /**
     * @param $mouseId
     * @return Model|null
     */
    public function find($mouseId)
    {
        return MouseList::query()->find($mouseId);
    }

}
