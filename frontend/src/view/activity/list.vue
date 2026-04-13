<template>
  <div>
    <div class="container" v-if="!showEdit">
      <div class="header">
        <div class="title">活动列表</div>
      </div>
      <el-table :data="activities.data" v-loading="loading">
        <el-table-column type="index" :index="indexMethod" label="序号" width="100"></el-table-column>
        <el-table-column prop="theme" label="活动主题"></el-table-column>
        <el-table-column label="活动图片">
          <template #default="scope">
            <el-image :src="scope.row.image_url" fit="cover" :preview-src-list="[scope.row.image_url]" style="width: 100px; height: 60px;"></el-image>
          </template>
        </el-table-column>
<!--        <el-table-column prop="description" label="活动介绍"></el-table-column>-->
        <el-table-column prop="address" label="活动地址"></el-table-column>
        <el-table-column label="已报名人数">
          <template #default="scope">
            <el-button type="text" @click="openUserList(scope.row.id)">{{ scope.row.registration_count }}</el-button>
          </template>
        </el-table-column>
        <el-table-column prop="registration_limit" label="活动报名人数限制"></el-table-column>
        <el-table-column prop="registration_start_time" label="活动报名开始时间"></el-table-column>
        <el-table-column prop="registration_end_time" label="活动报名结束时间"></el-table-column>
        <el-table-column label="操作" fixed="right" width="275">
          <template #default="scope">
            <el-button plain size="small" type="primary" @click="handleEdit(scope.row.id)">编辑</el-button>
            <el-button plain size="small" type="danger" @click="handleDelete(scope.row.id)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <div class="pagination">
        <el-pagination
            :total="totalNum"
            :background="true"
            :page-size="pageCount"
            :current-page="listQuery.page"
            layout="total,prev, pager, next, jumper"
            @current-change="handleCurrentChange"
        >
        </el-pagination>
      </div>


      <el-dialog v-model="dialogVisible" title="用户信息" width="50%" class="dialog-body">

<!--  导出按钮      -->
        <el-button type="primary" @click="exportExcel">导出</el-button>

        <el-table :data="userList">
          <el-table-column type="user_form.id" label="序号" width="100"></el-table-column>
          <el-table-column prop="user_form.name" label="姓名"></el-table-column>
          <el-table-column prop="user_form.phone" label="电话"></el-table-column>
          <el-table-column prop="user_form.remark" label="备注"></el-table-column>
        </el-table>

      </el-dialog>
    </div>



    <activityModify v-if="showEdit" @editClose="editClose" :editActivityId="editId"></activityModify>
  </div>



</template>

<script setup>
import { ref } from 'vue'
import {ElMessage, ElMessageBox, ElNotification} from 'element-plus'
import Activity from "@/model/activity";
import activityModify from "./add.vue";
import {Loading} from "@element-plus/icons-vue";
import axios from "axios";
import {getToken} from "lin/util/token";
const activities = ref([]);

const loading = ref(false)
const showEdit = ref(false)
const editId = ref('')
const currentActivityId = ref('')
const notifyT = ref(null);
const dialogVisible = ref(false);
const userList = ref([]);

// 分页相关
const totalNum = ref(0)
const pageCount = ref(10)
const listQuery = ref({ page: 1, count: 10 })

const handleCurrentChange = (page) => {
  listQuery.value.page = page
  getActivities(page)
}

const getActivities = async (page = 1) => {
  try {
    loading.value = true
    activities.value = await Activity.getActivities(page)
    loading.value = false
    totalNum.value = activities.value.total || 0
  } catch (error) {
    loading.value = false
    if (error.code === 10020) {
      activities.value = { data: [], total: 0, current_page: 1, per_page: 10 }
      totalNum.value = 0
    }
  }
}

const openUserList = async (activityId) => {

  userList.value = await Activity.getRegisteredUsers(activityId);
  console.log('userList:', userList.value); // Check the value of userList
  dialogVisible.value = true;
  currentActivityId.value = activityId;

}
const handleEdit = id => {
  // Handle edit action

  showEdit.value = true
  editId.value = id
}

const handleDelete = id => {
  ElMessageBox.confirm('此操作将永久删除该活动, 是否继续?', '提示', {
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    type: 'warning',
  }).then(async () => {
    const res = await Activity.deleteActivity(id)
    if (res.code < window.MAX_SUCCESS_CODE) {
      getActivities()
      ElMessage.success(`${res.message}`)
    }
  })
}

//exportExcel
const exportExcel = async () => {
  try {
    const activityId = currentActivityId.value;

    notifyT.value = ElNotification({
      title: "导出文件准备中",
      icon: Loading,
      customClass: "upload-loading-box is-loading",
      duration: 1000,
      offset: 100,
      message: "准备工作正在后台进行，你可以选择进行其他操作。",
    });


    // 使用fetch来获取Excel文件的URL
    //const response = await studentModel.getExamRecordExcel(listQueryCopy);
    // 检查是否需要手动解析Blob

    const axios = require("axios");

    // 设置axios实例
    const api = axios.create({
      baseURL: `https://yuecheng.jixiangjiaoyu.com/v1/activity/`, // 替换为您的API基础URL
      responseType: "blob", // 指定响应类型为Blob
      headers: {
        Authorization: getToken("access_token"), // 添加Authorization标头
      },
    });
    //文件名 用学校名称即可

    const fileName = `活动预约表.xlsx`;
    // 调用接口
        api
        .get(`exportRegisteredUsers/${activityId}`)
        .then((response) => {
          // 获取Blob数据
          const blob = response.data;

          // 创建Blob URL
          const blobUrl = URL.createObjectURL(blob);

          // 创建一个链接并模拟点击下载
          const link = document.createElement("a");
          link.href = blobUrl;
          link.setAttribute("download", fileName);
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);

          ElNotification({
            title: "导出成功",
            type: "success",
            message: "活动报名记录已成功下载。",
            duration: 5000,
          });
        })

        .catch((error) => {});

    //关闭notifyT.value
  } catch (error) {
    console.error(error);
  }
};
const editClose = () => {
  showEdit.value = false
  getActivities()
}
const indexMethod = index => index + 1

getActivities()


</script>
<style lang="scss" scoped>
.container {
  padding: 0 30px;
  min-height: 100vh;
  display: flex;
  flex-direction: column;

  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;

    .title {
      height: 59px;
      line-height: 59px;
      color: $parent-title-color;
      font-size: 16px;
      font-weight: 500;
    }
  }

  .el-table {
    flex: 1;
    margin-bottom: 20px;
  }

  .pagination {
    display: flex;
    justify-content: flex-end;
    margin: 20px 0;
    padding: 10px 0;
  }

  .ssq-box {
    .el-select {
      width: 140px;
      margin-right: 10px;
    }
  }
}
</style>
