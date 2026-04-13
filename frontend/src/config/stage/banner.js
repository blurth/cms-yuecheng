const bannerRouter = {
  route: null,
  name: null,
  title: '轮播图管理',
  type: 'folder', // 类型: folder, tab, view
  icon: 'iconfont el-icon-postcard',
  filePath: 'view/banner/', // 文件路径
  order: null,
  inNav: true,
  children: [
    {
      title: '轮播图列表',
      type: 'view',
      name: 'BannerList',
      route: '/banner/list',
      filePath: 'view/banner/banner-list.vue',
      inNav: true,
      icon: 'iconfont icon-tushuguanli',
    },
    {
      title: '轮播图元素列表',
      type: 'view',
      name: 'BannerCreate',
      route: '/banner/item/list',
      filePath: 'view/banner/banner-item-list.vue',
      inNav: false,
      icon: 'iconfont icon-tushuguanli',
    },
    {
      title: '添加轮播图',
      type: 'view',
      name: 'BannerCreate',
      route: '/banner/add',
      filePath: 'view/banner/banner.vue',
      inNav: true,
      icon: 'iconfont icon-add',
    },
  ],
}

export default bannerRouter
