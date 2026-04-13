<template>
  <div class="container">
    <div v-if="!showEdit">
      <div class="header">
        <div class="title">轮播图元素列表</div>
        <el-button @click="handleAdd">添加</el-button>
      </div>

      <!-- 表格 -->
      <el-table :data="itemsBanner" stripe v-loading="loading">
        <el-table-column type="index" :index="indexMethod" label="序号" width="100"></el-table-column>
        <el-table-column prop="name" label="名称"></el-table-column>
        <el-table-column prop="img" label="图片">
          <template #default="scope">
            <img style="width: 50px; height: 50px" :src="scope.row.img" alt="" />
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="创建时间"></el-table-column>

        <el-table-column label="操作" fixed="right" width="275">
          <template #default="scope">
            <el-button plain size="small" type="primary" @click="handleEdit(scope.row.id)">编辑</el-button>
            <el-button
              plain
              size="small"
              type="danger"
              @click="handleDelete(scope.row.id, scope.$index)"
              v-permission="{ permission: '删除图书', type: 'disabled' }"
              >删除</el-button
            >
          </template>
        </el-table-column>
      </el-table>
    </div>
    <bannerItemModify
      v-else
      @editClose="editClose"
      :bannerId="bannerId"
      :editBannerItemId="editBannerItemId"
    ></bannerItemModify>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import bannerModel from '@/model/banner'
import { ElMessageBox, ElMessage } from 'element-plus'
import bannerItemModify from './banner-item'

export default {
  components: {
    bannerItemModify,
  },
  setup() {
    const loading = ref(false)
    const itemsBanner = ref([])
    const route = useRoute()
    const showEdit = ref(false)
    const editBannerItemId = ref('')
    const bannerId = ref('')

    const getBannerItem = async () => {
      loading.value = true
      bannerId.value = route.query.id
      const list = await bannerModel.getBannerItem(route.query.id)
      itemsBanner.value = list
      loading.value = false
    }
    getBannerItem()

    const editClose = () => {
      showEdit.value = false
      getBannerItem()
    }

    const handleAdd = () => {
      showEdit.value = true
    }

    const handleEdit = id => {
      editBannerItemId.value = id
      showEdit.value = true
    }

    const handleDelete = (id, index) => {
      ElMessageBox.confirm('此操作将永久删除该轮播图元素, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
      }).then(async () => {
        console.log(index)
        const res = await bannerModel.deleteBannerItem({
          ids: id,
        })
        if (res.code < window.MAX_SUCCESS_CODE) {
          itemsBanner.value.splice(index, 1)
          ElMessage.success(`${res.message}`)
        }
      })
    }

    return {
      loading,
      itemsBanner,
      showEdit,
      bannerId,
      editBannerItemId,
      getBannerItem,
      handleDelete,
      editClose,
      handleEdit,
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
