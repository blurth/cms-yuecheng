import _axios, { get, _delete, post } from '@/lin/plugin/axios'

export default class Student {
  /**
   * 获取学生列表
   * @param {*} data
   * @returns
   */
  static async getClassList(data) {
    const { result } = await get('v1/school/class', {
      ...data,
    })
    return result
  }

  /**
   * 获取班级详情
   * @returns
   * @param id
   */
  static async getClassDetailById(id) {
    const { result } = await get(`v1/school/class/${id}`)
    return result
  }
  /**
   * 获取问卷记录列表
   * @param {*} data
   * @returns
   */

  static async getQuestionnaireList(data) {
    const { result } = await get('v1/exam_record/list', {
      ...data,
    })
    return result
  }

  /**
   * 学生管理保存
   * @param {*} data
   * @returns
   */

  static async classSave(data) {
    const res = await post('v1/school/save_class', data)
    return res
  }

  /**
   * 获取学生详情
   * @param {*} id
   * @returns
   */
  static async getStudentDetail(id) {
    const { result } = await get(`v1/student/detail?id=${id}`)
    return result
  }

  /**
   * 下载模版
   * @returns
   */
  static async getClassExcelDemo() {
    const { result } = await get('v1/school_class/excel_demo')
    return result
  }

  /**
   *导入
   * @returns {*} data
   */
  static async classImportApi(data) {
    const res = await post('v1/school_class/importApi', data)
    return res
  }

  /**
   * 获取问卷详情
   * @param {*} id
   * @returns
   */
  static async getExamRecordDetail(id) {
    const { result } = await get(`v1/exam_record/detail/${id}`)
    return result
  }
}
