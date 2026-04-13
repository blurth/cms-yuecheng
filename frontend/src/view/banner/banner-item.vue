<template>
  <div class="container">
    <div class="title">
      <span>修改轮播图元素{{ editBannerItemId }}</span>
      <span class="back" @click="back"> <i class="iconfont icon-fanhui"></i> 返回 </span>
    </div>
    <!-- </div> -->

    <div class="wrap">
      <el-row>
        <el-col :lg="16" :md="20" :sm="24" :xs="24">
          <el-form :model="banner" status-icon ref="form" label-width="100px" @submit.prevent :rules="rules">
            <!-- <el-form-item label="标题" prop="title">
              <el-input v-model="banner.title" placeholder="请填写标题"></el-input>
            </el-form-item> -->
            <el-form-item label="名称" prop="name">
              <el-input v-model="banner.name" placeholder="请填写名称"></el-input>
            </el-form-item>
            <el-form-item label="排序" prop="index">
              <el-input v-model="banner.index" placeholder="请填写排序"></el-input>
            </el-form-item>
            <el-form-item label="关键字" prop="keyword">
              <el-input v-model="banner.keyword" placeholder="请填写关键字"></el-input>
            </el-form-item>
            <el-form-item label="类型" prop="type">
              <!-- <el-input v-model="banner.type" placeholder="请填写类型"></el-input> -->
              <el-select v-model="banner.type">
                <el-option
                  v-for="(item, index) in types"
                  :key="index"
                  :label="item.label"
                  :value="item.value"
                ></el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="图片" prop="img">
              <uploadImage
                v-model="banner.img"
                :qiniuToken="qiniutoken"
                :maxNum="1"
                :minNum="1"
                :value="[{ display: banner.img }]"
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
import uploadImage from '@/component/base/upload-image'
import { reactive, ref, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import bannerModel from '@/model/banner'
import dataModel from '@/model/data'

export default {
  components: {
    uploadImage,
  },
  props: {
    editBannerItemId: {
      type: Number,
      default: null,
    },
    bannerId: {
      type: Number,
      default: null,
    },
    itemsBanner: {
      type: Array,
      default: () => [],
    },
  },
  setup(props, context) {
    const showEdit = ref(false)
    const form = ref(null)
    const loading = ref(false)
    const qiniutoken = ref('')
    const banners = ref([])
    const types = ref([
      { label: '成长专栏得视频', value: 1 },
      { label: '指导方法百科', value: 2 },
      { label: '指导方法指导方法', value: 3 },
      { label: '指导方法对音频', value: 4 },
      { label: '指导方法的视频', value: 5 },
    ])
    const banner = reactive({ name: '', img: '', keyword: '', type: '', banner_id: '', index: '' })
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
      banner.banner_id = props.bannerId
      if (props.editBannerItemId) {
        getBannerItemDetail()
      }
    })

    const getQiniuToken = async () => {
      qiniutoken.value = await dataModel.getQiniuToken()
    }
    getQiniuToken()

    const getBannerItemDetail = async () => {
      loading.value = true
      const res = await bannerModel.getBannerItemDetail(props.editBannerItemId)
      listAssign(banner, res)
      loading.value = false
    }

    // 重置表单
    const resetForm = () => {
      form.value.resetFields()
    }

    const submitForm = async formName => {
      form.value.validate(async valid => {
        if (valid) {
          let res = {}
          if (props.editBannerItemId) {
            res = await bannerModel.saveBannerItem({
              id: props.editBannerItemId,
              ...banner,
            })
            context.emit('editClose')
          } else {
            res = await bannerModel.saveBannerItem(banner)

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
      banner,
      banners,
      form,
      rules,
      showEdit,
      qiniutoken,
      types,
      getQiniuToken,
      back,
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
