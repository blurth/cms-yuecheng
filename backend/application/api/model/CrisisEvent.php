<?php

namespace app\api\model;

class CrisisEvent extends BaseModel
{
    protected $name = 'crisis_event';

    protected $type = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
