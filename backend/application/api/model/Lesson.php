<?php

namespace app\api\model;

use think\exception\DbException;
use think\Paginator;

class Lesson extends BaseModel
{

    /*    public function getCidAttr($value)
    {
                $data = LessonCategory::get($value);
                $res = [];
                if ($data['parent_id'] == 0){
                    return $res;
                }else{
                    $data[] = $data['id'];
                }
    }*/

    /**
     * @throws DbException
     */
    public static function getList($where): Paginator
    {

        $data = self::alias('l')
            ->where($where)
            ->field(mt_fields([
                'l' => ['id', 'title', 'identity_type', 'sub_title', 'author_id', 'resource_type', 'description', 'label', 'lesson_count', 'cover','cid','learn_num','play_num','index','is_hot','create_time','gender_type','article_type'],
                'c' => ['name classify'],
                's' => ['name s_name'],
            ]))
            ->join('lesson_category c', 'c.id=l.cid', 'left')
            ->join('lesson_author s','s.id = l.author_id','left')
            ->order('l.id DESC')
            ->paginate(input('param.size','15','intval'));

        return $data;
    }
}