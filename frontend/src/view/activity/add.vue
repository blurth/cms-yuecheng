<template>
  <div class="container">


    <div class="title" v-if="!editActivityId">添加活动</div>
    <div class="title" v-else>
      <span>修改活动 {{ editActivityId }}</span>
      <span class="back" @click="back"> <i class="iconfont icon-fanhui"></i> 返回 </span>
    </div>





    <div class="wrap">
      <el-row>
        <el-col :lg="16" :md="20" :sm="24" :xs="24">
          <el-form :model="activity" status-icon ref="form" label-width="150px" @submit.prevent :rules="rules">
            <el-form-item label="活动主题：" prop="theme">
              <el-input v-model="activity.theme" placeholder="请输入活动主题"></el-input>
            </el-form-item>

            <el-form-item label="活动图片URL" prop="profile_picture">
              <uploadImage
                  v-model="activity.image_url"
                  :qiniuToken="qiniutoken"
                  :maxNum="1"
                  :minNum="1"
                  :value="[{ display: activity.image_url }]"
              ></uploadImage>
            </el-form-item>

            <el-form-item label="活动介绍：" prop="description">
              <tinymce :defaultContent="activity.description" @change="changeContent"></tinymce>
            </el-form-item>

            <el-form-item label="活动开始时间：" prop="start_time">
              <el-date-picker v-model="activity.start_time" type="datetime" placeholder="选择日期时间"></el-date-picker>
            </el-form-item>

            <el-form-item label="活动结束时间：" prop="end_time">
              <el-date-picker v-model="activity.end_time" type="datetime" placeholder="选择日期时间"></el-date-picker>
            </el-form-item>

            <el-form-item label="活动报名人数限制：" prop="registration_limit">
              <el-input-number v-model="activity.registration_limit" controls-position="right" :min="0" placeholder="请输入活动报名人数限制"></el-input-number>
            </el-form-item>

            <el-form-item label="活动报名开始时间：" prop="registration_start_time">
              <el-date-picker v-model="activity.registration_start_time" type="datetime" placeholder="选择日期时间"></el-date-picker>
            </el-form-item>

            <el-form-item label="活动报名结束时间：" prop="registration_end_time">
              <el-date-picker v-model="activity.registration_end_time" type="datetime" placeholder="选择日期时间"></el-date-picker>
            </el-form-item>


            <el-form-item label="活动核销开始时间：" prop="check_in_start_time">
              <el-date-picker v-model="activity.check_in_start_time" type="datetime" placeholder="选择日期时间"></el-date-picker>
            </el-form-item>

            <el-form-item label="活动核销结束时间：" prop="check_in_end_time">
              <el-date-picker v-model="activity.check_in_end_time" type="datetime" placeholder="选择日期时间"></el-date-picker>
            </el-form-item>

            <el-form-item v-for="(item, index) in activity.conditions" :key="index" :label="'报名条件' + (index + 1)">
              <el-input v-model="item.key" placeholder="请输入键"></el-input>
              <el-input v-model="item.value" placeholder="请输入值"></el-input>
              <el-button @click="addCondition">添加报名条件</el-button>
              <el-button @click="removeCondition(index)">删除报名条件</el-button>
            </el-form-item>


            <el-form-item label="上课地点：" prop="address">
              <el-input v-model="activity.address" placeholder="请输入上课地点"></el-input>
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
import {ref, reactive, onMounted, defineEmits} from 'vue'
import { ElMessage } from 'element-plus'
import uploadImage from '@/component/base/upload-image'
import dataModel from "@/model/data";
import Activity from "@/model/activity";
import tinymce from '@/component/base/tinymce'
import moment from 'moment'
const qiniutoken = ref(null)


const props = defineProps({
  editActivityId: {
    type: String,
    default: () => '',
  },
})

const activity = reactive({
  theme: '',
  image_url: '',
  description: '',
  registration_code: '',
  registration_limit: '',
  registration_start_time: '',
  registration_end_time: '',
  check_in_code: '',
  check_in_start_time: '',
  check_in_end_time: '',
  start_time: '',
  end_time: '',
  conditions: [{ key: '', value: '' }],
  address: '',
})

const removeCondition = (index) => {
  activity.conditions.splice(index, 1);
};


const changeContent = e => {
  activity.description = e
}
/*
[{"key": "报名对象：小学3-6年级亲子家庭", "value": ""}
    [{"key": "报名对象","value": "小学3-6年级亲子家庭"}, {"key": "随身携带","value": "身份证（门口安检核验备用）"}]
*/
const addCondition = () => {
  activity.conditions.push({ key: '', value: '' });
};
const getQiniuToken = async () => {
  qiniutoken.value = await dataModel.getQiniuToken()
}

getQiniuToken()
const submitForm = async () => {
  // Validate form and submit data to your API
  try {
    //如果是编辑 id 传入

    if (props.editActivityId) {
      activity.id = props.editActivityId
    }

    activity.check_in_start_time = moment(activity.check_in_start_time).format('YYYY-MM-DD HH:mm')
    activity.check_in_end_time = moment(activity.check_in_end_time).format('YYYY-MM-DD HH:mm')
    activity.registration_start_time = moment(activity.registration_start_time).format('YYYY-MM-DD HH:mm')
    activity.registration_end_time = moment(activity.registration_end_time).format('YYYY-MM-DD HH:mm')
    activity.start_time = moment(activity.start_time).format('YYYY-MM-DD HH:mm')
    activity.end_time = moment(activity.end_time).format('YYYY-MM-DD HH:mm')

    const response = await Activity.saveActivity(activity)

    if (response.code == 0) {
      ElMessage.success('保存成功')
    } else {
      ElMessage.error('保存失败')
    }
  } catch (error) {

    ElMessage.error('保存失败')
  }



}

const emit = defineEmits(['editClose'])

const back = () => {
  emit('editClose')
}


onMounted(async () => {
  if (props.editActivityId) {
    await getActivities()
  }
})


const getActivities = async () => {
  const response = await Activity.getActivity(props.editActivityId)
  if (response) {
    for (let key in activity) {
      if (response.hasOwnProperty(key)) {
        activity[key] = response[key];
      }
    }
  } else {
    ElMessage.error('获取活动详情失败')
  }
}
const resetForm = () => {
  // Reset form fields
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
