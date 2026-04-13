const Appointments = {
    route: null,
    name: null,
    title: '预约管理',
    type: 'folder', // 类型: folder, tab, view
    icon: 'iconfont el-icon-postcard',
    filePath: 'view/appointments/', // 文件路径
    order: null,
    inNav: true,
    children: [
        // constructor() {}
        {
            title: '添加排期',
            type: 'view',
            name: 'schedule',
            route: '/appointments/add',
            filePath: 'view/appointments/schedule.vue',
            inNav: true,
            icon: 'iconfont icon-tushuguanli',
        },
        {
            title: '预约列表',
            type: 'view',
            name: 'appointments',
            route: '/appointments/appointments',
            filePath: 'view/appointments/appointments.vue',
            inNav: true,
            icon: 'iconfont icon-tushuguanli',
        }
    ],
}

export default Appointments
