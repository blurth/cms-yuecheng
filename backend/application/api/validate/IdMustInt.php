<?php

namespace app\api\validate;

use LinCmsTp5\validate\BaseValidate;
class IdMustInt extends BaseValidate
{
    protected $rule = [
        'id' => 'integer',
    ];
}