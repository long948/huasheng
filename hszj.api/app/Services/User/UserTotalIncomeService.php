<?php


namespace App\Services\User;


use App\Common\DTO\UserDeductionDTO;
use App\Exceptions\ArException;
use App\Models\CoinModel as Coin;
use App\Models\User\UserTotalIncome;
use App\Services\Bill\BillUserTotalIncomeService;
use App\Services\MemberService;
use App\Services\Service;
use App\Services\UserGiveAwayService;
use App\Utils\Enum\Enums;
use App\Utils\RedisLock;
use App\Utils\Snowflake;
use Illuminate\Support\Facades\DB;

/**
 * 用户总收益
 * Class UserTotalIncomeService
 * @package App\Service\User
 */
class UserTotalIncomeService extends Service
{

    /**
     * 用户总收益信息
     * @param $userId
     * @return UserTotalIncome
     */
    public function find($userId)
    {
        return UserTotalIncome::query()->where('user_id', $userId)->first();
    }


    /**
     * @return Collection
     */
    public function list()
    {
        return UserTotalIncome::query()->where('amount', '>', 0)->select(['id', 'user_id', 'amount'])->get();
    }

    /**
     * 获取用户总收益
     * @param $userId
     * @return float
     * @throws \Exception
     */
    public function getUserAmount($userId): float
    {
        $userTotalIncome = $this->find($userId);
        if (empty($userTotalIncome)) {
            $this->initUserTotalIncome($userId);
            return 0;
        }
        if ($userTotalIncome) {
            return $userTotalIncome->amount;
        }
        return 0;
    }


    /**
     * 余额变动
     * @param UserDeductionDTO $amountDTO
     * @return bool
     * @throws ArException
     * @throws \Throwable
     */
    public function changeUserAmount(UserDeductionDTO $amountDTO)
    {
        $userAmount = UserTotalIncome::query()->where('user_id', $amountDTO->getUserId())->first();
        if (empty($userAmount)) {
            $this->initUserTotalIncome($amountDTO->getUserId());
            $userAmount = $this->find($amountDTO->getUserId());
        }
        if ($amountDTO->getMethod() == 1) { //进账
            $userAmount->before_amount = $userAmount->amount;
            $userAmount->amount += $amountDTO->getAmount();
            $userAmount->after_amount += $amountDTO->getAmount();
        }

        if ($amountDTO->getMethod() == 2) { //出账
            if (bccomp($userAmount->amount, $amountDTO->getAmount(), 5) < 0) {
                throw new ArException(ArException::SELF_ERROR, '余额不足');
            }
            $userAmount->before_amount = $userAmount->amount;
            $userAmount->amount -= $amountDTO->getAmount();
            $userAmount->after_amount -= $amountDTO->getAmount();
        }
        return DB::transaction(function () use ($userAmount, $amountDTO) {
            if ($userAmount->save()) {
                $billUserTotalIncomeService = new BillUserTotalIncomeService();
                return $billUserTotalIncomeService->addIncome($amountDTO);
            }
            return false;
        });
    }


    /**
     * 转出花生米到账户
     * @param $user_id
     * @param $amount
     * @return bool
     * @throws \Exception
     * @throws \Throwable
     */
    public function transfer($user_id, $amount)
    {
        return RedisLock::lock(Enums::REDIS_LOCK_KEY['USER_TRANSFER_HSM'] . $user_id, function () use ($user_id, $amount) {

            $userDeductionDTO = new UserDeductionDTO();
            $userDeductionDTO->setUserId($user_id);
            $userDeductionDTO->setChildId(0);
            $userDeductionDTO->setBusinessId(0);
            $userDeductionDTO->setMethod(2);
            $userDeductionDTO->setType(4);
            $userDeductionDTO->setRemarks('转出至账户');
            $userDeductionDTO->setAmount($amount);
            $userDeductionDTO->setStatus(1);
            $userDeductionDTO->setCoinId(0);

            return DB::transaction(function () use ($userDeductionDTO, $user_id) {
                $isPrincipal = getUserIsPrincipal($user_id);

                $memberCoinAmount = $userDeductionDTO->getAmount();
                $thisCoin = Coin::GetByEnName();

                //当用户交易额还没达到对应金额时 直接按照比例放入赠送金额账户
                if (!$isPrincipal) {
                    $memberCoinAmount = bcmul($userDeductionDTO->getAmount(), 0.1, 4);
                    $userGiveAwayService = new UserGiveAwayService();
                    $userGiveDTO = new UserDeductionDTO();
                    $userGiveDTO->setUserId($user_id);
                    $userGiveDTO->setChildId(0);
                    $userGiveDTO->setBusinessId(0);
                    $userGiveDTO->setStatus(1);
                    $userGiveDTO->setAmount(bcsub($userDeductionDTO->getAmount(), $memberCoinAmount, 4));
                    $userGiveDTO->setType(1);
                    $userGiveDTO->setMethod(1);
                    $userGiveDTO->setRemarks('花田本金');
                    $userGiveDTO->setCoinId(0);
                    $userGiveAwayService->changeUserAmount($userGiveDTO);
                }

                $coins = DB::table('Coin')->get();
                foreach ($coins as $coin) {
                    $coinExists = DB::table('MemberCoin')->where('MemberId', $userDeductionDTO->getUserId())->where('CoinId', $coin->Id)->exists();
                    if ($coinExists) {
                        continue;
                    }
                    DB::table('MemberCoin')->insert([
                        'CoinId' => $coin->Id,
                        'CoinName' => $coin->EnName,
                        'MemberId' => $userDeductionDTO->getUserId(),
                        'Address' => (new MemberService())->generate_str(),
                    ]);
                }

                DB::table('MemberCoin')->where('MemberId', $userDeductionDTO->getUserId())
                    ->where('CoinId', $thisCoin->Id)
                    ->update([
                        'Money' => DB::raw("Money+{$memberCoinAmount}")
                    ]);
                $this->AddLog($userDeductionDTO->getUserId(), $memberCoinAmount, $thisCoin, 'transfer_hsm');
                $this->changeUserAmount($userDeductionDTO);
                return true;
            });
        }, 5);
    }

    /**
     * 初始化总收益
     * @param $userId
     * @return bool
     * @throws \Exception
     */
    public function initUserTotalIncome($userId)
    {
        $sn = new Snowflake();
        $totalIncome = new UserTotalIncome();
        $totalIncome->id = $sn->nextId();
        $totalIncome->user_id = $userId;
        $totalIncome->amount = 0;
        $totalIncome->before_amount = 0;
        $totalIncome->after_amount = 0;
        return $totalIncome->save();
    }


    /**
     * 钱包转入备用金
     * @param $userId
     * @param $amount
     * @return mixed
     * @throws ArException
     */
    public function turning($userId, $amount)
    {
        return RedisLock::lock(Enums::REDIS_LOCK_KEY['USER_TURNING'] . $userId, function () use ($userId, $amount) {

            if (getUserIsPrincipal($userId)) {
                throw new ArException(ArException::SELF_ERROR, '您已不支持转入备用斤');
            }
            return DB::transaction(function () use ($amount, $userId) {
                $coin = Coin::GetByEnName();
                $userAmount = getUserAmountByCoin($userId, $coin->Id);
                if ($amount > $userAmount->Money) {
                    throw new ArException(ArException::SELF_ERROR, '您的余额不足');
                }
                $userGiveAwayService = new UserGiveAwayService();
                $userGiveDTO = new UserDeductionDTO();
                $userGiveDTO->setUserId($userId);
                $userGiveDTO->setChildId(0);
                $userGiveDTO->setBusinessId(0);
                $userGiveDTO->setStatus(1);
                $userGiveDTO->setAmount($amount);
                $userGiveDTO->setType(2);
                $userGiveDTO->setMethod(1);
                $userGiveDTO->setRemarks('账户转入至备用斤');
                $userGiveDTO->setCoinId(0);
                $userGiveAwayService->changeUserAmount($userGiveDTO);

                DB::table('MemberCoin')->where('MemberId', $userId)
                    ->where('CoinId', $coin->Id)
                    ->update([
                        'Money' => DB::raw("Money-{$amount}")
                    ]);
                $this->AddLog($userId, (-$amount), $coin, 'turning');
                return true;
            });
        });
    }
}
