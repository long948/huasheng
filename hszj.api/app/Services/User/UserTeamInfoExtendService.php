<?php


namespace App\Services\User;


use App\Utils\Snowflake;
use Illuminate\Support\Facades\DB;

class UserTeamInfoExtendService
{
    /**
     * 初始化数据贡献相关信息
     * @param $userId
     * @return bool
     * @throws \Exception
     */
    public function init($userId)
    {
        $isExists = DB::table('miner_user_team_info_extend')->where('user_id', $userId)->exists();
        if ($isExists) {
            return false;
        }
        return DB::table('miner_user_team_info_extend')->insert([
            'id' => (new Snowflake())->nextId(),
            'user_id' => $userId
        ]);
    }

    /**
     * @param $userId
     */
    public function find($userId)
    {
        $isExists = DB::table('miner_user_team_info_extend')->where('user_id', $userId)->first();
        if (!$isExists) {
            $this->init($userId);
            $isExists = DB::table('miner_user_team_info_extend')->where('user_id', $userId)->first();
        }
        return $isExists;
    }

    /**
     * 更新
     * @param $userId
     * @param $is_existing_direct_push_count
     * @param $is_exchange_people_count
     */
    public function update($userId, $is_existing_direct_push_count, $is_exchange_people_count)
    {
        DB::table('miner_user_team_info_extend')->where('user_id', $userId)->update([
            'is_existing_direct_push_count' => $is_existing_direct_push_count,
            'is_exchange_people_count' => $is_exchange_people_count
        ]);
    }
}
