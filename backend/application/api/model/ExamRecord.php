<?php

namespace app\api\model;
use think\Paginator;

class ExamRecord extends BaseModel
{
    public function exam()
    {
        return $this->belongsTo('Exam','exam_id','id');
    }

    public function student()
    {
        return $this->belongsTo('schoolStudent','student_id','id');
    }

    public static function getListByMap($map, $field='*',$order = 'id desc', $size = 15): Paginator
    {
        return self::where($map)
                        ->field($field)
                        ->order($order)
                        ->with(['exam'=>function($query){
                            return $query->field('id,title');
                        },'student'=>function($query){
                            $query->field('id,name');
                        }])
                        ->paginate($size)->each(function ($item){
                            return $item;
                        });
    }
}