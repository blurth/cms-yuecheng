/* eslint-disable class-methods-use-this */
import _axios, {get, put, _delete, post} from '@/lin/plugin/axios'

// 我们通过 class 这样的语法糖使模型这个概念更加具象化，其优点：耦合性低、可维护性。
class Activity {


    async deleteActivity(id) {
        const res = await _delete(`v1/activity/${id}`)
        return res
    }

    async getActivities() {
        const {result} = await get(`v1/activity/all`)
        return result
    }

    async saveActivity(data) {
        const result = await post('v1/activity/save', data)

        return result
    }

    async getActivity(id) {
        const {result} = await get(`v1/activity/detail/${id}`)
        return result
    }


    //getRegisteredUsers

    async getRegisteredUsers(id) {
        const {result} = await get(`v1/activity/registeredUsers/${id}`)
        return result
    }




}

export default new Activity()
