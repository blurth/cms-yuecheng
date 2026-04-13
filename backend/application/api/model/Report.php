<?php

namespace app\api\model;

class Report extends BaseModel
{
    public function school()
    {
        return $this->belongsTo('School','school_id' , 'id');
    }
}