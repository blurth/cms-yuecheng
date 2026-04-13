<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\facade\Route;

Route::get('v1/test', 'api/v1.Test/index');

Route::group('', function () {
    Route::group('cms', function () {
        // 账户相关接口分组
        Route::group('user', function () {
            // 登陆接口
            Route::post('login', 'api/cms.User/userLogin');
            // 刷新令牌
            Route::get('refresh', 'api/cms.User/refreshToken');
            // 查询自己拥有的权限
            Route::get('permissions', 'api/cms.User/getAllowedApis');
            // 注册一个用户
            Route::post('register', 'api/cms.User/register');
            // 查询自己信息
            Route::get('information', 'api/cms.User/getInformation');
            // 用户更新信息
            Route::put('', 'api/cms.User/update');
            // 修改自己密码
            Route::put('change_password', 'api/cms.User/changePassword');
        });
        // 管理类接口
        Route::group('admin', function () {
            // 查询所有可分配的权限
            Route::get('permission', 'api/cms.Admin/getAllPermissions');
            // 查询所有用户
            Route::get('users', 'api/cms.Admin/getAdminUsers');
            // 修改用户密码
            Route::put('user/:id/password', 'api/cms.Admin/changeUserPassword');
            // 删除用户
            Route::delete('user/:id', 'api/cms.Admin/deleteUser');
            // 更新用户信息
            Route::put('user/:id', 'api/cms.Admin/updateUser');
            // 查询所有权限组
            Route::get('group/all', 'api/cms.Admin/getGroupAll');
            // 新增权限组
            Route::post('group', 'api/cms.Admin/createGroup');
            // 查询指定分组及其权限
            Route::get('group/:id', 'api/cms.Admin/getGroup');
            // 更新一个权限组
            Route::put('group/:id', 'api/cms.Admin/updateGroup');
            // 删除一个分组
            Route::delete('group/:id', 'api/cms.Admin/deleteGroup');
            // 删除多个权限
            Route::post('permission/remove', 'api/cms.Admin/removePermissions');
            // 分配多个权限
            Route::post('permission/dispatch/batch', 'api/cms.Admin/dispatchPermissions');

        });
        // 日志类接口
        Route::group('log', function () {
            Route::get('', 'api/cms.Log/getLogs');
            Route::get('users', 'api/cms.Log/getUsers');
            Route::get('search', 'api/cms.Log/getUserLogs');
        });
        //上传文件类接口
        Route::post('file', 'api/cms.File/postFile');
        // 用户管理接口
        Route::group('yue-user', function () {
            Route::get('', 'api/cms.YueUser/getList');
            Route::get(':id', 'api/cms.YueUser/getDetail');
            Route::put(':id/status', 'api/cms.YueUser/toggleStatus');
            Route::put(':id', 'api/cms.YueUser/update');
            Route::delete(':id', 'api/cms.YueUser/delete');
        });
        // 预警管理接口
        Route::group('crisis-event', function () {
            Route::get('', 'api/cms.CrisisEvent/getList');
            Route::get(':id', 'api/cms.CrisisEvent/getDetail');
            Route::put(':id/status', 'api/cms.CrisisEvent/updateStatus');
        });
    });
    Route::group('v1', function () {

        //轮播图
        Route::group('banner', function () {
            //查询轮播图详情
            Route::get(':id', 'api/v1.Banner/detail');
            // 查询所有轮播图
            Route::get('', 'api/v1.Banner/getList');
            //查询轮播图元素详情
            Route::get('banner_item/:id', 'api/v1.Banner/getBannerItemById');
            // 查询轮播图元素列表
            Route::get('item_list/:id', 'api/v1.Banner/getBannerItemList');
            // 更新轮新建播图主体
            Route::post('save', 'api/v1.Banner/editOrCreateBanner');
            // 新建and更新播图元素
            Route::post('add_item', 'api/v1.Banner/addBannerItem');
            // 删除轮播图主体
            Route::delete('del_banner', 'api/v1.Banner/delBanner');
            // 删除轮播图元素
            Route::delete('del_bannerItem', 'api/v1.Banner/delBannerItem');
        });

        //预约管理
        Route::group('appointment', function () {
            // 查询问题分类
            Route::get('detail/:id', 'api/v1.Appointment/detail');
            //预约列表
            Route::get('list', 'api/v1.Appointment/list');
            //导出预约列表
            Route::get('export_excel', 'api/v1.Appointment/exportList');
            //添加预约
            Route::post('save', 'api/v1.Appointment/save');
            //保存咨询记录
            Route::post('saveReport', 'api/v1.Appointment/saveReport');
            //删除预约
            Route::delete('/:id', 'api/v1.Appointment/delete');
            //取消预约
            Route::post('cancel/:id', 'api/v1.Appointment/cancel');
        });


        //心理老师
        Route::group('psychology', function () {
            //详情
            Route::get('detail/:id', 'api/v1.Psychology/detail');
            //列表
            Route::get('list', 'api/v1.Psychology/list');
            //添加及修改
            Route::post('save', 'api/v1.Psychology/save');
            //删除
            Route::delete('/:id', 'api/v1.Psychology/delete');
        });

        //课程管理
        Route::group('lesson', function () {
            // 查询所有课程
            Route::get('list', 'api/v1.Lesson/getLessonList');

            // 查询所有课程章节
            Route::get('sections', 'api/v1.Lesson/getSectionListByPid');

            Route::get('detail/:id', 'api/v1.Lesson/getLessonDetailById');

            Route::get('section_detail/:id', 'api/v1.Lesson/getSectionDetailById');

            Route::post('save', 'api/v1.Lesson/createOrUpdate');

            Route::post('section_save', 'api/v1.Lesson/createOrSaveSection');

            Route::delete('delete', 'api/v1.Lesson/delete');

            Route::delete('delete_sections', 'api/v1.Lesson/deleteSections');

        });

        Route::group('lessonAuthor', function () {
            // 查询所有课程作者
            Route::get('list', 'api/v1.LessonAuthor/getList');
            // 新增或者更新作者
            Route::post('save', 'api/v1.LessonAuthor/save');
            //删除作者
            Route::delete('delete', 'api/v1.LessonAuthor/delete');

        });

        Route::group('lessonCategory', function () {
            // 查询所有课程
            Route::get('list', 'api/v1.LessonCategory/getCateGory');

        });

        Route::group('feedback', function () {
            // 查询所有课程
            Route::get('list', 'api/v1.Feedback/getFeedBack');

        });

        Route::group('report', function () {

            Route::get('list', 'api/v1.Report/list');
            Route::post('save', 'api/v1.Report/save');

        });



        Route::group('token', function () {
            // 查询所有轮播图
            Route::get('qn', 'api/v1.Token/getQnToken');
        });


        //活动管理
        Route::group('activity', function () {
            // 查询所有活动
            Route::get('all', 'api/v1.Activity/list');
            // 查询活动详情
            Route::get('detail/:id', 'api/v1.Activity/detail');

            Route::get('exportRegisteredUsers/:id', 'api/v1.Activity/exportRegisteredUsers');

            Route::get('registeredUsers/:id', 'api/v1.Activity/getRegisteredUsersById');
            // 新增or更新活动
            Route::post('save', 'api/v1.Activity/save');

            // 删除活动
            Route::delete('/:id', 'api/v1.Activity/delete');
        });


        //新闻动态
        Route::group('news', function () {
            // 查询所有新闻动态
            Route::get('all', 'api/v1.News/list');
            // 查询新闻动态详情
            Route::get('detail/:id', 'api/v1.News/detail');
            // 新增or更新新闻动态
            Route::post('save', 'api/v1.News/save');
            // 删除新闻动态
            Route::delete('/:id', 'api/v1.News/delete');
        });


        //通知
        Route::group('notice', function () {
            // 获取所有通知
            Route::get('all', 'api/v1.Notice/list');
            // 获取通知详情
            Route::get('detail/:id', 'api/v1.Notice/detail');
            // 新增or更新通知
            Route::post('save', 'api/v1.Notice/save');
            // 删除通知
            Route::delete('/:id', 'api/v1.Notice/delete');
        });



    });
})->middleware(['Authentication', 'ReflexValidate'])->allowCrossDomain(true, $header = [
    'Access-Control-Allow-Credentials' => 'true',
    'Access-Control-Allow-Methods' => 'GET, POST, PATCH, PUT, DELETE',
    'Access-Control-Allow-Headers' => 'tag, Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With',
]);

