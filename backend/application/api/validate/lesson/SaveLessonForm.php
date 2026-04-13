<?php

namespace app\api\validate\lesson;

use LinCmsTp5\validate\BaseValidate;

class SaveLessonForm extends BaseValidate
{
    protected $rule = [
        'id' => 'integer',
        'title|标题' => 'require|max:100',
    ];
}