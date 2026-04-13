/* eslint-disable class-methods-use-this */
import { get, put, _delete } from '@/lin/plugin/axios'

class YueUser {
  async getList(params) {
    const res = await get('cms/yue-user', params)
    return res
  }

  async getDetail(id) {
    const res = await get(`cms/yue-user/${id}`)
    return res
  }

  async toggleStatus(id) {
    const res = await put(`cms/yue-user/${id}/status`)
    return res
  }

  async update(id, data) {
    const res = await put(`cms/yue-user/${id}`, data)
    return res
  }

  async delete(id) {
    const res = await _delete(`cms/yue-user/${id}`)
    return res
  }
}

export default new YueUser()
