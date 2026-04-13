// import { post, get, put } from '@/lin/plugin/axios'
import _axios, { get, _delete, post } from '@/lin/plugin/axios'

export default class Banner {
  /**
   * 保存修改轮播图
   * @param {*} data
   * @returns
   */
  static async saveBanner(data) {
    const info = await post('v1/banner/save', {
      ...data,
    })
    return info
  }

  /**
   * 保存修改轮播图元素
   * @param {*} data
   * @returns
   */
  static async saveBannerItem(data) {
    const info = await post('v1/banner/add_item', {
      ...data,
    })
    return info
  }

  /**
   * 获取轮播图列表
   * @param {*} data
   * @returns
   */
  static async getBanner(data) {
    const info = await get('v1/banner', {
      ...data,
    })
    return info
  }

  /**
   * 获取轮播图元素列表
   * @param {*} data
   * @returns
   */
  static async getBannerItem(id) {
    const { result } = await get(`v1/banner/item_list/${id}`)
    return result
  }

  /**
   *获取轮播图主体详情
   * @param {*} id
   * @returns
   */
  static async getBannerDetail(id) {
    const { result } = await get(`v1/banner/${id}`)
    return result
  }

  /**
   *获取轮播图元素详情
   * @param {*} id
   * @returns
   */
  static async getBannerItemDetail(id) {
    const { result } = await get(`v1/banner/banner_item/${id}`)
    return result
  }

  /**
   * 删除轮播图主体
   * @param {*} ids
   * @returns
   */
  static async deleteBanner(data) {
    // const info = await _delete('v1/banner/del_banner', {
    //   ...data,
    // })
    // return info

    return _axios({
      method: 'delete',
      url: 'v1/banner/del_banner',
      data,
    })
  }

  /**
   * 删除轮播图元素
   * @param {*} ids
   * @returns
   */
  static async deleteBannerItem(data) {
    const info = await _delete('v1/banner/del_bannerItem', {
      ...data,
    })
    return info
  }
}
