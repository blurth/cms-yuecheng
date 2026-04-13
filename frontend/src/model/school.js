import {_delete, get, post} from '@/lin/plugin/axios'

export default class School {
  /**
   * 获取学校列表
   * @param {*} data
   * @returns
   */
  static async getSchoolList(data) {
    const { result } = await get('v1/school/list', {
      ...data,
    })
    return result
  }

  /**
   * 获取学校详情
   * @param {*} id
   * @returns
   */
  static async getSchoolDetail(id) {
    const { result } = await get(`v1/school/detail?id=${id}`)
    return result
  }

  /**
   * 获取学校Logo
   * @param {*} id
   * @returns
   */
  static async getMySchoolLogo(id) {
    const { result } = await get(`v1/school/logo`)
    return result
  }

  /**
   * 删除学校
   * @param {*} ids
   * @returns
   */
  static async deleteSchool(id) {
    const info = await _delete(`v1/school/${id}`)
    return info
  }

  /**
   * 创建学校或修改学校
   * @param {*} ids
   * @returns
   */
  static async saveSchool(data) {
    const info = await post('v1/school/save', {
      ...data,
    })
    return info
  }


  /**
   * 发送问卷
   * @param data
   * @returns
   */
  static async sendRecord(data) {
    return post('v1/exam_record/send_record', {
      ...data,
    });
  }
}
