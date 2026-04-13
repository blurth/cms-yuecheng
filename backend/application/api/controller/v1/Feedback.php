<?php

namespace app\api\controller\v1;

use think\Db;
use think\exception\DbException;
use think\response\Json;

class Feedback
{
    /**
     * 获取反馈列表
     * @throws DbException
     */
    public function getFeedBack(): Json
    {
        $data = Db::name('user_feedback')->paginate(input('get.size', 10))->each(function ($item) {
            $item['img'] =json_decode($item['img'],true);
            return $item;
        });

        return json($data);
    }
}