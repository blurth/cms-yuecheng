import {get} from "lin/plugin/axios";

export default class Mht {
    static async getQuestionnaireList(data) {
        const { result } = await get('v1/mht/list', {
            ...data,
        })
        return result
    }

    static async getunfinishedList(data) {
        const { result } = await get('v1/mht/unfinished_list', {
            ...data,
        })
        return result
    }
}
