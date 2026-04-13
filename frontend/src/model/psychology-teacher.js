/* eslint-disable class-methods-use-this */
import { get,post, put, _delete } from '@/lin/plugin/axios'
export default class PsychologyTeacher {

    static async getPsychologyTeachers(data) {
        const { result } = await get('v1/psychology/list',data)
        return result
    }

    static async getPsychologistDetail(id) {
        const { result } = await get(`v1/psychology/detail/${id}`)
        return result
    }


    static async savePsychologist(data) {
        const {result} = await post('v1/psychology/save', data)
        return result
    }

    //删除心理老师

    static async deletePsychologist(id) {
        const res = await _delete(`v1/psychology/${id}`)
        return res
    }

}

