<?php

namespace app\api\controller\v1;

use think\Db;
use think\facade\Hook;

class ExamPush
{
    public function pushExam()
    {
        $_data = input('post.');

        $data = [
            'frequency_type' => $_data['frequencyType'],
            'first_push_date' => $_data['firstDate'],
            'next_push_date' => $_data['firstDate'],
            'exam_id' => $_data['examId'],
            'object_type' => $_data['objectType'],
            'province_code' => $_data['provinceCode'],
            'area_code' => $_data['areaCode'],
            'city_code' => $_data['cityCode'],
            'school_id' => $_data['schoolId'],
            'grade_id' => implode(',', $_data['gradeId']),
            'update_time' => time(),
            'create_time' => time(),
        ];

        if (!empty($_data['classId'])) {
            $data['class_id'] = implode(',', $_data['classId']);
        }

        switch ($_data['frequencyType']) {
            case 1:
                if (empty($_data['weekend'])) {
                    return $this->errorReturn('请至少选择一个时间');
                }
                break;
            case 2:
                if (empty($_data['month'])) {
                    return $this->errorReturn('请至少选择一个时间');
                }
                break;
            case 3:
                if (empty($_data['date'][0])) {
                    return $this->errorReturn('请选择开始时间');
                }
                if (empty($_data['date'][1])) {
                    return $this->errorReturn('请选择结束时间');
                }
                $timeStart = strtotime($_data['date'][0] . ' 00:00:00');
                $timeEnd = strtotime($_data['date'][1] . ' 23:59:59');
                if ($timeEnd < $timeStart) {
                    return $this->errorReturn('结束时间应不小于开始时间');
                }
                $_data['firstDate'] = $_data['date'][0];
                $data['frequency_config'] = $timeStart . ',' . $timeEnd;
                break;
            default:
                return $this->errorReturn('无效的频率类型');
        }

        if ($_data['firstDate'] == '') {
            return $this->errorReturn('请选择开始日期');
        }
        if ($_data['firstDate'] < date('Y-m-d')) {
            return $this->errorReturn('开始日期应大于今天');
        }

        $res = Db::name('table_name')->insert($data);

        if ($res) {
            return $this->successReturn($res, '创建成功');
        }

        return $this->errorReturn('创建失败');
    }

    public function getList()
    {
        $where = [];

        $data = \app\api\model\ExamPush::getListPage($where);

        return writeJson(200, $data, '获取成功');
    }

    public function delete()
    {
        $id = input('get.id');

        $res  = \app\api\model\ExamPush::destroy($id);

        if ($res) {
            Hook::listen('logger', '删除了推送考试' . $id);
            return writeJson(200, [], '删除成功');
        }else{
            return writeJson(200, [], '删除失败');
        }
    }

}