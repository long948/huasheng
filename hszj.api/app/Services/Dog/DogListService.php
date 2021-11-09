<?php


namespace App\Services\Dog;


use App\Models\CoinModel;
use App\Models\Dog\DogList;
use App\Models\Mouse\MouseList;
use App\Services\Service;

class DogListService extends Service
{

    /**
     * @param $userId
     * @return Collection
     */
    public function list($userId)
    {
        return DogList::query()->get()->each(function (&$key, $val) use ($userId) {
            $coin = CoinModel::GetById($key->coin_id);
            $userAmountInfo = getUserAmountByCoin($userId, $coin->Id);
            $userAmount = $userAmountInfo->Money;
            /**
             * @var $key DogList
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
        return DogList::query()->get();
    }

    /**
     * @param $dogId
     * @return Model|null
     */
    public function find($dogId)
    {
        return DogList::query()->find($dogId);
    }

}
