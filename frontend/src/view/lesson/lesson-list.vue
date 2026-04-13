<template>
  <div>
    <!-- 列表页面 -->
    <div class="container" v-if="!showEdit">
      <div class="header">
        <div class="title">课程列表</div>
        <div class="d-flex aligin-items-center">
          <el-cascader
            style="margin-right: 20px"
            v-model="value"
            :options="options"
            @change="handleChange"
            clearable
          ></el-cascader>
          <lin-search @query="onQueryChange" ref="searchKeywordDom" />
        </div>
      </div>
      <!-- 表格 -->
      <el-table :data="lessons" v-loading="loading" stripe>
        <el-table-column prop="title" label="课程标题" width="300"></el-table-column>
        <el-table-column prop="cover" label="封面">
          <template #default="scope">
            <img style="width: 50px; height: 50px" :src="scope.row.cover" alt="" />
          </template>
        </el-table-column>
        <el-table-column prop="lesson_count" label="章节数量"></el-table-column>
        <el-table-column prop="s_name" label="作者"></el-table-column>

        <el-table-column prop="classify" label="分类"></el-table-column>
        <!-- <el-table-column prop="cover" label="封面">
          <template #default="scope">
            <img style="width: 50px; height: 50px" :src="scope.row.cover" alt="" />
          </template>
        </el-table-column> -->
        <el-table-column prop="create_time" label="添加时间" width="275"></el-table-column>
        <el-table-column label="操作" fixed="right" width="275">
          <template #default="scope">
            <el-button plain size="small" type="primary" @click="handleEdit(scope.row.id)">编辑</el-button>
            <el-button plain size="small" type="primary" @click="handleJump(scope.row.id)">章节列表</el-button>
            <el-button
              plain
              size="small"
              type="danger"
              @click="handleDelete(scope.row.id)"
              v-permission="{ permission: '删除章节', type: 'disabled' }"
              >删除</el-button
            >
          </template>
        </el-table-column>
      </el-table>
    </div>
    <!-- 编辑页面 -->
    <lesson-modify v-else @editClose="editClose" :editLessonId="editLessonId"></lesson-modify>

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
import LinSearch from '@/component/base/search/lin-search'
import { onMounted, ref } from 'vue'
import { ElMessageBox, ElMessage } from 'element-plus'
import lessonModel from '@/model/lesson'
import { useRouter } from 'vue-router'
import lessonModify from './lesson'

export default {
  components: {
    LinSearch,
    lessonModify,
  },
  setup() {
    const router = useRouter()
    const lessons = ref([])
    const editLessonId = ref(1)
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
      // getLessonAuthorList();
      getLessonCategoryn()
      getLesson()
    })
    /*
     *获取所有课程作者
     */
    // const getLessonAuthorList = () => {
    //   lessonModel.getLessonAuthorList().then(res => {
    //     lessonAuthorList.value = res;
    //   });
    // };

    /**
     * 获取课程章节
     */
    const getLessonCategoryn = () => {
      lessonModel.getLessonCategoryn().then(res => {
        // let result = []
        options.value = res.map(org => mapTree(org))
      })
    }

    const mapTree = org => {
      const haveChildren = Array.isArray(org.child) && org.child.length > 0
      return {
        label: org.name,
        value: org.id,
        children: haveChildren ? org.child.map(i => mapTree(i)) : [],
      }
    }

    /**
     * 获取课程章节
     * */
    const getLesson = async () => {
      try {
        loading.value = true
        // let listQueryCopy = Object.assign({}, listQuery.value);
        // if (!listQueryCopy.cid) {
        //   delete listQueryCopy.cid;
        // }

        // if (!listQueryCopy.title) {
        //   delete listQueryCopy.title;
        // }
        lessonModel.getLessonList(listQuery.value).then(res => {
          totalNum.value = res.result.total
          lessons.value = res.result.data
          // if (lessonAuthorList.value.length > 0) {
          //   lessons.value.forEach((item, index) => {
          //     if (item.author_id === lessonAuthorList.value[index] && lessonAuthorList.value[index].id) {
          //       item.author_name = lessonAuthorList.value[index].name;
          //     }
          //   });
          // }
        })
        loading.value = false
      } catch (error) {
        loading.value = false
        if (error.code === 10020) {
          lessons.value = []
        }
      }
    }

    const handleEdit = id => {
      showEdit.value = true
      editLessonId.value = id
    }

    const handleDelete = id => {
      ElMessageBox.confirm('此操作将永久删除该课程, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
      }).then(async () => {
        const res = await lessonModel.deleteLesson({
          ids: id,
        })
        if (res.code < window.MAX_SUCCESS_CODE) {
          initData()
          getLesson()
          ElMessage.success(`${res.message}`)
        }
      })
    }

    const initData = () => {
      listQuery.value = {
        title: '',
        page: 1,
        size: 10,
        cid: '',
      }
    }

    const editClose = () => {
      showEdit.value = false
      getLesson()
    }
    /*
     *搜索
     */
    const onQueryChange = query => {
      listQuery.value.title = query
      getLesson()
    }

    /**
     * 翻页
     */
    const handleCurrentChange = async val => {
      listQuery.value.page = val
      await getLesson()
    }
    /**
     * 筛选
     */
    const handleChange = e => {
      listQuery.value.cid = e[e.length - 1]
      getLesson()
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
      editLessonId,
      pageCount,
      options,
      handleJump,
      editClose,
      handleEdit,
      handleDelete,
      getLesson,
      handleChange,
      onQueryChange,
      handleCurrentChange,
      initData,
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
