<template>
  <div class="container">
    <div class="title" v-if="!editLessonId">新建课程</div>
    <div class="title" v-else>
      <span>修改课程{{ editLessonId }}</span>
      <span class="back" @click="back"> <i class="iconfont icon-fanhui"></i> 返回 </span>
    </div>

    <div class="wrap">
      <el-row>
        <el-col :lg="16" :md="20" :sm="24" :xs="24">
          <el-form :model="lesson" status-icon ref="form" label-width="100px" @submit.prevent :rules="rules">
            <el-form-item label="标题" prop="title">
              <el-input v-model="lesson.title" placeholder="请填写标题"></el-input>
            </el-form-item>
            <el-form-item label="副标题" prop="sub_title">
              <el-input v-model="lesson.sub_title" placeholder="请填写标题"></el-input>
            </el-form-item>
            <el-form-item label="章节数量" prop="lesson_count">
              <el-input v-model="lesson.lesson_count" placeholder="请填写章节数量"></el-input>
            </el-form-item>
            <el-form-item label="学习人数" prop="learn_num">
              <el-input v-model="lesson.learn_num" placeholder="请填写学习人数"></el-input>
            </el-form-item>
            <el-form-item label="封面" prop="cover">
              <uploadImage
                v-model="lesson.cover"
                :qiniuToken="qiniutoken"
                :maxNum="1"
                :minNum="1"
                :value="[{ display: lesson.cover }]"
              ></uploadImage>
            </el-form-item>

            <el-form-item label="类型" prop="resource_type">
              <el-select v-model="lesson.resource_type">
                <el-option
                  v-for="(item, index) in resourceTypes"
                  :key="index"
                  :value="item.value"
                  :label="item.label"
                ></el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="文章类型" prop="article_type" v-if="lesson.resource_type === 3">
              <el-select v-model="lesson.article_type">
                <el-option value="1" label="科普"></el-option>
                <el-option value="2" label="指导方法"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="性别类型" prop="gender_type">
              <el-select v-model="lesson.gender_type">
                <el-option
                  v-for="(item, index) in genderTypes"
                  :key="index"
                  :value="item.value"
                  :label="item.label"
                ></el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="所属老师" prop="author_id">
              <el-select v-model="lesson.author_id">
                <el-option
                  v-for="(item, index) in authorList"
                  :key="index"
                  :value="item.id"
                  :label="item.name"
                ></el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="分类">
              <el-cascader v-model="lesson.cid" :options="options" @change="handleChange"></el-cascader>
            </el-form-item>
            <el-form-item label="排序">
              <el-input v-model="lesson.index" placeholder="请填写标题"></el-input>
            </el-form-item>
            <el-form-item label="介绍">
              <el-input
                type="textarea"
                :autosize="{ minRows: 4, maxRows: 8 }"
                placeholder="请输入介绍"
                v-model="lesson.description"
              >
              </el-input>
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
import { reactive, ref, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import lessonModel from '@/model/lesson'
import uploadImage from '@/component/base/upload-image'
import dataModel from '@/model/data'

export default {
  components: {
    uploadImage,
  },
  props: {
    editLessonId: {
      type: Number,
      default: null,
    },
  },
  setup(props, context) {
    const authorList = ref([])
    const qiniutoken = ref('')
    const form = ref(null)
    const loading = ref(false)
    const options = ref([])
    const lesson = reactive({
      id: '',
      title: '',
      sub_title: '',
      author: '',
      summary: '',
      image: '',
      cid: [1, 2, 5],
      article_type: '',
      lesson_count: '',
      learn_num: '',
      index: '',
      author_id: '',
    })
    const genderTypes = ref([
      { value: 0, label: '通用' },
      { value: 1, label: '男' },
      { value: 2, label: '女' },
    ])
    const resourceTypes = ref([
      { value: 1, label: '视频' },
      { value: 2, label: '音频' },
      { value: 3, label: '文章' },
    ])
    const listAssign = (a, b) => Object.keys(a).forEach(key => {
      a[key] = b[key] || a[key]
    })

    /**
     * 表单规则验证
     */
    const { rules } = getRules()

    onMounted(() => {
      getLessonCategoryn()
      if (props.editLessonId) {
        lesson.id = props.editLessonId
        getlesson()
      }
    })

    /**
     * 获取分类
     */
    const getLessonCategoryn = () => {
      lessonModel.getLessonCategoryn().then(res => {
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

    const getlesson = async () => {
      loading.value = true
      const res = await lessonModel.getlesson(props.editLessonId)
      listAssign(lesson, res)
      loading.value = false
    }

    const handleChange = e => {
      console.log(e)
    }

    // 重置表单
    const resetForm = () => {
      form.value.resetFields()
    }

    const submitForm = async formName => {
      form.value.validate(async valid => {
        if (valid) {
          let res = {}
          const formData = { ...lesson }
          if (!props.editLessonId) {
            delete formData.id
          }
          formData.cid = formData.cid[formData.cid.length - 1]
          res = await lessonModel.saveLesson(formData)
          if (!props.editLessonId) {
            resetForm(formName)
          } else {
            context.emit('editClose')
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

    const getLessonAuthor = async () => {
      authorList.value = await lessonModel.getLessonAuthorList()
    }

    getLessonAuthor()

    const back = () => {
      context.emit('editClose')
    }

    const getQiniuToken = async () => {
      qiniutoken.value = await dataModel.getQiniuToken()
    }
    getQiniuToken()

    return {
      back,
      lesson,
      form,
      rules,
      genderTypes,
      resourceTypes,
      options,
      resetForm,
      submitForm,
      handleChange,
      getLessonCategoryn,
      qiniutoken,
      authorList,
      getQiniuToken,
      getLessonAuthor,
    }
  },
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
    author: [{ validator: checkInfo, trigger: 'blur', required: true }],
    summary: [{ validator: checkInfo, trigger: 'blur', required: true }],
    image: [{ validator: checkInfo, trigger: 'blur', required: true }],
  }
  return { rules }
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
}
</style>
