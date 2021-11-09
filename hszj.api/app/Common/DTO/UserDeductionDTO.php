<?php


namespace App\Common\DTO;

use App\Services\Enum\UserDeductionEnum;
use App\Utils\Enum\DeductionEnums;

/**
 * 用户扣费接口实体(领导奖等)
 * Class UserDeductionDTO
 * @package App\Services\Vo
 */
class UserDeductionDTO
{

    /**
     * 用户ID
     * @var int
     */
    private $userId;

    /**
     * 币种
     * @var int
     */
    private $coinId;

    /**
     * 类型 进出帐 详细查看Enums
     * @var int
     */
    private $method;

    /**
     * 金额
     * @var string
     */
    private $amount;

    /**
     * 扣款类型
     * @var int
     */
    private $type;

    /**
     * 第三方业务编号
     * @var int
     */
    private $businessId;

    /**
     * 备注
     * @var string
     */
    private $remarks;

    /**
     * 状态
     * @var int
     */
    private $status;

    /**
     * 二级用户
     * @var string
     */
    private $childId;

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getCoinId(): int
    {
        return $this->coinId;
    }

    /**
     * @param int $coinId
     */
    public function setCoinId(int $coinId): void
    {
        $this->coinId = $coinId;
    }

    /**
     * @return int
     */
    public function getMethod(): int
    {
        return $this->method;
    }

    /**
     * @param int $method
     */
    public function setMethod(int $method): void
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     */
    public function setAmount(string $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type ? $this->type : 0;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getBusinessId(): int
    {
        return $this->businessId;
    }

    /**
     * @param int $businessId
     */
    public function setBusinessId(int $businessId): void
    {
        $this->businessId = $businessId;
    }

    /**
     * @return string
     */
    public function getRemarks(): string
    {
        return $this->remarks;
    }

    /**
     * @param string $remarks
     */
    public function setRemarks(string $remarks): void
    {
        $this->remarks = $remarks;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getChildId(): string
    {
        return $this->childId ? $this->childId : 0;
    }

    /**
     * @param string $childId
     */
    public function setChildId(string $childId): void
    {
        $this->childId = $childId;
    }


}
