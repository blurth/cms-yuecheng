<?php

namespace app\api\controller\v1;

use app\api\model\NewsModel;
use think\response\Json;

class News extends BaseController
{
    /*CREATE TABLE `yu_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '新闻标题',
  `publisher` varchar(100) NOT NULL COMMENT '发布人',
  `publish_time` datetime NOT NULL COMMENT '发布时间',
  `content` longtext COMMENT '新闻内容（富文本）',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `cover` varchar(255) DEFAULT NULL COMMENT '封面',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;*/
    public function list(): Json
    {
        $data = db('news')
            ->field('id,title,publisher,publish_time,cover')
            ->order('publish_time desc')
            ->paginate(input('get.size', 10));

        return writeJson(200, $data);
    }

    public function save(): Json
    {
        $data = input('post.');


        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        $model = new NewsModel();

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

        $model = new NewsModel();
        $success = $model->where('id', $id)->delete();

        if ($success) {
            return writeJson(200, '', '成功');
        } else {
            return writeJson(400, '', '失败');
        }
    }


    public function detail($id): Json
    {
        $data = db('news')
            ->where('id', $id)
            ->find();

        return writeJson(200, $data);
    }
}