<?php

namespace app\api\controller\v1;

use think\Db;
use think\response\Json;

class index
{
    public function getPage(): Json
    {
        $param = input('param.');
        $where = [];
        if (isset($param['type']) && $param['type']){
            $where[] = ['type','=',$param['type']];
        }

        $data = Db::name('page')->where($where)->select();

        return writeJson(201, $data, 'success');
    }




}