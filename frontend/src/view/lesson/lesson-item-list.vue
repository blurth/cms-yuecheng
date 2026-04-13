<template>
  <div class="container">
    <div v-if="!showEdit">
      <div class="header">
        <div class="title">章节列表</div>
        <el-button @click="handleAdd">添加</el-button>
      </div>

      <!-- 表格 -->
      <el-table :data="items" stripe v-loading="loading">
        <el-table-column type="index" :index="indexMethod" label="序号" width="100"></el-table-column>
        <el-table-column prop="name" label="名称"></el-table-column>
        <el-table-column prop="play_count" label="播放次数"></el-table-column>
        <el-table-column prop="status" label="是否启用">
          <template #default="scope">
            <el-switch
              v-model="scope.row.status"
              :active-value="1"
              :inactive-value="0"
              active-text="上架"
              inactive-text="下架"
              @change="switchChange($event, scope.row.id)"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column prop="add_time" label="创建时间"></el-table-column>
        <el-table-column label="操作" fixed="right" width="275">
          <template #default="scope">
            <el-button plain size="small" type="primary" @click="handleEdit(scope.row.id)">编辑</el-button>
            <el-button
              plain
              size="small"
              type="danger"
              @click="handleDelete(scope.row.id, scope.$index)"
              v-permission="{ permission: '删除章节', type: 'disabled' }"
              >删除</el-button
            >
          </template>
        </el-table-column>
      </el-table>
    </div>
    <lessonItemModify v-else :pid="pid" @editClose="editClose" :editlessonItemId="editlessonItemId"></lessonItemModify>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import lessonModel from '@/model/lesson'
import { ElMessageBox, ElMessage } from 'element-plus'
import lessonItemModify from './lesson-item'

export default {
  components: {
    lessonItemModify,
  },
  setup() {
    const loading = ref(false)
    const items = ref([])
    const route = useRoute()
    const showEdit = ref(false)
    const editlessonItemId = ref('')
    const pid = ref(route.query.id)
    const getLessonSections = async () => {
      loading.value = true
      const list = await lessonModel.getLessonSections(route.query.id)
      items.value = list
      loading.value = false
    }
    getLessonSections()

    const editClose = () => {
      showEdit.value = false
      editlessonItemId.value = ''
      getLessonSections()
    }

    const handleAdd = () => {
      showEdit.value = true
    }

    const handleEdit = id => {
      editlessonItemId.value = id
      showEdit.value = true
    }

    const switchChange = async (status, id) => {
      await lessonModel.saveLessonSection({ id, status })
    }

    const handleDelete = (id, index) => {
      ElMessageBox.confirm('此操作将永久删除该章节, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
      }).then(async () => {
        console.log(index)
        const res = await lessonModel.deleteLessonSections({
          ids: id,
        })
        if (res.code < window.MAX_SUCCESS_CODE) {
          items.value.splice(index, 1)
          ElMessage.success(`${res.message}`)
        }
      })
    }

    return {
      loading,
      items,
      pid,
      showEdit,
      editlessonItemId,
      getLessonSections,
      handleDelete,
      editClose,
      handleEdit,
      handleAdd,
      switchChange,
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
