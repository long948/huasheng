<?php
/**
 * Created by PhpStorm.
 * User: ChenJulong
 * Date: 2019/9/26
 * Time: 14:41
 */


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DogListModel extends Model
{
    public $table = 'dog_list';
    public $timestamps = false;
    protected $keyType = 'string';
    protected $casts = ['id' => 'string'];

    public static function GetPageList()
    {
        return self::orderBy('id','desc')
            ->get();
    }
    /**
     * @func根据id修改数据
     * @param $id
     * @return mixed
     */
    public static function updatedById (string $Id)
    {
        $data=[
            'is_audit'=>0
        ];
        return self::where('id', $Id)->update($data);
    }

}
