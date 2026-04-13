/* eslint-disable class-methods-use-this */
import {get, post ,_delete} from '@/lin/plugin/axios'

export default class Teacher {

    static async getTeachers(data) {
        const { result } = await get('v1/teacher/list', {
            ...data,
        })
        return result
    }

    static async saveTeacher(data) {
        return post('v1/teacher/save', {
            ...data,
        });
    }

    //删除

    static async deleteTeacher(id) {
        return _delete (`v1/teacher/${id}`);
    }


}
