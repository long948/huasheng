<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as BaseModel;

abstract class Model extends BaseModel
{

    /**
     * 不维护主键
     * @var bool
     */
    public $incrementing = false;


    /**
     * 时间格式
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * 是否自动维护时间戳
     * @var bool
     */
    public $timestamps = true;

    /**
     * 创建时间
     */
    const CREATED_AT = 'create_time';

    /**
     * 更新时间
     */
    const UPDATED_AT = 'update_time';


    /**
     * 获取集合
     * @param array $filed
     * @return Collection
     */
    protected static function list($filed = ['*'])
    {
        return self::query()->get($filed);
    }

    /**
     * 单个详情
     * @param array $id
     * @return Model
     */
    protected static function find($id)
    {
        return self::query()->find($id);
    }

    /**
     * 修改
     * @param Model $model
     * @return bool
     */
    protected static function edit(self $model): bool
    {
        return $model->save();
    }


    /**
     * 重写时间的格式
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
