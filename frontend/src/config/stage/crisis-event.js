const crisisEventRouter = {
  route: null,
  name: null,
  title: '预警管理',
  type: 'folder',
  icon: 'iconfont el-icon-warning',
  filePath: 'view/crisis-event/',
  order: null,
  inNav: true,
  children: [
    {
      title: '风险列表',
      type: 'view',
      name: 'crisisEventList',
      route: '/crisis-event/list',
      filePath: 'view/crisis-event/crisis-event-list.vue',
      inNav: true,
      icon: 'iconfont icon-tushuguanli',
    },
  ],
}

export default crisisEventRouter
