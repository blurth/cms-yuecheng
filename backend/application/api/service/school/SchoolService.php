<?php

namespace app\api\service\school;

use think\exception\DbException;
use think\Paginator;

class SchoolService
{

    /**
     * @throws DbException
     */
    public function getSchoolList($where): Paginator
    {
        return db('school')->alias('A')->where($where)->paginate()->each(function ($item){
            $item['province'] = db('District')->where('id',$item['province_code'])->value('title');
            $item['city'] = db('District')->where('id',$item['city_code'])->value('title');
            $item['area'] = db('District')->where('id',$item['area_code'])->value('title');
            $item['create_time'] = date('Y-m-d H:i:s',$item['create_time']);
            return $item;
        });
    }










}