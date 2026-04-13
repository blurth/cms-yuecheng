<template>
  <div class="container">
    <div class="title" v-if="!editAuthorId">
      <span>新建讲师</span>
      <span class="back" @click="back"> <i class="iconfont icon-fanhui"></i> 返回 </span>
    </div>
    <div class="title" v-else>
      <span>修改讲师{{ editAuthorId }}</span>
      <span class="back" @click="back"> <i class="iconfont icon-fanhui"></i> 返回 </span>
    </div>

    <div class="wrap">
      <el-row>
        <el-col :lg="16" :md="20" :sm="24" :xs="24">
          <el-form :model="author" status-icon ref="form" label-width="100px" @submit.prevent :rules="rules">
            <el-form-item label="姓名" prop="name">
              <el-input v-model="author.name" placeholder="请填写姓名"></el-input>
            </el-form-item>
            <el-form-item label="头像" prop="avatar_url">
              <uploadImage
                v-model="author.avatar_url"
                :qiniuToken="qiniutoken"
                :maxNum="1"
                :minNum="1"
                :value="[{ display: author.avatar_url }]"
              ></uploadImage>
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
// import authorModel from '@/model/author'
import uploadImage from '@/component/base/upload-image'
import dataModel from '@/model/data'
import lessonModel from '@/model/lesson'

export default {
  components: {
    uploadImage,
  },
  props: {
    editAuthorId: {
      type: Number,
      default: null,
    },
  },
  setup(props, context) {
    const qiniutoken = ref('')
    const form = ref(null)
    const loading = ref(false)
    const author = reactive({
      name: '',
      id: '',
      avatar_url: '',
    })

    const listAssign = (a, b) => {
      Object.keys(a).forEach(key => {
        a[key] = b[key] || a[key]
      })
    }
    /**
     * 表单规则验证
     */
    const { rules } = getRules()

    onMounted(() => {
      if (props.editAuthorId) {
        getLessonAuthorList()
      }
    })

    const getLessonAuthorList = () => {
      lessonModel.getLessonAuthorList().then(res => {
        res.forEach(item => {
          if (item.id === props.editAuthorId) {
            listAssign(author, item)
          }
        })
      })
    }

    // 重置表单
    const resetForm = () => {
      form.value.resetFields()
    }

    const getQiniuToken = async () => {
      qiniutoken.value = await dataModel.getQiniuToken()
    }
    getQiniuToken()

    const submitForm = async formName => {
      form.value.validate(async valid => {
        if (valid) {
          let res = {}
          const formData = { ...author }
          if (!props.editAuthorId) {
            delete formData.id
            res = await lessonModel.saveLessonAuthor(formData)
            resetForm(formName)
          } else {
            res = await lessonModel.saveLessonAuthor(formData)
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

    const back = () => {
      context.emit('editClose')
    }

    return {
      back,
      author,
      form,
      rules,
      qiniutoken,
      resetForm,
      submitForm,
      getQiniuToken,
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
    name: [{ validator: checkInfo, trigger: 'blur', required: true }],
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
