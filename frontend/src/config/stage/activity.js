const activityRouter = {
    route: null,
    name: null,
    title: '活动管理',
    type: 'folder', // 类型: folder, tab, view
    icon: 'iconfont el-icon-postcard',
    filePath: 'view/activity/', // 文件路径
    order: null,
    inNav: true,
    children: [
        // constructor() {}
        {
            title: '添加活动',
            type: 'view',
            name: 'activityCreate',
            route: '/activity/add',
            filePath: 'view/activity/add.vue',
            inNav: true,
            icon: 'iconfont icon-tushuguanli',
        },
        {
            title: '活动列表',
            type: 'view',
            name: 'activityList',
            route: '/activity/list',
            filePath: 'view/activity/list.vue',
            inNav: true,
            icon: 'iconfont icon-tushuguanli',
        }
    ],
}

export default activityRouter
