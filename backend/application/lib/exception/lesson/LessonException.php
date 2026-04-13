<?php

namespace app\lib\exception\lesson;

use LinCmsTp5\exception\BaseException;

class LessonException extends BaseException
{
    public $code = 404;
    public $msg = '课程不存在';
    public $error_code = 20000;
}