<?php

namespace app\api\controller\v1;

use think\Db;
use think\response\Json;

class District extends BaseController
{
    public function getProvinces(): Json
    {
        $provinces = Db::name('district')->where('level', 1)->select();

        if ($provinces){
            return writeJson(201, $provinces, '获取省级数据成功！');
        }
        return writeJson(201, [], '获取省级数据失败！');
    }


    public function getCities($province_id): Json
    {
        $cities = Db::name('district')->where('parent_id', $province_id)->select();

        if ($cities){
            return writeJson(201, $cities, '获取市级数据成功！');
        }
        return writeJson(201, [], '获取市级数据失败！');
    }


    public function getDistricts($city_id): Json
    {
        $districts = Db::name('district')->where('parent_id', $city_id)->select();

        if ($districts){
            return writeJson(201, $districts, '获取区级数据成功！');
        }
        return writeJson(201, [], '获取区级数据失败！');
    }


}