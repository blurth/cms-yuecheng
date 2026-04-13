<?php

namespace app\api\controller\v1;

use app\api\model\SchoolTeacher;
use think\Db;
use think\facade\Hook;
use think\response\Json;

class Teacher extends BaseController
{

    public function list(): Json
    {
        $map = [
            ['sp.school_id', 'in', $this->school],
            ['sp.is_delete', '=', 0]
        ];

        $param = input('get.');

        // 现有条件
        if (isset($param['school_id']) && $param['school_id']) {
            $map[] = ['sp.school_id', '=', $param['school_id']];
        }
        if (isset($param['name']) && $param['name']) {
            $map[] = ['sp.name', 'like', '%' . trim($param['name']) . '%'];
        }

        // 新添加的学院和专业条件
        if (isset($param['college_id']) && $param['college_id']) {
            $map[] = ['sp.college_id', '=', $param['college_id']];
        }
        if (isset($param['major_id']) && $param['major_id']) {
            $map[] = ['sp.major_id', '=', $param['major_id']];
        }

        // 查询
        $res = db('school_teacher')
            ->alias('sp')
            ->join('school s2', 's2.id = sp.school_id')
            ->join('school_colleges col', 'col.id = sp.college_id') // 添加学院
            ->join('school_majors maj', 'maj.id = sp.major_id') // 添加专业
            ->field('sp.*, s2.title as school_name, col.college_name, maj.major_name') // 添加新字段
            ->where($map)
            ->paginate(input('get.size', 10));

        return writeJson(200, $res);
    }



    public function save(): Json
    {
        $params = input('post.'); // 获取POST请求传递的参数
        $TeacherModel = new SchoolTeacher(); // 创建模型实例

        $where = [];

        if (isset($params['id']) && $params['id']){
            $where = ['id' => $params['id']]; // 构造查询条件
        }

        // 更新或新增教师信息
        $TeacherModel->allowField(true)->save($params, $where);
        $mes = isset($params['id']) && $params['id'] ? '更新了教师信息' : '新增了教师信息';
        //记录日志
        Hook::listen('logger', $mes . $TeacherModel->id);

        return writeJson(201,'','教师信息已更新');
    }

    public function delete($id): Json
    {
        // 检查教师ID是否存在
        $teacher = Db::name('school_teacher')->where('id', $id)->find();
        if (!$teacher) {
            return writeJson(404,'教师不存在');
        }

        // 删除（或标记删除）教师记录

        $res =  Db::name('school_teacher')->where('id',$id)->update(['is_delete' => 1]);

        if ($res){
            //记录日志
            Hook::listen('logger', '删除了教师' . $id);

            return writeJson(201,'删除成功');
        }else{
            return writeJson(400,'删除失败');
        }
    }

    public function getWorkLog(): Json
    {
        $param = input('get.');

        $where = [];

        if (isset($param['school_id']) && $param['school_id']){
            $where[] = ['school_id','=',$param['school_id']];
        }

        if (isset($param['teacher_id']) && $param['teacher_id']){
            $where[] = ['teacher_id','=',$param['teacher_id']];
        }

        //name
        if (isset($param['name']) && $param['name']){
            $where[] = ['name','like','%'.trim($param['name']).'%'];
        }

        if (isset($param['start_time']) && $param['start_time']){
            $where[] = ['create_time','>=',strtotime($param['start_time'])];
        }

        if (isset($param['end_time']) && $param['end_time']){
            $where[] = ['create_time','<=',strtotime($param['end_time'])];
        }

        $res = db('school_teacher_log')
            ->where($where)
            ->paginate(input('get.size',10));

        return writeJson(200,$res);
    }
}