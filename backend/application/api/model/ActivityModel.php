<?php

namespace app\api\model;

use think\model\concern\SoftDelete;

class ActivityModel extends BaseModel
{
    protected $name = 'activity';

    use SoftDelete;

    protected $deleteTime = 'delete_at';

    //delete_time字段的类型是 datetime

    protected $type = [
        'delete_at' => 'datetime'
    ];
}