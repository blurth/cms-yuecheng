const newsRouter = {
    route: null,
    name: null,
    title: '动态管理',
    type: 'folder', // 类型: folder, tab, view
    icon: 'iconfont el-icon-postcard',
    filePath: 'view/news/', // 文件路径
    order: null,
    inNav: true,
    children: [
        // constructor() {}
        {
            title: '添加动态',
            type: 'view',
            name: 'newsCreate',
            route: '/news/add',
            filePath: 'view/news/add.vue',
            inNav: true,
            icon: 'iconfont icon-tushuguanli',
        },
        {
            title: '动态列表',
            type: 'view',
            name: 'newsList',
            route: '/news/list',
            filePath: 'view/news/list.vue',
            inNav: true,
            icon: 'iconfont icon-tushuguanli',
        }
    ],
}

export default newsRouter
