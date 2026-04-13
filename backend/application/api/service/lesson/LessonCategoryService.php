<?php

namespace app\api\service\lesson;



use app\api\model\LessonCategory;

class LessonCategoryService
{
    public function getListG()
    {
        $lessonCategory = new LessonCategory();

        $data = $lessonCategory->where('parent_id',0)->select();

        foreach ($data as $v){
            $arr = [];
            $arr['name'] = $v['name'];
            $arr['id'] = $v['id'];
            $arr['child'] = $this->getLastList($v['id']);
            $data[] = $arr;
        }
       return $data;
    }
    private function getLastList($pid = 0): array
    {
        $lessonCategory = new LessonCategory();

        $datas = $lessonCategory->where('parent_id',$pid)->select();

        $arr = [];
        if($datas){
            foreach ($datas as $v){
                $arrs = [];
                $arrs['name'] = $v['name'];
                $arrs['id'] = $v['id'];
                $arrs['child'] = self::getLastList($v['id']);
                $arr[] = $arrs;
            }
        }
        return $arr;
    }
}