<?php

namespace app\api\controller\v1;
use app\api\model\Lesson as LessonModel;
use app\api\model\LessonSection;
use app\api\model\LessonSection as LessonSectionModel;
use app\api\service\lesson\LessonService;
use app\lib\exception\Lesson\LessonException;
use app\lib\exception\NotFoundException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;
use think\response\Json;

class Lesson
{
    /**
     * 获取课程列表
     *
     * @throws DbException
     */
    public function getLessonList(): Json
    {
        $param = input('param.');
        $map = [];

        if (isset($param['title']) && $param['title']){
            $map[] = ['title','like','%'.$param['title'].'%'];
        }

        if (isset($param['resource_type']) && $param['resource_type']){
            $map[] = ['resource_type','=',$param['resource_type']];
        }

        if (isset($param['cid']) && $param['cid']){
            $map[] = ['cid','=',$param['cid']];
        }

        $data = LessonModel::getList($map);

        return writeJson(200,$data,'success');

    }

    /**
     * @param $id
     * @return Json
     */
    public function LessonDetail($id): Json
    {
       $data = LessonModel::get(1);

       return writeJson(200,$data);
    }


    /**
     * 获取章节列表 不带分页
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws NotFoundException
     */
    public function getSectionListByPid($id): Json
    {
        $data = LessonSectionModel::getListByPid($id);

        return writeJson(200,$data,'success');

    }


    /**
     * 创建或者新增一条课程
     *  @validate('SaveLessonForm')
     * @return mixed
     * @throws NotFoundException
     */
    public function createOrUpdate()
    {
        $data = input('post.');

        if (isset($data['id']) && $data) {
            // 如果请求参数中包含 id 字段，则认为是更新操作
            $model = LessonModel::get($data['id']);
            if ($model) {
                $model->allowField(true)->save($data);
                return writeJson(200,$model->id,'更新成功');
            } else {
                // 如果指定 ID 的数据不存在，则抛出异常
                throw new NotFoundException();
            }
        } else {
            // 否则认为是新增操作
            $data['create_time'] = date('Y-m-d H:i:s');
            $data['update_time'] = date('Y-m-d H:i:s');

            $model = new LessonModel;
            $model->allowField(true)->save($data);
            return writeJson(200,$model->id,'创建成功');
        }
    }


    /**
     * 删除课程
     * @loginRequired
     * @permission('删除课程权限','管理员','hidden')
     * @param $ids
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws LessonException
     * @throws Exception
     * @throws PDOException
     */
    public function delete($ids): Json
    {
        $result = LessonService::deleteLesson($ids);

        return writeJson(200,$result,'删除成功');

    }

    //创建章节
    public function createOrSaveSection(): Json
    {
        $data = input('post.');
        if (isset($data['id'])) {
            // 如果请求参数中包含 id 字段，则认为是更新操作
            $model = LessonSectionModel::get($data['id']);
            if ($model) {
                $model->allowField(true)->save($data);
                return writeJson(200,$model->id,'更新成功');
            } else {
                // 如果指定 ID 的数据不存在，则抛出异常
                throw new NotFoundException();
            }
        } else {
            // 否则认为是新增操作
            $model = new LessonSectionModel;
            $model->allowField(true)->save($data);
            return writeJson(200,$model->id,'创建成功');
        }
    }


    public function deleteSections($ids): Json
    {
        $idArray = explode(',', $ids);

        $result = LessonSection::where('id', 'in', $idArray)->delete();

        return writeJson(200,$result,'删除成功');
    }


    public function getSectionDetailById($id): Json
    {
        $data = LessonSectionModel::get($id);

        return writeJson(200,$data,'success');
    }


    public function getLessonDetailById($id): Json
    {
        $data = LessonModel::get($id);

        return writeJson(200,$data,'success');
    }
}