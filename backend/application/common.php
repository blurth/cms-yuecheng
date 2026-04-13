<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use LinCmsTp5\exception\ParameterException;
use think\response\Json;

/**
 * 统一响应包装函数
 * @param $code
 * @param $errorCode
 * @param $data
 * @param $msg
 * @return Json
 */
function writeJson($code, $data, $msg = 'ok', $errorCode = 0)
{
    $data = [
        'code' => $errorCode,
        'result' => $data,
        'message' => $msg
    ];
    return json($data, $code);
}

/**
 * 分页参数处理函数
 * @param int $count
 * @param int $page
 * @return array
 * @throws ParameterException
 */
function paginate(int $count = 10, int $page = 0)
{
    // $count = intval(Request::get('count', $count));
    // $start = intval(Request::get('page', $page));
    // $page = $start;
    $count = $count >= 15 ? 15 : $count;
    $start = $page * $count;

    if ($start < 0 || $count < 0) throw new ParameterException();

    return [$start, $count];
}

/**
 * 权限数组格式化函数
 * @param array $permissions
 * @return array
 */
function formatPermissions(array $permissions)
{
    $groupPermission = [];
    foreach ($permissions as $permission) {
        $item = [
            'permission' => $permission['name'],
            'module' => $permission['module']
        ];
        $groupPermission[$permission['module']][] = $item;
    }
    $result = [];
    foreach ($groupPermission as $key => $item) {
        array_push($result, [$key => $item]);
    }

    return $result;
}


/**
 * 多表字段
 * @param array $fields = [
 *                      'a' => ['name', 'color', 'add_time'],
 *                      'b' => ['id', 'img'],
 *                      'c' => ['name admin_name']
 *                      ];
 * @return string
 */
function mt_fields(array $fields): string
{
    $fields_str = '';
    foreach ($fields as $k => $v) {
        if (!is_array($v)) {
            exit(__FILE__ . ': ' . __LINE__ . ' mt_fields：字段必须是数组');
        }
        foreach ($v as $item)
            $fields_str .= $k . '.' . $item . ',';
    }
    return substr($fields_str, 0, strlen($fields_str) - 1);
}
/**
 * 根据身份证号获取年龄
 * @param $idCard
 * @return int
 */
function getAge($idCard): int
{
    $birthDate = strtotime(substr($idCard, 6, 8));
    $year = date('Y', $birthDate);
    $month = date('m', $birthDate);
    $day = date('d', $birthDate);

    //当前的年月日
    $currentYear = date('Y');
    $currentMonth = date('m');
    $currentDay = date('d');

    //计算年龄
    $age = intval($currentYear - $year);
    if ($month > $currentMonth || $month == $currentMonth && $day > $currentDay) {
        $age--;
    }

    return $age;
}

/**
 * 根据身份证号获取性别
 * @param $idCard
 * @return int
 */
function getSex($idCard): int
{
    if (!$idCard){
        halt($idCard);
    }
    $sexNum = substr($idCard, 4, 1);
    try {
        return $sexNum % 2 === 0 ? 2 : 1;

    }catch (\Exception $e){

        halt($idCard);
    }
}


function getGrade($entranceYear, $schoolType)
{
    try {


    // 这里可以根据具体的逻辑来判断年级
    // 计算入学年级
    $currentYear = date('Y');
    $currentMonth = date('m');
    $gradeNumber = $currentYear - $entranceYear;

    //判断当前是否超过9月,如果超过9月，表示已经提前升入下一学年
    if($currentMonth > 8){
        $gradeNumber += 1;
    }

    // 定义不同学校类型对应的年级字符串
    $gradeMap = [
        '小学' => ['一年级', '二年级', '三年级', '四年级', '五年级', '六年级'],
        '初中' => ['初一', '初二', '初三', '高中预备年级'],
        '高中' => ['高一', '高二', '高三', '大学预备年级'],
        '大学' => ['大一', '大二', '大三', '大四', '毕业'],
    ];

    // 获取学校类型对应的年级数组
    $grades = $gradeMap[$schoolType] ?? [];

    // 如果对应的年级数组为空或者入学年级小于1或大于数组元素个数，则返回“未知”
    if (empty($grades) || $gradeNumber < 1 || $gradeNumber > count($grades)) {
        $grade = '未知';
    } else {
        // 获取对应年级字符串
        $grade = $grades[$gradeNumber - 1];
    }
        return $grade;
    } catch (\Exception $e){
        return "***";
    }
}

if (!function_exists('dd')) {
    function dd(...$vars) {
        foreach ($vars as $v) {
            \Symfony\Component\VarDumper\VarDumper::dump($v);
        }
        die(1);
    }
}
