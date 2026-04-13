
<template>
  <div class="container">
    <div class="title" v-if="!editNewsId">新建新闻</div>
    <div class="title" v-else>
      <span>修改新闻 {{ editNewsId }}</span>
      <span class="back" @click="back"> <i class="iconfont icon-fanhui"></i> 返回 </span>
    </div>

    <div class="wrap">
      <el-row>
        <el-col :lg="16" :md="20" :sm="24" :xs="24">
          <el-form :model="news" status-icon ref="form" label-width="150px" @submit.prevent :rules="rules">
            <el-form-item label="新闻标题：" prop="title">
              <el-input v-model="news.title" placeholder="请输入新闻标题"></el-input>
            </el-form-item>

            <el-form-item label="发布人：" prop="publisher">
              <el-input v-model="news.publisher" placeholder="请输入发布人"></el-input>
            </el-form-item>

            <el-form-item label="发布时间：" prop="publish_time">
              <el-date-picker v-model="news.publish_time" type="datetime" placeholder="选择日期时间"></el-date-picker>
            </el-form-item>

            <el-form-item label="新闻内容：" prop="content">
              <tinymce :defaultContent="news.content" @change="changeContent"></tinymce>
            </el-form-item>

            <el-form-item label="封面URL" prop="cover">
              <uploadImage
                  v-model="news.cover"
                  :qiniuToken="qiniutoken"
                  :maxNum="1"
                  :minNum="1"
                  :value="[{ display: news.cover }]"
              ></uploadImage>
            </el-form-item>

            <el-form-item>
              <el-button type="primary" @click="submitForm">保存</el-button>
              <el-button @click="resetForm">重置</el-button>
            </el-form-item>
          </el-form>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ElMessage } from 'element-plus'
import uploadImage from '@/component/base/upload-image'
import dataModel from '@/model/data'
import { News } from '@/model/news'
import tinymce from '@/component/base/tinymce'

const route = useRoute()
const router = useRouter()

const props = defineProps({
  editNewsId: {
    type: String,
    default: () => '',
  },
})



const qiniutoken = ref('')


const news = reactive({
  title: '',
  publisher: '',
  publish_time: '',
  content: '',
  cover: '',
})

const rules = {
  title: [{ required: true, message: '请输入新闻标题', trigger: 'blur' }],
  publisher: [{ required: true, message: '请输入发布人', trigger: 'blur' }],
  publish_time: [{ required: true, message: '请选择发布时间', trigger: 'change' }],
  content: [{ required: true, message: '请输入新闻内容', trigger: 'blur' }],
  cover: [{ required: true, message: '请上传封面图片', trigger: 'change' }],
}

const changeContent = e => {
  news.content = e
}

const getQiniuToken = async () => {
  qiniutoken.value = await dataModel.getQiniuToken()
}

const getNewsDetail = async () => {
  const response = await News.getNewsDetail(props.editNewsId)
  if (response) {
    for (let key in news) {
      if (response.hasOwnProperty(key)) {
        news[key] = response[key]
      }
    }
  } else {
    ElMessage.error('获取新闻详情失败')
  }
}

onMounted(async () => {
  await getQiniuToken()
  if (props.editNewsId) {
    await getNewsDetail()
  }
})

const submitForm = async () => {
  try {
    const data = {
      title: news.title,
      publisher: news.publisher,
      publish_time: news.publish_time,
      content: news.content,
      cover: news.cover,
    }

    let response

    if (props.editNewsId) {
      data.id = props.editNewsId
      response = await News.saveNews(data)
    } else {
      response = await News.saveNews(data)
    }

    if (response) {
      ElMessage.success('保存成功')
    } else {
      ElMessage.error('保存失败')
    }
  } catch (error) {
    ElMessage.error('保存失败')
  }
}

const resetForm = () => {
  Object.assign(news, {
    title: '',
    publisher: '',
    publish_time: '',
    content: '',
    cover: '',
  })
  form.value.resetFields()
}

const back = () => {
  router.back()
}
</script>


<style lang="scss" scoped>
.container {
  .title {
    height: 59px;
    line-height: 59px;
    color: $parent-title-color;
    font-size: 16px;
    font-weight: 500;
    text-indent: 40px;
    border-bottom: 1px solid #dae1ec;

    .back {
      float: right;
      margin-right: 40px;
      cursor: pointer;
    }
  }

  .wrap {
    padding: 20px;
  }

  .submit {
    float: left;
  }

  .add-btn {
    margin-left: 30px;
  }
}
.custom-label {
  position: relative;

  &::after {
    content: '*';
    position: absolute;
    left: -8px;
    top: 0;
    color: var(--el-color-danger);
    font-size: 14px;
    font-weight: 500;
  }
}
.ssq-box {
  display: flex;
  .el-select {
    width: 140px;
    margin-right: 10px;
  }
}
</style>
