<?php

namespace app\api\service\lesson;

use app\api\model\Lesson;
use app\api\model\LessonSection;
use app\lib\exception\lesson\LessonException;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;

class LessonService
{
    /**
     * @param $ids
     * @return int
     * @throws LessonException
     * @throws Exception
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws PDOException
     */
    public static function deleteLesson($ids): int
    {

        $idArray = explode(',', $ids);

        // 遍历要删除的课程ID
        foreach ($idArray as $id) {
            // 查询该课程是否有关联的章节
            $hasSection = Db::name('lesson_section')->where('pid', $id)->count();

            if ($hasSection) {
                // 如果有关联的章节，抛出异常提示不能删除
                throw new LessonException(['msg'=>'该课程下存在章节，不能删除']);
            }
        }

        // 使用TP自带的批量删除方法删除指定id的课程
        return Lesson::where('id', 'in', $idArray)->delete();
    }


    public static function deleteSections($ids): int
    {
        $idArray = explode(',', $ids);

        return LessonSection::where('id', 'in', $idArray)->delete();
    }
}