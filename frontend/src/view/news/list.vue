

<template>
  <div>
    <!-- 列表页面 -->
    <div class="container" v-if="!showEdit">
      <div class="header">
        <div class="title">新闻动态列表</div>
      </div>
      <!-- 表格 -->
      <el-table :data="News.data" v-loading="loading">
        <el-table-column type="index" :index="indexMethod" label="序号" width="100"></el-table-column>

        <el-table-column label="封面">
          <template #default="scope">
            <img :src="scope.row.cover" alt="封面" style="width: 50px; height: 50px; border-radius: 50%;" />
          </template>
        </el-table-column>

        <el-table-column prop="title" label="新闻标题"></el-table-column>

        <el-table-column prop="publisher" label="发布人"></el-table-column>
        <el-table-column prop="publish_time" label="发布时间"></el-table-column>
        <el-table-column prop="created_at" label="创建时间"></el-table-column>
        <el-table-column prop="updated_at" label="更新时间"></el-table-column>

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
            :total="News.total"
            :background="true"
            :page-size="10"
            :current-page="News.current_page"
            layout="total,prev, pager, next, jumper"
            @current-change="handleCurrentChange"
        >
        </el-pagination>
      </div>

    </div>

    <NewsModify v-if="showEdit" @editClose="editClose" :editNewsId="editNewsId"></NewsModify>

  </div>
</template>

<script>
import { onMounted, ref } from 'vue'
import { ElMessageBox, ElMessage } from 'element-plus'

import {News as NewsModel} from '@/model/news'
import NewsModify from "@/view/news/add.vue";

export default {
  components: {
    NewsModify
  },
  setup() {
    const News = ref({ data: [], total: 0, current_page: 1, per_page: 10 });
    const editNewsId = ref('')
    const loading = ref(false)
    const showEdit = ref(false)

    onMounted(() => {
      getNews()
    })

    const getNews = async (page = 1) => {
      console.log('getNews', page)
      try {
        loading.value = true
        News.value = await NewsModel.getNews(page)
        console.log(News.value)
        loading.value = false
      } catch (error) {
        loading.value = false
        if (error.code === 10020) {
          News.value = { data: [], total: 0, current_page: 1, per_page: 10 }
        }
      }
    }

    const handleCurrentChange = (page) => {
      getNews(page)
    }

    const handleEdit = id => {
      showEdit.value = true
      editNewsId.value = id
    }

    const handleDelete = id => {
      ElMessageBox.confirm('此操作将永久删除该动态, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
      }).then(async () => {
        const res = await NewsModel.deleteNews(id)
        if (res.code < window.MAX_SUCCESS_CODE) {
          getNews()
          ElMessage.success(`${res.message}`)
        }
      })
    }

    const editClose = () => {
      showEdit.value = false
      getNews()
    }

    const indexMethod = index => index + 1

    return {
      News,
      loading,
      showEdit,
      editClose,
      handleEdit,
      editNewsId,
      indexMethod,
      handleDelete,
      handleCurrentChange,
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
