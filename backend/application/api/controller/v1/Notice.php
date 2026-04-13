<?php

namespace app\api\controller\v1;

use app\api\model\NotifcationModel;
use think\response\Json;

class Notice extends BaseController
{
    public function list(): Json
    {
        $data = db('notification')
            ->field('id,user_id,notification_type,message,is_read,key_id,url')
            ->order('created_at desc')
            ->paginate(input('get.size', 10));

        return writeJson(200, $data);
    }

    public function save(): Json
    {
        $data = input('post.');


        $data['created_at'] = date('Y-m-d H:i:s');

        $model = new NotifcationModel();

        // If id exists, update, else insert

        if (isset($data['id']) && $data['id']) {
            // Update operation
            $success = $model->isUpdate(true)->save($data, ['id' => $data['id']]);
        } else {
            // Insert operation
            $success = $model->save($data);
        }

        if ($success) {
            return writeJson(201, '', '成功');
        } else {
            return writeJson(400, '', '失败');
        }
    }

    public function delete($id): Json
    {

        $model = new NotifcationModel();
        $success = $model->where('id', $id)->delete();

        if ($success) {
            return writeJson(200, '', '成功');
        } else {
            return writeJson(400, '', '失败');
        }
    }


    public function detail($id): Json
    {
        $data = db('notification')
            ->where('id', $id)
            ->find();

        return writeJson(200, $data);
    }
}