<template>
  <div class="container">
    <div class="title" v-if="!editPsychologistId">新建心理老师</div>
    <div class="title" v-else>
      <span>修改心理老{{ editPsychologistId }}</span>
      <span class="back" @click="back"> <i class="iconfont icon-fanhui"></i> 返回 </span>
    </div>

    <div class="wrap">
      <el-row>
        <el-col :lg="16" :md="20" :sm="24" :xs="24">
          <el-form :model="Psychologist" status-icon ref="form" label-width="100px" @submit.prevent :rules="rules">
            <el-form-item label="姓名" prop="name">
              <el-input :maxLength="20" v-model="Psychologist.name" placeholder="请填写姓名"></el-input>
            </el-form-item>
            <el-form-item label="手机号" prop="phone">
              <el-input v-model="Psychologist.phone" placeholder="请填写手机号"></el-input>
            </el-form-item>
            <el-form-item label="性别" prop="gender">
              <el-select v-model="Psychologist.gender" placeholder="请选择性别">
                <el-option label="男" :value="1"></el-option>
                <el-option label="女" :value="2"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="经验年数" prop="experience_years">
              <el-input v-model="Psychologist.experience_years" placeholder="请填写经验年数"></el-input>
            </el-form-item>
            <el-form-item label="简介" prop="bio">
              <el-input v-model="Psychologist.bio" placeholder="请填写简介"></el-input>
            </el-form-item>
            <el-form-item label="擅长领域" prop="specialization">
              <el-input v-model="Psychologist.specialization" placeholder="请填写擅长领域"></el-input>
            </el-form-item>
            <el-form-item label="级别" prop="level">
              <el-input v-model="Psychologist.level" placeholder="请填写级别 举例(高中,小学)用英文逗号分隔"></el-input>
            </el-form-item>
            <el-form-item label="头像" prop="profile_picture">
              <uploadImage
                  v-model="Psychologist.profile_picture"
                  :qiniuToken="qiniutoken"
                  :maxNum="1"
                  :minNum="1"
                  :value="[{ display: Psychologist.profile_picture }]"
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

<script setup>
import uploadImage from '@/component/base/upload-image'
import dataModel from '@/model/data'
import PsychologistModel from '@/model/psychology-teacher.js'
import { ElMessage } from 'element-plus'
import { ref, reactive, defineEmits, onMounted, watch } from 'vue'

const emit = defineEmits(['editClose'])
const Psychologist = reactive({
  name: '',
  phone: '',
  gender: '',
  experience_years: '',
  bio: '',
  specialization: '',
  level: '',
  profile_picture: '',
})

const props = defineProps({
  editPsychologistId: {
    type: String,
    default: () => '',
  },
})
const qiniutoken = ref('')
const form = ref(null)

const listAssign = (a, b) => Object.keys(a).forEach(key => {
  a[key] = b[key] || a[key]
})

/**
 * 表单验证规则
 */
const getRules = () => {
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
  }
  return { rules }
}
const { rules } = getRules()

onMounted(async () => {
  if (props.editPsychologistId) {
    await getPsychologistDetail()
  }
})
const getPsychologistDetail = async () => {
  // loading.value = true

  const res = await PsychologistModel.getPsychologistDetail(props.editPsychologistId)
  listAssign(Psychologist, res)
  // loading.value = false
}



const back = () => {
  emit('editClose')
}

const getQiniuToken = async () => {
  qiniutoken.value = await dataModel.getQiniuToken()
}
getQiniuToken()

const submitForm = async formName => {
  form.value.validate(async valid => {
    if (valid) {
      console.log(valid)
      let res = {}
      if (props.editPsychologistId) {
        res = await PsychologistModel.savePsychologist({
          id: props.editPsychologistId,
          ...Psychologist,
        })
        emit('editClose')
      } else {
        res = await PsychologistModel.savePsychologist(Psychologist)
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

// 重置表单
const resetForm = () => {
  Psychologist.name = ''
  Psychologist.phone = ''
  Psychologist.gender = ''
  Psychologist.experience_years = ''
  Psychologist.bio = ''
  Psychologist.specialization = ''
  Psychologist.level = ''
  Psychologist.profile_picture = ''
  form.value.resetFields()
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
