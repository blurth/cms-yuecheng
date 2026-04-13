<template>
  <div class="container">
    <div v-if="!showEdit">
      <div class="title" v-if="!editBannerId">新建轮播图</div>
      <div class="title" v-else>
        <span>修改轮播图{{ editBannerId }}</span>
        <span class="back" @click="back"> <i class="iconfont icon-fanhui"></i> 返回 </span>
      </div>
      <!-- </div> -->

      <div class="wrap">
        <el-row>
          <el-col :lg="16" :md="20" :sm="24" :xs="24">
            <el-form :model="banner" status-icon ref="form" label-width="100px" @submit.prevent :rules="rules">
              <el-form-item label="标题" prop="title">
                <el-input v-model="banner.title" placeholder="请填写标题"></el-input>
              </el-form-item>
              <el-form-item label="名称" prop="name">
                <el-input v-model="banner.name" placeholder="请填写名称"></el-input>
              </el-form-item>

              <el-form-item label="主图" prop="img">
                <uploadImage
                  v-model="banner.img"
                  :qiniuToken="qiniutoken"
                  :maxNum="1"
                  :minNum="1"
                  :value="[{ display: banner.img }]"
                ></uploadImage>
              </el-form-item>
              <el-form-item label="描述" prop="description">
                <el-input
                  type="textarea"
                  :autosize="{ minRows: 4, maxRows: 8 }"
                  placeholder="请输入简介"
                  v-model="banner.description"
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
        <!-- <div v-if="editBannerId">
          <el-divider />
          <div style="line-height: 60px">
            <span>Banner-Item列表</span>
            <el-button type="primary" @click="handleAddBanner" class="el-button add-btn is-plain"
              >新增Banner-Item</el-button
            >
          </div>

        </div> -->
      </div>
    </div>
  </div>
</template>
<script>
import uploadImage from '@/component/base/upload-image'
import { reactive, ref, onMounted } from 'vue'
import { ElMessageBox, ElMessage } from 'element-plus'
import bannerModel from '@/model/banner'
import dataModel from '@/model/data'

export default {
  components: {
    uploadImage,
  },
  props: {
    editBannerId: {
      type: Number,
      default: null,
    },
    showAdd: {
      type: Boolean,
      default: false,
    },
    itemsBanner: {
      type: Array,
      default: () => [],
    },
  },
  setup(props, context) {
    const showEdit = ref(false)
    const form = ref(null)
    const editBannerItemId = ref('')
    const qiniutoken = ref('')
    const loading = ref(false)
    const banners = ref([])
    const banner = reactive({ title: '', name: '', description: '', img: '' })
    const data = ref({
      token: '',
      key: '',
    })
    const listAssign = (a, b) => Object.keys(a).forEach(key => {
      a[key] = b[key] || a[key]
    })

    /**
     * 表单规则验证
     */
    const { rules } = getRules()

    onMounted(async () => {
      if (props.editBannerId) {
        getBannerDetail()
      }
    })

    const getQiniuToken = async () => {
      qiniutoken.value = await dataModel.getQiniuToken()
    }
    getQiniuToken()

    const getBannerDetail = async () => {
      loading.value = true
      const res = await bannerModel.getBannerDetail(props.editBannerId)
      listAssign(banner, res)
      loading.value = false
    }

    const handleAddBanner = () => {
      showEdit.value = true
    }

    // 重置表单
    const resetForm = () => {
      form.value.resetFields()
    }

    const submitForm = async formName => {
      form.value.validate(async valid => {
        if (valid) {
          let res = {}
          if (props.editBannerId) {
            res = await bannerModel.saveBanner({
              id: props.editBannerId,
              ...banner,
            })
            context.emit('editClose')
          } else {
            res = await bannerModel.saveBanner(banner)
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

    const handleLook = id => {
      showEdit.value = true
      editBannerItemId.value = id
    }

    const back = () => {
      context.emit('editClose')
    }

    const editClose = () => {
      showEdit.value = false
    }

    return {
      data,
      banner,
      banners,
      form,
      rules,
      showEdit,
      editBannerItemId,
      qiniutoken,
      getQiniuToken,
      editClose,
      handleLook,
      back,
      handleAddBanner,
      resetForm,
      submitForm,
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
        img: [{ validator: checkInfo, trigger: 'blur', required: true }],
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
