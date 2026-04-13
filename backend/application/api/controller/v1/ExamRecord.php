<?php

namespace app\api\controller\v1;
use app\api\model\ExamRecord as ExamRecordModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\Db;
use think\response\Json;

class ExamRecord extends BaseController
{
    public function list(): Json
    {
        $param = input('param.');

        $map = [];


        $sid = Db::name('school_student')->where('school_id','in',$this->school)->column('id');

        $map[] = ['student_id','in',$sid];



        if (isset($param['school_id']) && $param['school_id']){
            $sid = Db::name('school_student')->where('school_id',$param['school_id'])->column('id');

            $map[] = ['student_id','in',$sid];
        }

        if (isset($param['grade_id']) && $param['grade_id']){
            $sid = Db::name('school_student')->where('grade_id',$param['grade_id'])->column('id');

            $map[] = ['student_id','in',$sid];
        }


        if (isset($param['class_id']) && $param['class_id']){
            $sid = Db::name('school_student')->where('class_id',$param['class_id'])->column('id');

            $map[] = ['student_id','in',$sid];
        }



        if (isset($param['exam_id']) && $param['exam_id']){
            $map[] = ['exam_id','=',$param['exam_id']];
        }

        if (isset($param['status']) && $param['status']){
            $map[] = ['status','=',$param['status']];
        }

        if (isset($param['identity']) && $param['identity']){
            $map[] = ['identity','=',$param['identity']];
        }

        if (isset($param['source']) && $param['source']){
            $map[] = ['source','=',$param['source']];
        }

        if (isset($param['is_skip']) && $param['is_skip']){
            $map[] = ['is_skip','=',$param['is_skip']];
        }

        if (isset($param['times']) && $param['times']){
            $map[] = ['times','=',$param['times']];
        }

        if (isset($param['expire_timeline']) && $param['expire_timeline']){
            $map[] = ['expire_timeline','=',$param['expire_timeline']];
        }

        if (isset($param['update_time']) && $param['update_time']){
            $map[] = ['update_time','=',$param['update_time']];
        }

        if (isset($param['create_time']) && $param['create_time']){
            $map[] = ['create_time','=',$param['create_time']];
        }


        if (isset($param['name']) && $param['name']){
            $studentIds = Db::name('school_student')->where('name' , 'like' ,'%'.trim($param['name']).'%')->column('id');

            $map[] = ['student_id','in',$studentIds];
        }


        $size = $param['size'] ?? 15;

        $data = Db::name('exam_record')
            ->alias('er')
            ->field('erd.exam_result_id,sc.title schoolName,sg.title gradeName,scs.title className,er.id, er.status, er.exam_id, er.identity, er.source, er.student_id, er.times, er.is_skip, er.expire_timeline, er.update_time, er.create_time')
            ->where($map)
            ->join('exam e', 'er.exam_id = e.id')
            ->join('school_student s', 'er.student_id = s.id')
            ->join('school sc', 's.school_id = sc.id')
            ->join('school_grade sg', 's.grade_id = sg.id')
            ->join('school_class scs', 's.class_id = scs.id')
            ->join('exam_record_detail erd', 'er.id = erd.record_id')
            ->field('e.id as exam_id, e.title, s.id as student_id, s.name')
            ->order('er.update_time desc')
            ->paginate($size)
            ->each(function ($item) {
                $item['expire_timeline'] = date('Y-m-d H:i:s', $item['expire_timeline']);
                $item['update_time'] = date('Y-m-d H:i:s', $item['update_time']);
                $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
                return $item;
            });



        return writeJson(200, $data);

    }


    public function detail($id)
    {
        $data = Db::name('exam_record')
            ->alias('er')
            ->field('GROUP_CONCAT(DISTINCT ssr.description) as descriptions, e.title, er.id, erd.snap_detail_item, er.status, er.exam_id, er.identity, er.source, er.student_id, er.times, er.is_skip, er.expire_timeline, er.update_time, er.create_time')
            ->where('er.id', $id)
            ->join('exam e', 'er.exam_id = e.id')
            ->join('exam_record_detail erd', 'er.id = erd.record_id')
            ->join('school_student_record ssr', 'er.student_id = ssr.student_id', 'left')
            ->find();

        if (empty($data)) {
            return writeJson(404, [], '记录不存在');
        }

        $data['expire_timeline'] = date('Y-m-d H:i:s', $data['expire_timeline']);
        $data['update_time'] = date('Y-m-d H:i:s', $data['update_time']);
        $data['snap_detail_item'] = json_decode($data['snap_detail_item'], true);
        $data['descriptions'] = explode(',', $data['descriptions']);

        $res = [
            'detail' => $data,
            'result' => [],
            'record' => []
        ];
        return writeJson(200, $res);
    }


    public function exportExamResult()
    {
        //查询学校id16的所有问卷已完成的第五次的 86
        set_time_limit(0);


        $param = input('get.');

        $where = [
            ['school_id','in',[$param['school_id']]],
        ];

        $studentIds = Db::name('school_student')
            ->where($where)
            ->column('id');

        $where = [
            ['er.student_id','in',$studentIds],
            ['er.status','=',1],
            ['er.exam_id','=',5],
            ['er.times','=',$param['times']]
        ];

        $data = Db::name('exam_record')
            ->alias('er')
            ->where($where)
            ->join('exam_record_detail erd','er.id = erd.record_id','left')
            ->join('school_student ss','er.student_id = ss.id','inner')
            ->join('school s','ss.school_id = s.id','inner')
            ->join('school_class sc','ss.class_id = sc.id','inner')
            ->join('exam_result eres','erd.exam_result_id = eres.id','inner')
            ->field('ss.id student_id,er.expire_timeline,,er.update_time,s.title as schoolName,ss.gender,er.id,er.student_id,erd.snap_detail_item,ss.name,ss.grade_id,ss.student_number,sc.title className,eres.title as resultTitle')
            ->select()
            ->toArray();

        $result = [];

        foreach ($data as $v){

            $item = json_decode($v['snap_detail_item'],true);
            $xmap = [
                ['record_id','=',$v['id']] ,
                ['question_id','=',86]
            ];
            $row = [];
            $row['学校'] = $v['schoolName'];
            $row['年级'] = $v['grade_id'];
            $row['班级'] = $v['className'];
            $row['学号'] = $v['student_number'];
            $row['姓名'] = $v['name'];
            $row['性别'] = $v['gender'] == 1? '男' : '女';
            $row['体重'] = $item['constData']['now_wight'];
            $row['睡眠时长'] = $item['constData']['sleep_time'];
            $row['日均运动时长'] = $item['constData']['sports_time'];

            $row['问卷结果'] = $v['resultTitle'];
            $row['问卷填写时间'] = date('Y-m-d H:i:s',$v['update_time']);
            $row['问卷过期时间'] = date('Y-m-d H:i:s',$v['expire_timeline']);
            $xdata = Db::name('exam_record_result')->where($xmap)->find();

            $row['消极事件'] = $this->getQuestionDetailTitleByIds($xdata['question_detail_id']).$xdata['description'];

            foreach($item['list'] as $listItem){
                $row[$listItem['label'].'问题（1-1）'] = $listItem['description'];
                $row[$listItem['label'].'哪些问题（1-2）'] = $listItem['detail']['reason'] ?? '';
                $row[$listItem['label'].'原因或诱发事件（1-3）'] = $listItem['detail']['manifestation'] ?? '';
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

// 设置HTTP头，告诉浏览器这是一个Excel文件
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="jingche.xlsx"');
        header('Cache-Control: max-age=0');

// 创建Excel写入器并直接将内容发送到浏览器
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

    }

}