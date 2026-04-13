<?php

namespace app\api\controller\v1;

use app\api\model\ActivityModel;
use app\api\model\ActivityUser;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use think\Db;
use think\response\Json;

class Activity extends BaseController
{

    /*CREATE TABLE `yu_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `theme` varchar(255) NOT NULL COMMENT '活动主题',
  `image_url` varchar(255) DEFAULT NULL COMMENT '活动图片URL',
  `description` text COMMENT '活动介绍',
  `registration_code` varchar(50) NOT NULL COMMENT '活动报名码',
  `registration_limit` int(11) DEFAULT NULL COMMENT '活动报名人数限制',
  `registration_count` int(11) DEFAULT '0' COMMENT '已报名人数',
  `registration_start_time` datetime NOT NULL COMMENT '活动报名开始时间',
  `registration_end_time` datetime NOT NULL COMMENT '活动报名结束时间',
  `check_in_code` varchar(50) NOT NULL COMMENT '活动核销码',
  `check_in_count` int(11) DEFAULT '0' COMMENT '已核销人数',
  `check_in_start_time` datetime NOT NULL COMMENT '活动核销开始时间',
  `version` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '乐观锁的版本号',
  `check_in_end_time` datetime NOT NULL COMMENT '活动核销结束时间',
  `conditions` json DEFAULT NULL COMMENT '报名条件',
  `address` varchar(255) DEFAULT NULL COMMENT '上课地点',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `registration_code` (`registration_code`),
  UNIQUE KEY `check_in_code` (`check_in_code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;*/


    public function list(): Json
    {
        $data = db('activity')
            ->whereNull('delete_at')
            ->order('created_at desc')
            ->paginate(input('get.size', 15));

        return writeJson(200, $data);
    }

    public function detail($id): Json
    {
        $data = db('activity')
            ->where('id', $id)
            ->find();

        $data['conditions'] = json_decode($data['conditions'], true);
        return writeJson(200, $data);
    }

    public function save(): Json
    {
        $data = input('post.');

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $model = new ActivityModel();

        // If id exists, update, else insert
        if (isset($data['id']) && $data['id']) {

            $data['conditions'] = json_encode($data['conditions']);

            // Update operation
            $model->isUpdate(true)->save($data, ['id' => $data['id']]);
        } else {
            // Insert operation
            unset($data['id']); // Ensure 'id' is not set for insert operation


            $data['conditions'] = json_encode($data['conditions']);

            $model->isUpdate(false)->save($data);
        }

        if ($model->id) {
            return writeJson(200, '', '操作成功');
        } else {
            return writeJson(400, '', '操作失败');
        }
    }

    public function delete($id): Json
    {
        $model = ActivityModel::get($id);
        if ($model) {
            $model->delete();
            return writeJson(200, '', '删除成功');
        } else {
            return writeJson(400, '', '删除失败');
        }
    }

    public function getRegisteredUsersById($id): Json
    {

        // ->field() json_decode user_form

        $data = ActivityUser::getRegisteredUsers($id);


        return writeJson(200, $data);
    }

    //导出活动报名用户 Excel
    public function exportRegisteredUsers($id)
    {

        //查找活动
        $activity = ActivityModel::get($id);

        if (!$activity) {
            return writeJson(400, '', '活动不存在');
        }

        //查找活动报名用户

        $data = ActivityUser::getRegisteredUsers($id);

        if (empty($data)) {
            return writeJson(400, '', '没有报名用户');
        }

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        // Add some data
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', '活动名称')
            ->setCellValue('B1', '用户名')
            ->setCellValue('C1', '手机号')
            ->setCellValue('D1', '年级')
            ->setCellValue('E1', '身份')
            ->setCellValue('F1', '备注')
            ->setCellValue('G1', '报名时间');

        // Miscellaneous glyphs, UTF-8
        $i = 2;
        foreach($data as $row) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $activity['theme'])
                ->setCellValue('B' . $i, $row['user_form']['name'])
                ->setCellValue('C' . $i, $row['user_form']['phone'])
                ->setCellValue('D' . $i, $row['user_form']['grade'])
                ->setCellValue('E' . $i, $row['user_form']['identity'])
                ->setCellValue('F' . $i, '') // Add your remark here
                ->setCellValue('G' . $i, $row['registration_time']);
            $i++;
        }

        // Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('活动报名用户');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="活动报名用户.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}