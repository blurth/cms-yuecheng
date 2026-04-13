<?php

namespace app\api\controller\v1;

use app\api\model\LessonAuthor as LessonAuthorModel;
use think\response\Json;

class LessonAuthor
{
    //author 增删改查

    public function save(): Json
    {
        //创建作者
        $params = input('post.');

        $lessonAuthor = new LessonAuthorModel();

        $where = [];

        if (isset($params['id']) && $params['id']){
            $where = ['id' => $params['id']];
            unset($params['id']);
        }
        $lessonAuthor->allowField(true)->save($params,$where);

        return writeJson(201, '', '成功');

    }

    public function delete($id): Json
    {
        //删除作者
        $lessonAuthor = LessonAuthorModel::get($id);

        if ($lessonAuthor){
            $lessonAuthor->delete();
            return writeJson(201, '', '成功');
        }

        return writeJson(400, '', '失败');
    }



    public function detail($id): Json
    {
        //获取作者详情
        $lessonAuthor = LessonAuthorModel::get($id);

        if ($lessonAuthor){
            return writeJson(200, $lessonAuthor, '成功');
        }

        return writeJson(404, '', '失败');
    }

    public function getList(): Json
    {
        //获取作者列表
        $lessonAuthor = LessonAuthorModel::all();

        return writeJson(200, $lessonAuthor, '成功');

    }
}