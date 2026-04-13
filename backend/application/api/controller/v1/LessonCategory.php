<?php

namespace app\api\controller\v1;

use app\api\service\lesson\LessonCategoryService;

class LessonCategory
{
    public function getCateGory()
    {
        $result = (new LessonCategoryService)->getListG();

        return writeJson(200,$result);
    }
}