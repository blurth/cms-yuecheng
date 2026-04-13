<?php

namespace app\api\controller\cms;

use app\api\model\YueUser as YueUserModel;
use think\Request;

class YueUser
{
    /**
     * @adminRequired
     * @permission('查询用户列表','用户管理')
     */
    public function getList(Request $request)
    {
        $page = $request->get('page', 1);
        $count = $request->get('count', 10);
        $name = $request->get('name', '');
        $audit = $request->get('audit', '');

        $query = YueUserModel::where('is_delete', 0);

        if ($name) {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($audit !== '') {
            $query->where('audit', $audit);
        }

        $list = $query->field('id,nickname,name,phone,audit,last_login_time')
            ->order('id desc')
            ->paginate([
                'list_rows' => $count,
                'page' => $page
            ]);

        return [
            'items' => $list->items(),
            'total' => $list->total(),
            'page' => $page,
            'count' => $count
        ];
    }

    /**
     * @adminRequired
     * @permission('查询用户详情','用户管理')
     */
    public function getDetail(Request $request, $id)
    {
        $user = YueUserModel::where('id', $id)
            ->where('is_delete', 0)
            ->find();

        if (!$user) {
            return writeJson(404, null, '用户不存在');
        }

        return $user;
    }

    /**
     * @adminRequired
     * @permission('切换用户状态','用户管理')
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = YueUserModel::where('id', $id)->find();

        if (!$user) {
            return writeJson(404, null, '用户不存在');
        }

        $newStatus = $user->audit == 1 ? 2 : 1;
        $user->audit = $newStatus;
        $user->save();

        return writeJson(200, null, '状态切换成功');
    }

    /**
     * @adminRequired
     * @permission('更新用户','用户管理')
     */
    public function update(Request $request, $id)
    {
        $params = $request->put();

        $user = YueUserModel::where('id', $id)->find();

        if (!$user) {
            return writeJson(404, null, '用户不存在');
        }

        $user->save($params);

        return writeJson(200, null, '更新成功');
    }

    /**
     * @adminRequired
     * @permission('删除用户','用户管理')
     */
    public function delete(Request $request, $id)
    {
        $user = YueUserModel::where('id', $id)->find();

        if (!$user) {
            return writeJson(404, null, '用户不存在');
        }

        $user->is_delete = 1;
        $user->save();

        return writeJson(200, null, '删除成功');
    }
}
