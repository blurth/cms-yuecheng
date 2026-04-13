<?php

namespace app\api\validate;

use LinCmsTp5\validate\BaseValidate;

class SchoolClass extends BaseValidate
{
    protected $rule = [
        'audit' => 'require',
        'title' => 'require',
        'school_id' => 'require',
        'grade_id' => 'require',
        'is_delete' => 'require',
    ];

    protected $message = [
        'audit' => '审核状态不能为空',
        'title' => '班级名称不能为空',
        'school_id' => '学校ID不能为空',
        'grade_id' => '年级ID不能为空',
        'is_delete' => '删除状态不能为空',
    ];
}