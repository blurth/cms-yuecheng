<template>
  <div>
    <!-- 列表页面 -->
    <div class="container" v-if="!showEdit">
      <div class="header">
        <div class="title">讲师列表</div>
        <div class="d-flex aligin-items-center">
          <el-button @click="handleAdd">新增</el-button>
        </div>
      </div>
      <!-- 表格 -->
      <el-table :data="lessonAuthorList" v-loading="loading" stripe>
        <el-table-column prop="id" label="ID" width="300"></el-table-column>
        <el-table-column prop="name" label="讲师" width="300"></el-table-column>
        <el-table-column prop="avatar_url" label="头像">
          <template #default="scope">
            <img style="width: 50px; height: 50px" :src="scope.row.avatar_url" alt="" />
          </template>
        </el-table-column>
        <el-table-column label="操作" fixed="right" width="200">
          <template #default="scope">
            <el-button plain size="small" type="primary" @click="handleEdit(scope.row.id)">编辑</el-button>
            <el-button
              plain
              size="small"
              type="danger"
              @click="handleDelete(scope.row.id)"
              v-permission="{ permission: '删除讲师', type: 'disabled' }"
              >删除</el-button
            >
          </template>
        </el-table-column>
      </el-table>
    </div>
    <!-- 编辑页面 -->
    <author-modify v-else @editClose="editClose" :editAuthorId="editAuthorId"></author-modify>

    <!-- 分页 -->
    <div class="pagination">
      <el-pagination
        v-if="!showEdit"
        :total="totalNum"
        :background="true"
        :page-size="pageCount"
        :current-page="listQuery.page"
        layout="total,prev, pager, next, jumper"
        @current-change="handleCurrentChange"
      ></el-pagination>
    </div>
  </div>
</template>

<script>
import { onMounted, ref } from 'vue'
import { ElMessageBox, ElMessage } from 'element-plus'
import lessonModel from '@/model/lesson'
import { useRouter } from 'vue-router'
import authorModify from './author'

export default {
  components: {
    authorModify,
  },
  setup() {
    const router = useRouter()
    const lessons = ref([])
    const editAuthorId = ref(null)
    const loading = ref(false)
    const showEdit = ref(false)
    const totalNum = ref(0)
    const lessonAuthorList = ref([])
    const pageCount = ref(10)
    const listQuery = ref({
      title: '',
      page: 1,
      size: 10,
      cid: '',
    })
    const options = ref([])
    onMounted(async () => {
      getLessonAuthorList()
    })
    /*
     *获取所有讲师作者
     */
    const getLessonAuthorList = () => {
      loading.value = true
      lessonModel.getLessonAuthorList().then(res => {
        lessonAuthorList.value = res
        loading.value = false
        totalNum.value = res.length
      })
    }

    const handleEdit = id => {
      showEdit.value = true
      editAuthorId.value = id
    }

    const handleDelete = id => {
      ElMessageBox.confirm('此操作将永久删除该讲师, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
      }).then(async () => {
        const res = await lessonModel.deleteLessonAuthor({
          id,
        })
        getLessonAuthorList()
        if (res.code < window.MAX_SUCCESS_CODE) {
          ElMessage.success(`${res.message}`)
        }
      })
    }

    /**
     * 新增
     */
    const handleAdd = () => {
      showEdit.value = true
    }

    const editClose = () => {
      showEdit.value = false
      editAuthorId.value = null
      getLessonAuthorList()
    }

    /**
     * 翻页
     */
    const handleCurrentChange = async val => {
      listQuery.value.page = val
    }
    /**
     * 筛选
     */
    const handleChange = e => {
      listQuery.value.cid = e[e.length - 1]
    }

    const handleJump = id => {
      router.push(`/lesson/item/list?id=${id}`)
    }

    return {
      totalNum,
      lessons,
      loading,
      showEdit,
      lessonAuthorList,
      listQuery,
      editAuthorId,
      pageCount,
      options,
      handleJump,
      editClose,
      handleEdit,
      handleDelete,
      handleChange,
      handleCurrentChange,
      handleAdd,
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
