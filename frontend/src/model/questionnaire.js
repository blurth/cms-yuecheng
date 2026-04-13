/* eslint-disable class-methods-use-this */
import _axios, { get, _delete, post } from '@/lin/plugin/axios'

// 我们通过 class 这样的语法糖使模型这个概念更加具象化，其优点：耦合性低、可维护性。
class Questionnaire {
  // 类中的方法可以代表一个用户行为
  async createBook(data) {
    return _axios({
      method: 'post',
      url: 'v1/book',
      data,
    })
  }

  /**
   * 反馈列表
   */
  async getTipList(data) {
    const { result } = await get('v1/question/tip_list', data)
    return result
  }

  async tipDelete(data) {
    const { result } = await _delete('v1/question/tip_delete', data)
    return result
  }

  /**
   * 添加反馈
   * @param {*} data
   * @returns
   */
  async tipSave(data) {
    const res = await post('v1/question/tip_save', data)
    return res
  }

  /**
   * 反馈详情
   * @param {*} data
   * @returns
   */

  async tipDetail(data) {
    const { result } = await get('v1/question/tip_detail', data)
    return result
  }

  /**
   * 反馈列表
   */
  async getPushList(data) {
    const { result } = await get('v1/exam/push_list', data)
    return result
  }

  /**
   * 反馈列表
   */
  async getQuestionist_api(data) {
    const { result } = await get('v1/question/list', data)
    return result
  }

  /**
   * 删除题库
   */
  async delQuestionist_api(id) {
    const res = await _delete(`v1/question/${id}`)
    return res
  }

  /**
   *
   */
  async postQuestionistSave_api(data) {
    const { result } = await post('v1/question/save', data)
    return result
  }

  /**
   *问卷分类
   */

  async getQuestionCategoryList_api(data) {
    const { result } = await get('v1/question_category/list', data)
    return result
  }
}

export default new Questionnaire()
