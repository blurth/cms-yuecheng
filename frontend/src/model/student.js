import {get, post} from '@/lin/plugin/axios'

export default class Student {
  /**
   * 获取学生列表
   * @param {*} data
   * @returns
   */
  static async getStudentList(data) {
    const { result } = await get('v1/student/list', {
      ...data,
    })
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

  static async getStudentSave(data) {
    const res = await post('v1/student/save', data)
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
  static async getStudentExcelDemo() {
    const { result } = await get('v1/student/excel_demo')
    return result
  }

  /**
   *导入
   * @returns {*} data
   */
  static async studentimportApi(data) {
    const res = await post('v1/student/importApi', data)
    return res
  }

  /**
   *导入
   * @returns {*} data
   */
  static async sendRecordTw(data) {
    const res = await post('v1/student/sendRecordTw', data)
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


  /**
   * 导出问卷excel
   * @param {*} id
   * @returns
   */
  static async getExamRecordExcel(data) {
    return get(`v1/exam_record/export_exam`, data);
  }



}
