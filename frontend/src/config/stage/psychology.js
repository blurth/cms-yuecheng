const PsychologyRoute = {
    route: null,
    name: null,
    title: '心理老师管理',
    type: 'folder', // 类型: folder, tab, view
    icon: 'iconfont icon-tushuguanli',
    filePath: 'view/book/', // 文件路径
    order: null,
    inNav: true,
    children: [
        {
            title: '心理老师列表',
            type: 'view',
            name: 'BookCreate',
            route: '/psychology/list',
            filePath: 'view/psychology/list.vue',
            inNav: true,
            icon: 'iconfont icon-tushuguanli',
        },
        {
            title: '添加心理老师',
            type: 'view',
            name: 'psychologyCreate',
            route: '/psychology/save',
            filePath: 'view/psychology/save.vue',
            inNav: true,
            icon: 'iconfont icon-tushuguanli',
        }
    ],
}
export default PsychologyRoute
