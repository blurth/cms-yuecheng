<?php

namespace app\api\model;

use app\lib\exception\NotFoundException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;

class LessonSection extends BaseModel
{





    /**
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws NotFoundException
     */
    public static function getListByPid($pid)
    {

       $data = self::where('pid',$pid)
           ->field('id,pid,img,name,play_count,add_time,status')
           ->select()
           ->toArray();

       if (!empty($data)){
           return $data;
       }

        throw new NotFoundException(['error_code' => 10077]);

    }
}