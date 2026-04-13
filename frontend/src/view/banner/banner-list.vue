<template>
  <div>
    <div class="container" v-if="!showEdit">
      <div class="header">
        <div class="title">轮播图列表</div>
        <lin-search @query="onQueryChange" ref="searchKeywordDom" />
      </div>
      <!-- 表格 -->
      <el-table stripe :data="banners" v-loading="loading">
        <el-table-column prop="id" label="id" width="100"></el-table-column>
        <el-table-column prop="title" label="标题"></el-table-column>
        <el-table-column prop="name" label="名称"></el-table-column>
        <el-table-column prop="description" label="描述"></el-table-column>
        <!-- <el-table-column prop="img" label="图片">
          <template #default="scope">
            <img style="width: 50px; height: 50px" :src="scope.row.img" alt="" />
          </template>
        </el-table-column> -->
        <el-table-column prop="create_time" label="创建时间"></el-table-column>

        <el-table-column label="操作" fixed="right" width="350">
          <template #default="scope">
            <el-button plain size="small" type="primary" @click="handleLook(scope.row)">编辑</el-button>
            <el-button plain size="small" type="primary" @click="handleJump(scope.row.id)">轮播图元素列表</el-button>
            <el-button
              plain
              size="small"
              type="danger"
              @click="handleDelete(scope.row.id)"
              v-permission="{ permission: '删除图书', type: 'disabled' }"
              >删除</el-button
            >
          </template>
        </el-table-column>
      </el-table>
    </div>
    <!-- 编辑页面 -->

    <banner-modify
      v-if="showEdit"
      @addBanner="addBanner"
      @editClose="editClose"
      :editBannerId="editBannerId"
    ></banner-modify>

    <!-- 分页 -->
    <div class="pagination">
      <el-pagination
        v-if="!showEdit"
        :total="totalNum"
        :background="true"
        :page-size="pageCount"
        :current-page="currentPage"
        layout="total,prev, pager, next, jumper"
        @current-change="handleCurrentChange"
      >
      </el-pagination>
    </div>
  </div>
</template>
<script>
import { ref } from 'vue'
import bannerModel from '@/model/banner'
import LinSearch from '@/component/base/search/lin-search'
import { ElMessageBox, ElMessage } from 'element-plus'
import { useRouter } from 'vue-router'
import bannerModify from './banner'

export default {
  components: {
    LinSearch,
    bannerModify,
  },
  setup() {
    const router = useRouter()
    const loading = ref(false)
    const banners = ref([])
    const showEdit = ref(false)
    const title = ref('')
    const editBannerId = ref('')
    const totalNum = ref(0)
    const pageCount = ref(10)
    const getBanner = async () => {
      try {
        loading.value = true
        bannerModel
          .getBanner({
            title: title.value,
          })
          .then(res => {
            totalNum.value = res.result.total
            banners.value = res.result.data
          })
        loading.value = false
      } catch (error) {
        loading.value = false
        if (error.code === 10020) {
          banners.value = []
        }
      }
    }

    const onQueryChange = query => {
      title.value = query
      getBanner()
    }

    const handleLook = rows => {
      showEdit.value = true
      editBannerId.value = rows.id
    }
    const editClose = () => {
      showEdit.value = false
      getBanner()
    }

    const handleJump = id => {
      router.push(`/banner/item/list?id=${id}`)
    }

    const handleDelete = id => {
      ElMessageBox.confirm('此操作将永久删除该轮播图, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
      }).then(async () => {
        const res = await bannerModel.deleteBanner({
          ids: id,
        })
        if (res.code < window.MAX_SUCCESS_CODE) {
          getBanner()
          ElMessage.success(`${res.message}`)
        }
      })
    }

    const addBanner = () => {
      showEdit.value = false
    }
    getBanner()

    return {
      loading,
      banners,
      showEdit,
      editBannerId,
      totalNum,
      pageCount,
      handleLook,
      editClose,
      onQueryChange,
      getBanner,
      addBanner,
      handleDelete,
      handleJump,
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
