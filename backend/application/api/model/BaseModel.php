<?php
/**
 * Created by PhpStorm.
 * User: 沁塵
 * Date: 2019/2/19
 * Time: 11:22
 */

namespace app\api\model;


use think\Model;

class BaseModel extends Model
{
    protected $hidden = ['delete_time'];
    protected $dateFormat = 'Y-m-d';
    protected $type = ['add_time' => 'timestamp'];


}