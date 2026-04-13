import {_delete , get , post} from "lin/plugin/axios";

export class News {
    static async getNews() {
        const {result} = await get(`v1/news/all`)
        return result
    }

    static async getNewsDetail(id) {
        const {result} = await get(`v1/news/detail/${id}`)
        return result
    }


    //添加新闻动态
    static async saveNews(data) {
        const res = await post('v1/news/save', data)
        return res
    }

    //删除新闻动态

    static async deleteNews(id) {
        const res = await _delete(`v1/news/${id}`)
        return res
    }

}
