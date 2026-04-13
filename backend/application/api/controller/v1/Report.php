<?php

namespace app\api\controller\v1;

use think\Db;
use think\response\Json;

class Report extends BaseController
{
    public function list(): Json
    {

        // 使用input()函数替换$request->get()


        $schoolId = input('get.school_id'); // 获取学校ID
        $reportTimes = input('get.times'); // 获取报告次数
        $search = input('get.name'); // 获取模糊搜索关键字
        $where = [
            ['r.school_id', 'in', $this->school]
        ];


        if ($schoolId) {
            $where[] = ['r.school_id', '=', $schoolId];
        }

        if ($reportTimes) {
            $where[] = ['r.times', '=', $reportTimes];
        }

        if ($search) {
            $where[] = ['s.title', 'like', "%{$search}%"];
        }



        $reports = Db::name('report')->alias('r')
            ->field('r.*, s.title as schoolName') // 选择字段并给学校起别名
            ->join('school s', 'r.school_id = s.id', 'left') // 执行关联查询
            ->where($where)
            ->order('create_time desc')
            ->paginate(input('get.size', 10));



        return writeJson(200, $reports);


    }


    /**
     * @return Json
     * 新增报告
     */
    public function save(): Json
    {
        $data = input('post.');

        $data['create_time'] = date('Y-m-d H:i:s');
        $data['update_time'] = date('Y-m-d H:i:s');
        $data['times'] = Db::name('school')->where('id', $data['school_id'])->value('last_exam_times');
        $res = Db::name('report')->insert($data);

        if ($res) {
            return writeJson(201, '', '添加成功');
        } else {
            return writeJson(400, '', '添加失败');
        }

    }


}