<?php

namespace app\api\controller\v1;

use app\api\model\Exam as ExamModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\Db;
use think\response\Json;

class Exam
{

    public function createOrUpdate(): Json
    {

        $param = input('param.');

        $QuestionModel = new ExamModel();
        $where = [];
        if (isset($params['id']) && $params['id']){
            $where = ['id' => $params['id']];
        }

        $QuestionModel->allowField(true)->save($param,$where);

        //Hook::listen('logger', '新建了问卷：'.$param['title'].'，问卷id：'.$QuestionModel->id);

        return writeJson(201, '', '成功');

    }


    //导出自测量表结果Excel

    public function exportSelfTestResult(): int
    {
        //查询学校id16的所有问卷已完成的第五次的 86
        set_time_limit(0);
        $where = [
            ['school_id','in',[17]],
        ];

        $studentIds = Db::name('school_student')
            ->where($where)
            ->column('id');

        $where = [
            ['er.student_id','in',$studentIds],
            //['er.status','=',1],
            ['er.exam_id','=',11],
            ['er.times','=',1]
        ];

        $data = Db::name('exam_record')
            ->alias('er')
            ->where($where)
            ->join('exam_record_detail erd','er.id = erd.record_id','left')
            ->join('school_student ss','er.student_id = ss.id','inner')
            ->join('school_class sc','ss.class_id = sc.id','inner')
            ->join('exam_result eres','erd.exam_result_id = eres.id','inner')
            ->field('ss.id student_id,er.expire_timeline,ss.gender,er.id,er.student_id,erd.snap_detail_item,ss.name,ss.grade_id,ss.student_number,sc.title className,eres.title as resultTitle')
            ->select()
            ->toArray();

        $result = [];

        foreach ($data as $v){
/*            $kist = Db::name('exam_record_result')
                ->alias('err')
                ->where('record_id' , $v['id'])
                ->join('question q' , 'q.id = err.question_id')
                ->join('question_detail qd' , 'qd.id = err.question_detail_id')
                ->field('q.id question_id ,q.title questionTitle ,qd.id question_detail_id, qd.score,qd.title as answer ,err.description,q.label
               ')->select();*/

            $item = json_decode($v['snap_detail_item'],true);

            $row = [];
            //$row['学校'] = $v['schoolName'];
            $row['年级'] = $v['grade_id'];
            $row['班级'] = $v['className'];
            $row['学号'] = $v['student_number'];
            $row['姓名'] = $v['name'];
            $row['量表效度'] = $v['status'] == 1 ? '有效' : '无效';


           foreach ($item['list'] as $kk => $vv){
               $row[$vv['label']] = $vv['total_score'];
              }


            $result[] = $row;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

// Header
        $columnIndex = 1;
        foreach ($result[0] as $key => $value) {
            $sheet->setCellValueByColumnAndRow($columnIndex, 1, $key);
            $columnIndex++;
        }

// Rows
        $rowIndex = 2;
        foreach ($result as $row) {
            $columnIndex = 1;
            foreach ($row as $cellValue) {
                $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $cellValue);
                $columnIndex++;
            }
            $rowIndex++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save('1234'.'.xlsx');

        return 1234;
    }


    public function list(): Json
    {
        $where = [];

        $param = input('param.');

        if (isset($param['title']) && $param['title']){
            $where[] = ['title','like','%'.$param['title'].'%'];
        }

        if (isset($param['category_id']) && $param['category_id']){
            $where[] = ['category_id','like','%'.$param['category_id'].'%'];
        }

        $ExamModel = new ExamModel();

        $data = $ExamModel
            ->where($where)
            ->paginate(input('get.size',15));

        return writeJson(200,$data);
    }


    /**
     * @permission('删除问卷','管理员','hidden')
     * @return Json
     * @throws \think\exception\DbException
     */
    public function delete($id): Json
    {
        $res = ExamModel::destroy($id);

        return writeJson(201, '', '成功');
    }
}