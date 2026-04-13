<template>
  <div class="container">
    <div class="header">
      <div class="title">风险列表</div>
    </div>

    <el-form :inline="true" :model="searchForm" class="search-form">
      <el-form-item label="风险等级">
        <el-select v-model="searchForm.risk_level" placeholder="请选择风险等级" clearable>
          <el-option label="低风险" :value="1"></el-option>
          <el-option label="中风险" :value="2"></el-option>
          <el-option label="高风险" :value="3"></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="状态">
        <el-select v-model="searchForm.status" placeholder="请选择状态" clearable>
          <el-option label="待处理" :value="0"></el-option>
          <el-option label="处理中" :value="1"></el-option>
          <el-option label="已处理" :value="2"></el-option>
        </el-select>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="handleSearch">查询</el-button>
        <el-button @click="handleReset">重置</el-button>
      </el-form-item>
    </el-form>

    <el-table :data="events" v-loading="loading" stripe>
      <el-table-column prop="id" label="ID" width="80"></el-table-column>
      <el-table-column prop="user_name" label="用户姓名" width="120"></el-table-column>
      <el-table-column prop="phone" label="手机号" width="130"></el-table-column>
      <el-table-column prop="risk_content" label="触发内容" width="200" show-overflow-tooltip></el-table-column>
      <el-table-column prop="source_type" label="来源类型" width="120">
        <template #default="scope">
          <span v-if="scope.row.source_type === 1">心情打卡</span>
          <span v-else-if="scope.row.source_type === 2">AI对话</span>
          <span v-else>其他</span>
        </template>
      </el-table-column>
      <el-table-column prop="risk_level" label="风险等级" width="100">
        <template #default="scope">
          <el-tag v-if="scope.row.risk_level === 1" type="success">低风险</el-tag>
          <el-tag v-else-if="scope.row.risk_level === 2" type="warning">中风险</el-tag>
          <el-tag v-else-if="scope.row.risk_level === 3" type="danger">高风险</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="status" label="状态" width="100">
        <template #default="scope">
          <el-tag v-if="scope.row.status === 0" type="info">待处理</el-tag>
          <el-tag v-else-if="scope.row.status === 1" type="warning">处理中</el-tag>
          <el-tag v-else-if="scope.row.status === 2" type="success">已处理</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="created_at" label="创建时间" width="180"></el-table-column>
      <el-table-column label="操作" fixed="right" width="150">
        <template #default="scope">
          <el-button plain size="small" type="primary" @click="handleProcess(scope.row)">处理</el-button>
        </template>
      </el-table-column>
    </el-table>

    <div class="pagination">
      <el-pagination
        :total="totalNum"
        :background="true"
        :page-size="pageCount"
        :current-page="listQuery.page"
        layout="total, prev, pager, next, jumper"
        @current-change="handleCurrentChange"
      ></el-pagination>
    </div>

    <el-dialog v-model="dialogVisible" title="处理预警" width="500px">
      <el-form :model="processForm" label-width="100px">
        <el-form-item label="状态">
          <el-select v-model="processForm.status">
            <el-option label="待处理" :value="0"></el-option>
            <el-option label="处理中" :value="1"></el-option>
            <el-option label="已处理" :value="2"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="处理备注">
          <el-input v-model="processForm.handle_note" type="textarea" :rows="4"></el-input>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="handleUpdate">确定</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import { onMounted, ref } from 'vue'
import { ElMessage } from 'element-plus'
import crisisEventModel from '@/model/crisis-event'

export default {
  setup() {
    const events = ref([])
    const loading = ref(false)
    const totalNum = ref(0)
    const pageCount = ref(10)
    const searchForm = ref({
      risk_level: '',
      status: '',
    })
    const listQuery = ref({
      page: 1,
      count: 10,
      risk_level: '',
      status: '',
    })
    const dialogVisible = ref(false)
    const processForm = ref({
      id: null,
      status: 0,
      handle_note: '',
    })

    onMounted(() => {
      getEvents()
    })

    const getEvents = async () => {
      try {
        loading.value = true
        const res = await crisisEventModel.getList(listQuery.value)
        events.value = res.items
        totalNum.value = res.total
        loading.value = false
      } catch (error) {
        loading.value = false
        ElMessage.error('获取列表失败')
      }
    }

    const handleProcess = row => {
      processForm.value = {
        id: row.id,
        status: row.status,
        handle_note: '',
      }
      dialogVisible.value = true
    }

    const handleUpdate = async () => {
      try {
        const res = await crisisEventModel.updateStatus(processForm.value.id, {
          status: processForm.value.status,
          handle_note: processForm.value.handle_note,
        })
        if (res.code < window.MAX_SUCCESS_CODE) {
          ElMessage.success('处理成功')
          dialogVisible.value = false
          getEvents()
        }
      } catch (error) {
        ElMessage.error('处理失败')
      }
    }

    const handleCurrentChange = val => {
      listQuery.value.page = val
      getEvents()
    }

    const handleSearch = () => {
      listQuery.value.page = 1
      listQuery.value.risk_level = searchForm.value.risk_level
      listQuery.value.status = searchForm.value.status
      getEvents()
    }

    const handleReset = () => {
      searchForm.value.risk_level = ''
      searchForm.value.status = ''
      listQuery.value.page = 1
      listQuery.value.risk_level = ''
      listQuery.value.status = ''
      getEvents()
    }

    return {
      events,
      loading,
      totalNum,
      pageCount,
      listQuery,
      searchForm,
      dialogVisible,
      processForm,
      handleProcess,
      handleUpdate,
      handleCurrentChange,
      handleSearch,
      handleReset,
    }
  },
}
</script>

<style lang="scss" scoped>
.container {
  padding: 0 30px;

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

  .pagination {
    display: flex;
    justify-content: flex-end;
    margin: 20px;
  }
}
</style>
