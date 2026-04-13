<?php

namespace app\api\controller\v1;

use app\api\model\Banner as BannerModel;
use app\api\model\BannerItem as BannerItemModel;
use think\exception\DbException;
use think\facade\Hook;
use think\facade\Request;
use think\response\Json;

class Banner
{
    /**
     * 获取轮播图主题详情
     * @throws DbException
     */
    public function detail($id): Json
    {

        $data = BannerModel::get($id);

        if (!empty($data)){
            return writeJson(200,$data);
        }

        return writeJson(200,$data);
    }


    /**
     * 获取轮播图列表 带查询
     * @throws DbException
     */
    public function getList(): Json
    {
        $where = [];
        $param = input('get.');

        if (isset($param['title'])){
            $where[] = ['title','like','%'.$param['title'].'%'];
            $where[] = ['description','like','%'.$param['title'].'%','or'];
        }
        $data = BannerModel::getList($where);

        if (!empty($data)){
            return writeJson(200,$data);
        }

        return writeJson(200,$data);
    }

    /**
     * 创建轮播图
     * @adminRequired
     * @permission('注册','管理员','hidden')
     * */
    public function create(): Json
    {
        $params = Request::post();

        $params['items'] = json_decode($params['items'],true);

        $res = BannerModel::add($params);

        if ($res){
            Hook::listen('logger', "新建了轮播");

            return writeJson(201,'新建轮播成功');
        }
        return writeJson(400,'新建轮播失敗');
    }


    /**
     * 编辑或者创建轮播图基础信息
     *
     * */
    public function editOrCreateBanner(): Json
    {
        $params = input('post.');
        $BannerModel = new BannerModel();
        $where = [];
        if (isset($params['id']) && $params['id']){
            $where = ['id' => $params['id']];
        }
        $BannerModel->allowField(true)->save($params,$where);

        return writeJson(201, '', '成功');
    }


    /**
     * 根据banner_id 获取轮播图元素列表
     *
     */

    public function getBannerItemList($id): Json
    {

        $data = BannerItemModel::where('banner_id',$id)->select();

        if (!empty($data)){
            return writeJson(200,$data);
        }

        return writeJson(200,$data);
    }


/**
     * 新增轮播图元素 多列
     *
     */
    public function addBannerItem(): Json
    {
        $params = Request::post();
        $method = '新增';
        $BannerModel = new BannerItemModel();
        $where = [];
        if (isset($params['id']) && $params['id']){
            $where = ['id' => $params['id']];
            $method = '编辑';
        }

        $result = $BannerModel->allowField(true)->save($params,$where);


        if ($result){
            return writeJson(201, [], $method.'轮播图元素成功！');
        }
        return writeJson(201, [], $method.'轮播图元素失败！');
    }


    public function getBannerItemById($id): Json
    {
        $data = BannerItemModel::get($id);

        if (!empty($data)){
            return writeJson(200,$data);
        }

        return writeJson(200,$data);
    }




    /**
     * 删除轮播图主体
     *
     */
    public function delBanner(): Json
    {
        $ids = Request::delete('ids');
        // 传入多个id组成的数组进行批量删除
        BannerModel::destroy($ids);

        return writeJson(201, [], '轮播图元素删除成功！');
    }

    /**
     * @adminRequired
     * 删除轮播图元素
     */
    public function delBannerItem(): Json
    {
        $idsStr = Request::param('ids');

        $ids = explode(',', $idsStr);

        if (empty($ids)) {
            return writeJson(400, [], '删除的id不能为空！');
        }


        // 传入多个id组成的数组进行批量删除
        $res = BannerItemModel::destroy($ids);

        if (!$res) {
            return writeJson(400, [], '轮播图元素删除失败！');
        }

        Hook::listen('logger', "删除了轮播id为");
        return writeJson(201, [], '轮播图元素删除成功！');
    }



}