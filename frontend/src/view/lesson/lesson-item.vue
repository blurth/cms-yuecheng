<template>
  <div class="container">
    <div class="title">
      <span>修改章节{{ editlessonItemId }}</span>
      <span class="back" @click="back"> <i class="iconfont icon-fanhui"></i> 返回 </span>
    </div>
    <!-- </div> -->

    <div class="wrap">
      <el-row>
        <el-col :lg="16" :md="20" :sm="24" :xs="24">
          <el-form :model="lesson" status-icon ref="form" label-width="100px" @submit.prevent :rules="rules">
            <!-- <el-form-item label="标题" prop="title">
                <el-input v-model="lesson.title" placeholder="请填写标题"></el-input>
              </el-form-item> -->
            <el-form-item label="名称" prop="name">
              <el-input v-model="lesson.name" placeholder="请填写名称"></el-input>
            </el-form-item>
            <el-form-item label="排序" prop="order_number">
              <el-input v-model="lesson.order_number" placeholder="请填写排序"></el-input>
            </el-form-item>
            <el-form-item label="发布时间" prop="release_time">
              <el-date-picker v-model="lesson.release_time" type="datetime" placeholder="选择日期时间">
              </el-date-picker>
            </el-form-item>

            <el-form-item label="封面" prop="img">
              <uploadImage
                v-model="lesson.img"
                :qiniuToken="qiniutoken"
                :maxNum="1"
                :minNum="1"
                :value="[{ display: lesson.img }]"
              ></uploadImage>
            </el-form-item>

            <el-form-item
              label="选择音/视频"
              prop="play_url"
              v-loading="videoLoading"
              element-loading-text="视频上传中"
            >
              <input type="file" @change="httpRequest($event)" />
              <el-input v-model="lesson.play_url" :disabled="true"></el-input>
            </el-form-item>
            <el-form-item label="视频时长（秒）" prop="duration">
              <el-input v-model="lesson.duration"></el-input>
            </el-form-item>

            <el-form-item label="是否上架" prop="status">
              <el-switch
                v-model="lesson.status"
                :active-value="1"
                :inactive-value="0"
                active-text="上架"
                inactive-text="下架"
              >
              </el-switch>
            </el-form-item>

            <el-form-item label="文章" prop="content">
              <tinymce :defaultContent="lesson.content" @change="changeContent"></tinymce>
            </el-form-item>

            <el-form-item class="submit">
              <el-button type="primary" @click="submitForm">保 存</el-button>
              <el-button @click="resetForm">重 置</el-button>
            </el-form-item>
          </el-form>
        </el-col>
      </el-row>
    </div>
  </div>
</template>
<script>
import uploadImage from '@/component/base/upload-image'
import { reactive, ref, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import lessonModel from '@/model/lesson'
import tinymce from '@/component/base/tinymce'
import { post } from '@/lin/plugin/axios'
import md5 from 'md5'
import dataModel from '@/model/data'

export default {
  components: {
    uploadImage,
    tinymce,
  },
  props: {
    editlessonItemId: {
      type: Number,
      default: null,
    },
    itemslesson: {
      type: Array,
      default: () => [],
    },
    pid: {
      type: [Array, String],
      default: '',
    },
  },
  setup(props, context) {
    const showEdit = ref(false)
    const form = ref(null)
    const loading = ref(false)
    const lessons = ref([])
    const videoLoading = ref(false)
    const lesson = reactive({
      pid: '',
      name: '',
      status: '',
      img: '',
      release_time: '',
      type: '',
      lesson_id: '',
      index: '',
      content: '',
      order_number: '',
      play_url: '',
      duration: '',
    })
    const data = ref({
      token: '',
      key: '',
    })
    const qiniutoken = ref('')
    const listAssign = (a, b) => Object.keys(a).forEach(key => {
      a[key] = b[key] || a[key]
    })

    /**
     * 表单规则验证
     */
    const { rules } = getRules()

    const getQiniuToken = async () => {
      qiniutoken.value = await dataModel.getQiniuToken()
    }
    getQiniuToken()
    onMounted(async () => {
      if (props.editlessonItemId) {
        getLessonSectionDetail()
      }
    })

    const getLessonSectionDetail = async () => {
      loading.value = true
      const res = await lessonModel.getLessonSectionDetail(props.editlessonItemId)
      listAssign(lesson, res)
      loading.value = false
    }

    const httpRequest = e => {
      const file = e.target.files[0]
      handleTime(file)
      const data = {
        token: qiniutoken.value,
        key: `${new Date().getFullYear()}/${new Date().getMonth() + 1}/${new Date().getDate()}${md5(
          file.name.split('.')[0],
        )}${new Date().getTime()}.${file.name.split('.')[file.name.split('.').length - 1]}`,
        file,
      }
      videoLoading.value = true
      return post('https://up-z0.qiniup.com', data).then(res => {
        lesson.play_url = `https://qn.jixiangjiaoyu.com/${res.key}`
        videoLoading.value = false
      })
    }

    const changeContent = e => {
      lesson.content = e
    }

    // 获取上传文件时长
    const handleTime = file => {
      const url = URL.createObjectURL(file)
      const audioElement = new Audio(url)
      audioElement.addEventListener('loadedmetadata', () => {
        lesson.duration = audioElement.duration
      })
    }

    // 重置表单
    const resetForm = () => {
      form.value.resetFields()
    }

    const submitForm = async formName => {
      form.value.validate(async valid => {
        if (valid) {
          let res = {}
          if (props.editlessonItemId) {
            res = await lessonModel.saveLessonSection({
              id: props.editlessonItemId,
              ...lesson,
            })
            context.emit('editClose')
          } else {
            res = await lessonModel.saveLessonSection({
              ...lesson,
              pid: props.pid,
            })
            resetForm(formName)
          }
          if (res.code < window.MAX_SUCCESS_CODE) {
            ElMessage.success(`${res.message}`)
          }
        } else {
          console.error('error submit!!')
          ElMessage.error('请将信息填写完整')
        }
      })
    }

    const back = () => {
      context.emit('editClose')
    }

    return {
      data,
      lesson,
      lessons,
      form,
      rules,
      showEdit,
      videoLoading,
      qiniutoken,
      changeContent,
      handleTime,
      back,
      resetForm,
      submitForm,
      httpRequest,
      getQiniuToken,
    }

    /**
     * 表单验证规则
     */
    function getRules() {
      /**
       * 验证回调函数
       */
      const checkInfo = (rule, value, callback) => {
        if (!value) {
          callback(new Error('信息不能为空'))
        }
        callback()
      }
      const rules = {
        title: [{ validator: checkInfo, trigger: 'blur', required: true }],
        // img: [{ validator: checkInfo, trigger: 'blur', required: true }],
      }
      return { rules }
    }
  },
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
</style>
