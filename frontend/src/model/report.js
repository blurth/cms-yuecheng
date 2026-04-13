import {get, post} from '@/lin/plugin/axios'

export default class Report {
    /**
     * 获取举报列表
     * @param {*} data
     * @returns
     */
    static async getReportList(data) {
        const { result } = await get('v1/report/list', {
        ...data,
        })
        return result
    }

    /**
     * 新增报告
     */

    static async addReport(data) {
        return post('v1/report/save', {
            ...data,
        });
    }
}
