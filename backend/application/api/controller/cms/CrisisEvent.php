<?php

namespace app\api\controller\cms;

use app\api\model\CrisisEvent as CrisisEventModel;
use think\Request;

class CrisisEvent
{
    /**
     * @adminRequired
     * @permission('查询预警列表','预警管理')
     */
    public function getList(Request $request)
    {
        $page = $request->get('page', 1);
        $count = $request->get('count', 10);
        $riskLevel = $request->get('risk_level', '');
        $status = $request->get('status', '');

        $query = CrisisEventModel::alias('ce')
            ->join('yue_user u', 'ce.user_id = u.id')
            ->field('ce.*,u.name as user_name,u.phone');

        if ($riskLevel !== '') {
            $query->where('ce.risk_level', $riskLevel);
        }

        if ($status !== '') {
            $query->where('ce.status', $status);
        }

        $list = $query->order('ce.id desc')
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
     * @permission('查询预警详情','预警管理')
     */
    public function getDetail(Request $request, $id)
    {
        $event = CrisisEventModel::alias('ce')
            ->join('yue_user u', 'ce.user_id = u.id')
            ->field('ce.*,u.name as user_name,u.phone,u.nickname')
            ->where('ce.id', $id)
            ->find();

        if (!$event) {
            return writeJson(404, null, '记录不存在');
        }

        return $event;
    }

    /**
     * @adminRequired
     * @permission('处理预警','预警管理')
     */
    public function updateStatus(Request $request, $id)
    {
        $status = $request->put('status');
        $handleNote = $request->put('handle_note', '');

        $event = CrisisEventModel::where('id', $id)->find();

        if (!$event) {
            return writeJson(404, null, '记录不存在');
        }

        $event->status = $status;
        if ($handleNote) {
            $event->handle_note = $handleNote;
        }
        $event->save();

        return writeJson(200, null, '处理成功');
    }
}
