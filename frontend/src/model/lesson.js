// import { post, get, put } from '@/lin/plugin/axios'
import _axios, { get, _delete, post } from '@/lin/plugin/axios'

export default class Banner {
  /**
   * 获取课程列表
   * @param {*} data
   * @returns
   */
  static async getLessonList(data) {
    const info = await get('v1/lesson/list', {
      ...data,
    })
    return info
  }

  /**
   * 获取所有课程作者
   * @param {*} data
   * @returns
   */
  static async getLessonAuthorList(data) {
    const { result } = await get('v1/lessonAuthor/list', {
      ...data,
    })
    return result
  }

  /**
   *获取课程分类
   * @param {*}
   * @returns
   */
  static async getLessonCategoryn() {
    const { result } = await get('v1/lessonCategory/list')
    return result
  }

  /**
   * 删除课程
   * @param {*} ids
   * @returns
   */
  static async deleteLesson(data) {
    return _axios({
      method: 'delete',
      url: 'v1/lesson/delete',
      data,
    })
  }

  /**
   * 获取课程详情
   * @param {*} id
   * @returns
   */
  static async getlesson(id) {
    const { result } = await get(`v1/lesson/detail/${id}`)
    return result
  }

  /**
   * 删除课程章节
   * @param {*} ids
   * @returns
   */
  static async deleteLessonSections(data) {
    const info = await _delete('v1/lesson/delete_sections', {
      ...data,
    })
    return info
  }

  /**
   * 创建课程或修改课程
   * @param {*} ids
   * @returns
   */
  static async saveLesson(data) {
    const info = await post('v1/lesson/save', data)
    return info
  }

  /**
   * 创建课程章节或修改课程章节
   * @param {*} ids
   * @returns
   */
  static async saveLessonSection(data) {
    const info = await post('v1/lesson/section_save', {
      ...data,
    })
    return info
  }

  /**
   * 获取课程章节
   * @param {*} id
   * @returns
   */
  static async getLessonSections(id) {
    const { result } = await get(`v1/lesson/sections?id=${id}`)
    return result
  }

  /**
   * 获取课程章节详情
   * @param {*} id
   * @returns
   */
  static async getLessonSectionDetail(id) {
    const { result } = await get(`v1/lesson/section_detail/${id}`)
    return result
  }

  /**
   * 新增课程讲师
   * @param {*} data
   * @returns
   */
  static async saveLessonAuthor(data) {
    const info = await post('v1/lessonAuthor/save', data)
    return info
  }

  /**
   * 删除课程讲师
   * @param {*} data
   * @returns
   */
  static async deleteLessonAuthor(data) {
    const info = await _delete('v1/lessonAuthor/delete', data)
    return info
  }
}
