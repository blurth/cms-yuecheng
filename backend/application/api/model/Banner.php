<?php

namespace app\api\model;

use app\api\model\Banner as BannerModel;
use think\exception\DbException;
use think\Paginator;

class Banner extends BaseModel
{

    protected $type = [
        'create_time' => 'datetime:Y-m-d H:i:s',
    ];
    public function items()
    {
        // 调用了模型实例的hasMany（）方法，这个方法定义了当前模型与被关联模型BannerItem是一种一对多的关系
        // 关联的内容是BannerItem模型里banner_id属性的值与当前模型的id属性的值一致的记录。
        return $this->hasMany('BannerItem','banner_id', 'id');
    }

    /**
     * 新增banner
     * */
    public static function add($parmas)
    {
        // 调用当前模型的静态方法create()，第一个参数为要写入的数据，第二个参数标识仅写入数据表定义的字段数据
        $banner = self::create($parmas, true);

        foreach ($parmas['items'] as &$key){
            $key['banner_id'] = $banner['id'];
        }
        // 调用关联模型，实现关联写入；saveAll()方法用于批量新增数据
        return $banner->items()->saveAll($parmas['items']);

    }


    /**
     * 获取banner
     * @throws DbException
     */
    static function getList($where): Paginator
    {
        $size = input('get.size','15','intval');
        return self::where([])->whereOr($where)->with('items')->paginate($size??15);
    }
}