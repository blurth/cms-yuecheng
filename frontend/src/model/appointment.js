/* eslint-disable class-methods-use-this */
import {get, post, _delete} from "@/lin/plugin/axios";

class Appointment {
  async getList(data) {
    let { result } = await get(`v1/appointment/list`, data);
    return result;
  }

  /**
   * 预约详情
   * @param {*} id
   * @returns
   */
  async getListDetail(id) {
    let { result } = await get(`v1/appointment/detail/${id}`);
    return result;
  }

    /**
     * 添加排期
     * @param {*} data
     * @returns
     */

    async addAppointment(data) {
      let result = await post(`v1/appointment/save`, data);
      return result;
    }


    /*cancelAppointment*/

    async cancelAppointment(id) {
      let result = await post(`v1/appointment/cancel/${id}`);
      return result;
    }

    /**
     * 删除预约
     * @param {number} id 预约ID
     * @returns {Promise}
     */
    async delete(id) {
      const result = await _delete(`v1/appointment/${id}`);
      return result;
    }
}

export default new Appointment();
