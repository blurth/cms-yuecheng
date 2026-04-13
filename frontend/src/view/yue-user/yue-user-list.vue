<template>
  <div class="container">
    <div class="header">
      <div class="title">用户列表</div>
    </div>

    <el-form :inline="true" :model="searchForm" class="search-form">
      <el-form-item label="姓名">
        <el-input v-model="searchForm.name" placeholder="请输入姓名" clearable></el-input>
      </el-form-item>
      <el-form-item label="状态">
        <el-select v-model="searchForm.audit" placeholder="请选择状态" clearable>
          <el-option label="正常" :value="1"></el-option>
          <el-option label="禁用" :value="2"></el-option>
        </el-select>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="handleSearch">查询</el-button>
        <el-button @click="handleReset">重置</el-button>
      </el-form-item>
    </el-form>

    <el-table :data="users" v-loading="loading" stripe>
      <el-table-column prop="id" label="ID" width="80"></el-table-column>
      <el-table-column prop="nickname" label="昵称" width="150"></el-table-column>
      <el-table-column prop="name" label="姓名" width="150"></el-table-column>
      <el-table-column prop="phone" label="手机号" width="150"></el-table-column>
      <el-table-column prop="audit" label="状态" width="100">
        <template #default="scope">
          <el-switch
            v-model="scope.row.audit"
            :active-value="2"
            :inactive-value="1"
            @change="handleToggleStatus(scope.row)"
          />
        </template>
      </el-table-column>
      <el-table-column prop="last_login_time" label="最后登录时间" width="200"></el-table-column>
      <el-table-column label="操作" fixed="right" width="200">
        <template #default="scope">
          <el-button plain size="small" type="primary" @click="handleEdit(scope.row)">编辑</el-button>
          <el-button plain size="small" type="danger" @click="handleDelete(scope.row.id)">删除</el-button>
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

    <el-dialog v-model="dialogVisible" title="编辑用户" width="500px">
      <el-form :model="editForm" label-width="100px">
        <el-form-item label="昵称">
          <el-input v-model="editForm.nickname"></el-input>
        </el-form-item>
        <el-form-item label="姓名">
          <el-input v-model="editForm.name"></el-input>
        </el-form-item>
        <el-form-item label="手机号">
          <el-input v-model="editForm.phone"></el-input>
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
import { ElMessageBox, ElMessage } from 'element-plus'
import yueUserModel from '@/model/yue-user'

export default {
  setup() {
    const users = ref([])
    const loading = ref(false)
    const totalNum = ref(0)
    const pageCount = ref(10)
    const searchForm = ref({
      name: '',
      audit: '',
    })
    const listQuery = ref({
      page: 1,
      count: 10,
      name: '',
      audit: '',
    })
    const dialogVisible = ref(false)
    const editForm = ref({
      id: null,
      nickname: '',
      name: '',
      phone: '',
    })

    onMounted(() => {
      getUsers()
    })

    const getUsers = async () => {
      try {
        loading.value = true
        const res = await yueUserModel.getList(listQuery.value)
        users.value = res.items
        totalNum.value = res.total
        loading.value = false
      } catch (error) {
        loading.value = false
        ElMessage.error('获取用户列表失败')
      }
    }

    const handleEdit = row => {
      editForm.value = {
        id: row.id,
        nickname: row.nickname,
        name: row.name,
        phone: row.phone,
      }
      dialogVisible.value = true
    }

    const handleUpdate = async () => {
      try {
        const res = await yueUserModel.update(editForm.value.id, {
          nickname: editForm.value.nickname,
          name: editForm.value.name,
          phone: editForm.value.phone,
        })
        if (res.code < window.MAX_SUCCESS_CODE) {
          ElMessage.success('更新成功')
          dialogVisible.value = false
          getUsers()
        }
      } catch (error) {
        ElMessage.error('更新失败')
      }
    }

    const handleToggleStatus = row => {
      const action = row.audit === 1 ? '禁用' : '启用'
      ElMessageBox.confirm(`确定要${action}该用户吗?`, '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
      }).then(async () => {
        const res = await yueUserModel.toggleStatus(row.id)
        if (res.code < window.MAX_SUCCESS_CODE) {
          ElMessage.success(`${action}成功`)
          getUsers()
        }
      })
    }

    const handleDelete = id => {
      ElMessageBox.confirm('此操作将删除该用户, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
      }).then(async () => {
        const res = await yueUserModel.delete(id)
        if (res.code < window.MAX_SUCCESS_CODE) {
          ElMessage.success('删除成功')
          getUsers()
        }
      })
    }

    const handleCurrentChange = val => {
      listQuery.value.page = val
      getUsers()
    }

    const handleSearch = () => {
      listQuery.value.page = 1
      listQuery.value.name = searchForm.value.name
      listQuery.value.audit = searchForm.value.audit
      getUsers()
    }

    const handleReset = () => {
      searchForm.value.name = ''
      searchForm.value.audit = ''
      listQuery.value.page = 1
      listQuery.value.name = ''
      listQuery.value.audit = ''
      getUsers()
    }

    return {
      users,
      loading,
      totalNum,
      pageCount,
      listQuery,
      searchForm,
      dialogVisible,
      editForm,
      handleEdit,
      handleUpdate,
      handleToggleStatus,
      handleDelete,
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
