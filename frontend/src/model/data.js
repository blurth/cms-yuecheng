import { get } from '@/lin/plugin/axios'

// 我们通过 class 这样的语法糖使模型这个概念更加具象化，其优点：耦合性低、可维护性。
export default class Data {
  static async getQiniuToken() {
    const { result } = await get('v1/token/qn')
    //保存到本地缓存
    localStorage.setItem('qiniuToken', result)
    return result
  }

  /**
   * 获取省
   * @returns
   */
  static async getProvinces() {
    const { result } = await get('v1/district/provinces')
    return result
  }

  /**
   * 获取城市
   * @param {*} code
   * @returns
   */
  static async geCities(code) {
    const { result } = await get(`v1/district/cities/${code}`)
    return result
  }

  /**
   * 获取区
   * @param {*} code
   * @returns
   */
  static async getDistricts(code) {
    const { result } = await get(`v1/district/districts/${code}`)
    return result
  }
  /**
   * 获取年级
   * @param {*} id
   * @returns
   */

  static async getGrade(id) {
    const { result } = await get(`v1/school/grade?id=${id}`)
    return result
  }

  /**
   * 获取班级
   * @param {*} id
   * @param {*} gradeId
   * @returns
   */
  static async getClass(id, gradeId) {
    const { result } = await get(`v1/school/class?school_id=${id}&grade_id=${gradeId}`)
    return result
  }
}
