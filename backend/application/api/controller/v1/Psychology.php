<?php

namespace app\api\controller\v1;

use app\api\model\Psychologist;
use app\api\model\SchoolTeacherMental;
use app\api\service\admin\User as UserService;
use think\Db;
use think\exception\DbException;
use think\facade\Hook;
use think\response\Json;

class Psychology extends BaseController
{

    public function detail($id): Json
    {
        $data = db('psychologist')
            ->where('id', $id)
            ->find();

        return writeJson(200, $data);
    }
    /**
     * 获取心理老师列表
     *
     * @return Json 返回Json格式的心理老师列表
     * @throws DbException
     */
    public function list(): Json
    {
        $map = [];

        if (input('get.name')) {
            $map[] = ['name', 'like', '%' . trim(input('get.name')) . '%'];
        }

        $map[] = ['id', 'in', $this->psychologist_id];

        $res = Db::name('psychologist')
            ->where($map)
            ->whereNull('delete_at')
            ->paginate(input('get.size','10'));

        return writeJson(200,$res);
    }

    /**
     * 更新或新增心理老师信息
     *
     * @return Json 返回操作结果，Json格式
     */
    public function save(): Json
{
    $data = input('post.');
    $data['created_at'] = date('Y-m-d H:i:s');
    $data['updated_at'] = date('Y-m-d H:i:s');

    $model = new Psychologist();

    // If id exists, update, else insert
    if (isset($data['id']) && $data['id']) {
        // Update operation
        $success = $model->isUpdate(true)->save($data, ['id' => $data['id']]);
        
        if ($success) {
            return writeJson(200, '', '操作成功');
        } else {
            return writeJson(400, '', '操作失败');
        }
    } else {
        // Insert operation - 新增心理老师
        $success = $model->save($data);
        
        if ($success) {
            $psychologistId = $model->id;
            
            // 同步创建管理员账号
            try {
                $adminData = [
                    'username' => $data['name'], // 账号使用老师名称
                    'password' => '666666', // 默认密码
                    'nickname' => $data['name'],
                    'psychologist_id' => $psychologistId // 关联心理老师ID
                ];
                
                $adminUser = UserService::createUser($adminData);
                
                // 更新心理老师表，添加关联的管理员ID
                $model->save(['admin_user_id' => $adminUser->id], ['id' => $psychologistId]);

                //添加咨询师分组CREATE TABLE `kai_lin_user_group` (
                //  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                //  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
                //  `group_id` int(10) unsigned NOT NULL COMMENT '分组id',
                //  PRIMARY KEY (`id`) USING BTREE,
                //  KEY `user_id_group_id` (`user_id`,`group_id`) USING BTREE COMMENT '联合索引'
                //) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;

                $groupData = [
                    'user_id' =>  $adminUser->id,
                    'group_id' => 5,
                ];
                Db::name('lin_user_group')->insert($groupData);


                Hook::listen('logger', '新增心理老师并创建管理员账号，心理老师ID=' . $psychologistId . '，管理员ID=' . $adminUser->id);
                
            } catch (\Exception $e) {
                // 如果创建管理员失败，记录日志但不影响心理老师创建
                Hook::listen('logger', '创建心理老师管理员账号失败：' . $e->getMessage());
            }
            
            return writeJson(200, '', '操作成功');
        } else {
            return writeJson(400, '', '操作失败');
        }
    }
}

    /**
     * 删除指定id的心理老师
     *
     * @param int $id 要删除的心理老师的id
     * @return Json 返回操作结果，Json格式
     */
    public function delete(int $id): Json
    {
        //改成 软删除 ， 更新 is_delete
        $res = db('psychologist')->where('id', $id)->update(['delete_at' => 1]);

        //记录日志
        Hook::listen('logger', '删除心理老师，id=' . $id);
        if ($res) {
            return writeJson(200, '', '删除成功');
        } else {
            return writeJson(400, '', '删除失败');
        }
    }
}