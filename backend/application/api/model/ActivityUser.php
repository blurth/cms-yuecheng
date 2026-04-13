<?php

namespace app\api\model;

class ActivityUser extends BaseModel
{
    public function getUserFormAttr($value, $data)
    {
        if (empty($value)) {
            return [];
        }
        return json_decode($value, true);
    }

    public static function getRegisteredUsers($id)
    {
        $data = self::where('activity_id', $id)->select();
        return $data;
    }

}