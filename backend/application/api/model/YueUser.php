<?php

namespace app\api\model;

class YueUser extends BaseModel
{
    protected $name = 'user';

    protected $type = [
        'last_login_time' => 'timestamp',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
