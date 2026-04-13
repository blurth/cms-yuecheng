

<template>
  <div>
    <!-- 列表页面 -->
    <div class="container" v-if="!showEdit">
      <div class="header">
        <div class="title">心理老师列表</div>
      </div>
      <!-- 表格 -->
      <el-table :data="teachers.data" v-loading="loading">
        <el-table-column type="index" :index="indexMethod" label="序号" width="100"></el-table-column>
        <el-table-column label="头像">
          <template #default="scope">
            <img :src="scope.row.profile_picture" alt="头像" style="width: 50px; height: 50px; border-radius: 50%;" />
          </template>
        </el-table-column>
        <el-table-column prop="name" label="姓名"></el-table-column>
        <el-table-column prop="phone" label="手机号"></el-table-column>
        <el-table-column prop="gender" label="性别">
          <template #default="scope">
            {{ scope.row.gender === 1 ? '男' : '女' }}
          </template>
        </el-table-column>

        <!--        <el-table-column prop="update_time" label="最近修改时间"></el-table-column>-->


        <el-table-column label="操作" fixed="right" width="275">
          <template #default="scope">
            <el-button plain size="small" type="primary" v-permission="{ permission: '删除心理老师', type: 'disabled' }" @click="handleEdit(scope.row.id)">编辑</el-button>
            <el-button
                plain
                size="small"
                type="danger"
                @click="handleDelete(scope.row.id)"
                v-permission="{ permission: '删除心理老师', type: 'disabled' }"
            >删除</el-button
            >
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

    </div>
    <!-- 编辑心理老师资料     -->

    <psychologyModify v-if="showEdit" @editClose="editClose" :editPsychologistId="editId"></psychologyModify>

  </div>
</template>


<script>
import { onMounted, ref } from 'vue'
import { ElMessageBox, ElMessage } from 'element-plus'
import psychologyModify from "./save"
import PsychologyTeacher from "@/model/psychology-teacher";


export default {
  components: {
    psychologyModify
  },
  setup() {
    const teachers = ref({ data: [], total: 0, current_page: 1, per_page: 10 });
    const editId = ref()
    const loading = ref(false)
    const showEdit = ref(false)

    //分页
    const totalNum = ref(0)
    const pageCount = ref(10)
    const listQuery = ref({ page: 1, count: 10 })

    const handleCurrentChange = val => {
      listQuery.value.page = val
      getTeachers(val)
    }
    const form = ref({
      name: '',
      phone: '',
      gender: '',
      profile_picture: '',
      bio: '',
      experience_years: '',
      specialization: '',
      level: '',
    });

    const submitForm = formName => {
      // Submit form logic here
    };

    const resetForm = formName => {
      // Reset form logic here
    };


    onMounted(() => {
      getTeachers()
    })

    const getTeachers = async (page = 1) => {
      try {
        loading.value = true
        teachers.value = await PsychologyTeacher.getPsychologyTeachers({page:page})
        loading.value = false
        totalNum.value = teachers.value.total

        // Calculate the total number of pages
        const totalPages = Math.ceil(totalNum.value / pageCount.value)

        // If the current page is greater than the total number of pages,
        // set the current page to the last page
        if (listQuery.value.page > totalPages) {
          listQuery.value.page = totalPages
        }
      } catch (error) {
        loading.value = false
        if (error.code === 10020) {
          teachers.value = []
        }
      }
    }

    const handleEdit = id => {
      showEdit.value = true
      editId.value = id
    }

    const handleDelete = id => {
      ElMessageBox.confirm('此操作将永久删除该心理老师, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
      }).then(async () => {
        const res = await PsychologyTeacher.deletePsychologist(id)
        if (res.code == 0) {
          getTeachers()
          ElMessage.success(`${res.message}`)
        }else {
          ElMessage.error(`${res.message}`)
        }
      })
    }

    const editClose = () => {
      showEdit.value = false
      getTeachers()
    }

    const indexMethod = index => index + 1

    return {
      teachers,
      loading,
      showEdit,
      editClose,
      handleEdit,
      form,
      editId,
      totalNum,
      pageCount,
      listQuery,
      submitForm,
      resetForm,
      indexMethod,
      handleCurrentChange,
      handleDelete,
    }
  },
}

</script>


<style scoped lang="scss">
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
}
</style>
