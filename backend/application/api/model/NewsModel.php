<?php

namespace app\api\model;

class NewsModel extends BaseModel
{
    protected $name = 'news';
    protected $hidden = ['delete_time'];
    protected $dateFormat = 'Y-m-d';
    protected $type = ['add_time' => 'timestamp'];
}