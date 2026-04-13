/* eslint-disable class-methods-use-this */
import { get, put } from '@/lin/plugin/axios'

class CrisisEvent {
  async getList(params) {
    const res = await get('cms/crisis-event', params)
    return res
  }

  async getDetail(id) {
    const res = await get(`cms/crisis-event/${id}`)
    return res
  }

  async updateStatus(id, data) {
    const res = await put(`cms/crisis-event/${id}/status`, data)
    return res
  }
}

export default new CrisisEvent()
