<?php

namespace app\api\controller\v1;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\Db;
use think\facade\Config;
use think\facade\Hook;
use think\response\Json;
use EasyWeChat\Factory;

class Appointment extends BaseController
{
    /**
     * 获取预约列表
     * @groupRequired
     * @permission('查询预约','咨询师')
     * @return Json
     */
    public function list(): Json
    {
        $where = [];
        $where[] = ['a.deleted_at', 'null', ''];

        $param = input('get.');

        if (isset($param['user_id']) && $param['user_id']) {
            $where[] = ['a.user_id', '=', $param['user_id']];
        }


        if (isset($param['name']) && $param['name']) {

            $userId = db('user')->where('name', $param['name'])->value('id');

            if ($userId) {
                $where[] = ['a.user_id', '=', $userId];
            }else{
                $where[] = ['a.user_id', '=', 0];
            }
        }

        if (isset($param['status']) && $param['status'] == 1) {
            $where[] = ['a.user_id', '>', 0];
        }

        if (isset($param['status']) && $param['status'] == 2) {
            $where[] = ['a.user_id', 'null', ''];
        }

        if (isset($param['psychologist_id']) && $param['psychologist_id']) {
            $where[] = ['a.psychologist_id', '=', $param['psychologist_id']];
        }else{
            $where[] = ['a.psychologist_id', 'in', $this->psychologist_id];
        }



        if (isset($param['start_time']) && $param['start_time']) {
            $where[] = ['a.appointment_date', '>=', $param['start_time']];
        }

        if (isset($param['end_time']) && $param['end_time']) {
            $where[] = ['a.appointment_date', '<=', $param['end_time']];
        }

        if (isset($param['appointment_date']) && $param['appointment_date']) {
            $where[] = ['a.appointment_date', '=', $param['appointment_date']];
        }

        if (isset($param['is_confirmed']) && $param['is_confirmed']) {
            $where[] = ['a.is_confirmed', '=', $param['is_confirmed']];
        }



        $data = db('appointments')
                ->alias('a')
                ->join('psychologist p', 'a.psychologist_id = p.id', 'left')
                ->where($where)
                ->leftJoin('user u', 'a.user_id = u.id')
                ->field('a.*, p.name as psychologist_name,u.name as user_name')
                ->order('a.appointment_date desc')
                ->paginate(input('get.size',15))->each(function ($item){
                    $item['user_form'] = empty($item['user_form'])? '' : json_decode($item['user_form'],true);
                    $item['log_form'] = empty($item['log_form'])? '' : json_decode($item['log_form'] ,true);
                    return $item;
                });

        return writeJson(200,$data);
    }


    /**
     * 获取预约详情
     * @groupRequired
     * @permission('查询预约','咨询师')
     * @param int $id
     * @return Json
     */
    public function detail($id): Json
    {
        // Query the yu_appointments table for the appointment with the given id
        $data = db('yu_appointments')
            ->where('id', $id)
            ->find();

        return writeJson(200, $data);
    }

    /**
     * 更新或者添加预约排期
     * @groupRequired
     * @permission('新增预约','咨询师')
     * @return Json
     */
    public function save(): Json
    {
        $data = input('post.');

        $data['create_time'] = date('Y-m-d H:i:s');

        // If the appointment is set to repeat
        if (isset($data['repeat']) &&  $data['repeat'] === 'repeat') {
            // Calculate the end date for the repeating appointments
            $endDate = $data['endDate'];

            // Start date of the appointment
            $startDate = $data['appointment_date'];

            // Calculate the difference in days between the start and end date
            $interval = abs($endDate - $startDate) / 86400;

            // Create the appointments based on repeat frequency  
            $currentDate = $startDate;
            $createdAppointments = [];
            
            // For workdays, we need to check each day individually
            while ($currentDate <= $endDate) {
                $currentDateFormatted = date('Y-m-d', $currentDate);
                $dayOfWeek = date('N', $currentDate); // 1=Monday, 7=Sunday
                $shouldCreate = false;
                
                switch ($data['repeatFrequency']) {
                    case 'daily':
                        $shouldCreate = true;
                        break;
                    case 'workdays':
                        // Only create on Monday(1) to Friday(5)
                        $shouldCreate = ($dayOfWeek >= 1 && $dayOfWeek <= 5);
                        break;
                    case 'weekends':
                        // Only create on Saturday(6) and Sunday(7)
                        $shouldCreate = ($dayOfWeek == 6 || $dayOfWeek == 7);
                        break;
                    case 'weekly':
                        // Create only if it's the same day of week as start date
                        $startDayOfWeek = date('N', $startDate);
                        $shouldCreate = ($dayOfWeek == $startDayOfWeek);
                        break;
                    default:
                        $shouldCreate = true;
                        break;
                }
                
                if ($shouldCreate) {
                    $appointmentData = $data;
                    $appointmentData['appointment_date'] = $currentDateFormatted;
                    
                    $res = db('appointments')->strict(false)->insert($appointmentData);
                    if (!$res) {
                        return writeJson(400, [], '添加预约失败，日期：' . $currentDateFormatted);
                    }
                    
                    $createdAppointments[] = [
                        'date' => $currentDateFormatted,
                        'dayOfWeek' => $dayOfWeek,
                        'dayName' => date('l', $currentDate)
                    ];
                }
                
                // Move to next day
                $currentDate += 86400;
                
                // For non-daily patterns, adjust the increment
                if ($data['repeatFrequency'] == 'weekly' && $shouldCreate) {
                    $currentDate += (6 * 86400); // Skip 6 days to get to next week
                } elseif ($data['repeatFrequency'] == 'biweekly' && $shouldCreate) {
                    $currentDate += (13 * 86400); // Skip 13 days to get to next biweek
                }
            }
            
            // Return success with debug info
            return writeJson(200, [
                'message' => '添加成功',
                'created_count' => count($createdAppointments),
                'frequency' => $data['repeatFrequency'],
                'period' => date('Y-m-d', $startDate) . ' to ' . date('Y-m-d', $endDate),
                'created_appointments' => $createdAppointments
            ], '添加成功');
        } else {
            // If the appointment is not set to repeat, just insert it once
            $data['appointment_date'] = date('Y-m-d', $data['appointment_date']);
            $res = db('appointments')->strict(false)->insert($data);

            if ($res) {
                return writeJson(200, [], '添加成功');
            } else {
                return writeJson(400, [], '添加失败');
            }
        }
    }


    /**
     * 删除预约
     * @groupRequired
     * @permission('删除预约','咨询师')
     * @param int $id
     * @return Json
     */
    public function delete($id): Json
    {
        $res = db('appointments')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);

        if ($res) {
            return writeJson(200, [], '删除成功');
        } else {
            return writeJson(400, [], '删除失败');
        }

    }

    /**
     * 导出预约列表
     * @groupRequired
     * @permission('导出预约','咨询师')
     * @return mixed
     */
    public function exportList()
    {

        $where = [];

        $param = input('get.');

        if (isset($param['user_id']) && $param['user_id']) {
            $where[] = ['a.user_id', '=', $param['user_id']];
        }


        if (isset($param['name']) && $param['name']) {

            $userId = db('user')->where('name', $param['name'])->value('id');

            if ($userId) {
                $where[] = ['a.user_id', '=', $userId];
            }else{
                $where[] = ['a.user_id', '=', 0];
            }
        }

        if (isset($param['status']) && $param['status'] == 1) {
            $where[] = ['a.user_id', '>', 0];
        }

        if (isset($param['status']) && $param['status'] == 2) {
            $where[] = ['a.user_id', 'null', ''];
        }

        if (isset($param['psychologist_id']) && $param['psychologist_id'] > 0) {
            $where[] = ['a.psychologist_id', '=', $param['psychologist_id']];
        }

        if (isset($param['start_time']) && $param['start_time']) {
            $where[] = ['a.appointment_date', '>=', $param['start_time']];
        }

        if (isset($param['end_time']) && $param['end_time']) {
            $where[] = ['a.appointment_date', '<=', $param['end_time']];
        }

        if (isset($param['appointment_date']) && $param['appointment_date']) {
            $where[] = ['a.appointment_date', '=', $param['appointment_date']];
        }

        $data = db('appointments')
            ->alias('a')
            ->join('psychologist p', 'a.psychologist_id = p.id', 'left')
            ->where($where)
            ->where('a.user_id','>',0)
            ->leftJoin('user u', 'a.user_id = u.id')
            ->field('a.*, p.name as psychologist_name,u.name as user_name')
            ->order('a.appointment_date desc')
            ->select()
            ->toArray();

        $result = [];

        foreach ($data as $v){
            $row = [];
            $row['心理老师'] = $v['psychologist_name'];
            $row['预约人'] = $v['user_name'];
            $row['预约时间'] = $v['appointment_date'].'-'.$v['start_time'].':'.$v['end_time'];
            $row['预约地址'] = $v['address'];
            $row['预约信息'] = $v['user_form'];

            //建档的 信息

            /*在进入大学前，你的主要生活区域是？
                你的家庭结构是？
                日常生活中你的主要抚养人是？
                你目前在校期间的身份包含以下哪些角色？
                你认为自己的性格特点是？
                你认为自己的压力主要来源于以下哪些方面？
                你是否经历过以下重大事件？
                你是否有过往（包含生理和心理）的疾病史？
                你是否关注并了解自己的心理健康状态？*/
            $result[] = $row;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $columnIndex = 'A';
        foreach ($result[0] as $key => $value) {
            $sheet->setCellValue($columnIndex.'1', $key);
            $columnIndex++;
        }

        foreach ($result as $k=>$row) {
            $k+=2;
            $columnIndex = 'A';
            foreach ($row as $cellValue) {
                $sheet->setCellValue($columnIndex.$k, $cellValue);
                $columnIndex++;
            }
        }
        $excelName = '预约排期'.date('Y-m-d H:i:s');

        // 设置HTTP头，告诉浏览器这是一个Excel文件
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$excelName.'.xlsx"');
        header('Cache-Control: max-age=0');

        // 创建Excel写入器并直接将内容发送到浏览器
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');


        return $data;
    }


    /**
     * 保存咨询记录
     * @groupRequired
     * @permission('保存咨询记录','咨询师')
     * @return Json
     */
    public function saveReport(): Json
    {
        $data = input('post.');
        
        // 校验必要参数
        if (!isset($data['appointment_id']) || !$data['appointment_id']) {
            return writeJson(400, '', '预约ID不能为空');
        }
        
        if (!isset($data['form']) || empty($data['form'])) {
            return writeJson(400, '', '咨询记录内容不能为空');
        }
        
        $appointmentId = $data['appointment_id'];
        $form = $data['form'];
        
        // 校验预约是否存在
        $appointment = db('appointments')->where('id', $appointmentId)->find();
        if (!$appointment) {
            return writeJson(400, '', '预约不存在');
        }
        
        // 校验form数据基本字段
        $requiredFields = ['name', 'gender', 'identity', 'schoolClass', 'source'];
        foreach ($requiredFields as $field) {
            if (!isset($form[$field]) || trim($form[$field]) === '') {
                return writeJson(400, '', "字段 {$field} 不能为空");
            }
        }
        
        try {
            // 将form数据转换为JSON格式保存
            $logFormJson = json_encode($form, JSON_UNESCAPED_UNICODE);
            
            // 更新预约表的log_form字段
            $result = db('appointments')
                ->where('id', $appointmentId)
                ->update([
                    'log_form' => $logFormJson,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            
            if ($result) {
                Hook::listen('logger', '保存咨询记录，预约ID=' . $appointmentId);
                return writeJson(200, '', '咨询记录保存成功');
            } else {
                return writeJson(400, '', '咨询记录保存失败');
            }
            
        } catch (\Exception $e) {
            Hook::listen('logger', '保存咨询记录失败：' . $e->getMessage());
            return writeJson(500, '', '系统错误，请稍后重试');
        }
    }

    /**
     * 取消预约
     * @groupRequired
     * @permission('取消预约','咨询师')
     * @param int $id
     * @return Json
     */
    public function cancel($id): Json
    {

        $data = Db::name('appointments')->where('id', $id)->find();

        if (!$data) {
            return writeJson(400, [], '预约不存在');
        }

        $user = Db::name('user')->where('id', $data['user_id'])->find();

        if (!$user) {
            return writeJson(400, [], '用户不存在');
        }


        // Start transaction



        Db::startTrans();

        try {
            $map = [
                'id' => $id
            ];

            $res = Db::name('appointments')->where($map)->lock(true)->find();
            if (!$res){
                return writeJson(400, [], '预约不存在');
            }

            if ($res['user_id'] != $user['id']){
                return writeJson(400, [], '用户不存在');
            }

            if ($res['appointment_date'] < date('Y-m-d')){
                return writeJson(400, [], '预约已过期');
            }

            //更新user_id and version
            $res = Db::name('appointments')->where($map)->update(['user_id' => null, 'version' => $res['version'] + 1]);
            //记录日志

            Hook::listen('logger', "取消了用户ID为".$user['id']."的预约");

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return writeJson(400, [], $e->getMessage());
        }


        //发送取消预约通知 easywechat

        $config = Config::get("wx.yujian"); // 加载微信配置
        $app = Factory::miniProgram($config);

        /*预约人
{{name1.DATA}}

预约地点
{{thing2.DATA}}

预约时间
{{date3.DATA}}

温馨提示
{{thing8.DATA}}*/
        $data = [
            'name1' => $user['name'],
            'thing2' => $data['address'],
            'date3' => $data['appointment_date'].' '.$data['start_time'].':'.$data['end_time'],
            'thing8' => '您的预约因咨询老师排班调整已被取消，请重新预约'
        ];
        $result = [];

        if ($res){
            $result = $app->subscribe_message->send([
                'touser' => $user['openid'],
                'template_id' => $config['template_id'],
                'page' => 'pages/home/index',
                'data' => $data,
            ]);


            // 如果发送失败 ， 就发送服务号的模板消息

            /*            if ($result['errcode'] != 0 && $user['wx_openid']){
                $app = Factory::officialAccount($config);
                $result = $app->template_message->send([
                    'touser' => $user['wx_openid'],
                    'template_id' => $config['service_template_id'],
                    'data' => $data,
                ]);
            }*/



        }


        return writeJson(200, $result, '取消成功');
    }

}
