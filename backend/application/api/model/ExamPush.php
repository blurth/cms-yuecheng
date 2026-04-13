<?php

namespace app\api\model;

class ExamPush extends BaseModel

{
    public function school()
    {
        return $this->belongsTo('School','school_id','id');
    }

    public function exam()
    {
        return $this->belongsTo('Exam','exam_id','id');
    }
    public static function getListPage($where): \think\Paginator
    {
        return self::where($where)
            ->with(['school','exam'])
            ->order('create_time desc')
            ->field('*')
            ->paginate(input('get.size',15,'intval'))->each(function ($item){

            });
    }
}