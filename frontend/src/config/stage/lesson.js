const lessonRouter = {
  route: null,
  name: null,
  title: '课程管理',
  type: 'folder', // 类型: folder, tab, view
  icon: 'iconfont el-icon-postcard',
  filePath: 'view/lesson/', // 文件路径
  order: null,
  inNav: true,
  permission: ['查询所有日志'],
  children: [
    // constructor() {}
    {
      title: '课程列表',
      type: 'view',
      name: 'lessonCreate',
      route: '/lesson/list',
      filePath: 'view/lesson/lesson-list.vue',
      inNav: true,
      icon: 'iconfont icon-tushuguanli',
    },
    {
      title: '章节列表',
      type: 'view',
      name: 'lessonItemCreate',
      route: '/lesson/item/list',
      filePath: 'view/lesson/lesson-item-list.vue',
      inNav: false,
      icon: 'iconfont icon-tushuguanli',
    },
    {
      title: '讲师列表',
      type: 'view',
      name: 'authorCreate',
      route: '/lesson/author/list',
      filePath: 'view/lesson/author-list.vue',
      inNav: true,
      icon: 'iconfont icon-tushuguanli',
    },
    {
      title: '添加课程',
      type: 'view',
      name: 'lessonCreate',
      route: '/lesson/add',
      filePath: 'view/lesson/lesson.vue',
      inNav: true,
      icon: 'iconfont icon-add',
      permission: ['查询所有日志'],
    },
    {
      title: '添加章节',
      type: 'view',
      name: 'lessonItemCreate',
      route: '/lesson/add',
      filePath: 'view/lesson/lesson-item.vue',
      inNav: false,
      icon: 'iconfont icon-add',
    },
  ],
}

export default lessonRouter
