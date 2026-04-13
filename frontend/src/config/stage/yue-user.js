const yueUserRouter = {
  route: null,
  name: null,
  title: '用户管理',
  type: 'folder',
  icon: 'iconfont el-icon-user',
  filePath: 'view/yue-user/',
  order: null,
  inNav: true,
  children: [
    {
      title: '用户列表',
      type: 'view',
      name: 'yueUserList',
      route: '/yue-user/list',
      filePath: 'view/yue-user/yue-user-list.vue',
      inNav: true,
      icon: 'iconfont icon-tushuguanli',
    },
  ],
}

export default yueUserRouter
