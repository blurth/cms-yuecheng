<template>
  <div class="container">
    <div class="title">添加排期</div>
    <div class="wrap">
      <el-row>
        <el-col :lg="16" :md="20" :sm="24" :xs="24">
          <el-form :model="schedule" status-icon ref="form" label-width="100px" @submit.prevent :rules="rules">
            <el-form-item label="心理老师：" prop="psychologist_id">
              <el-select v-model="schedule.psychologist_id" placeholder="选择心理老师">
                <el-option
                  v-for="psychologist in psychologists.data"
                  :key="psychologist.id"
                  :label="psychologist.name"
                  :value="psychologist.id"
                ></el-option>
              </el-select>
            </el-form-item>

            <el-form-item label="预约日期：" prop="appointment_date">
              <el-date-picker v-model="schedule.appointment_date" type="date" placeholder="Select date"></el-date-picker>
            </el-form-item>


        <el-form-item label="开始时间：" prop="start_time">
          <el-time-picker v-model="schedule.start_time" placeholder="选择开始时间" format="HH:mm"></el-time-picker>
        </el-form-item>

        <el-form-item label="结束时间：" prop="end_time">
          <el-time-picker v-model="schedule.end_time" placeholder="选择结束时间" format="HH:mm"></el-time-picker>
        </el-form-item>


            <el-form-item label="重复项目：" prop="repeat">
              <el-select v-model="schedule.repeat" placeholder="选择重复项目">
                <el-option label="重复" value="repeat"></el-option>
                <el-option label="仅一次" value="once"></el-option>
              </el-select>
            </el-form-item>

            <el-form-item label="重复频率：" prop="repeatFrequency" v-if="schedule.repeat === 'repeat'">
              <el-select v-model="schedule.repeatFrequency" placeholder="选择重复频率">
                <el-option label="每天" value="daily"></el-option>
                <el-option label="工作日" value="workdays"></el-option>
                <el-option label="周末" value="weekends"></el-option>
                <el-option label="每周" value="weekly"></el-option>
                <el-option label="每两周" value="biweekly"></el-option>
                <el-option label="每月" value="monthly"></el-option>
                <el-option label="每三个月" value="quarterly"></el-option>
                <el-option label="每年" value="yearly"></el-option>
              </el-select>
            </el-form-item>

            <el-form-item label="结束日期：" prop="endDate" v-if="schedule.repeat === 'repeat'">
              <el-date-picker v-model="schedule.endDate" type="date" placeholder="选择结束日期"></el-date-picker>
            </el-form-item>


            <el-form-item label="地址：" prop="address">
              <el-input v-model="schedule.address" placeholder="输入地址"></el-input>
            </el-form-item>

            <el-form-item class="submit">
              <el-button type="primary" @click="submitForm">Save</el-button>
              <el-button @click="resetForm">Reset</el-button>
            </el-form-item>
          </el-form>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage } from 'element-plus'

import AppointmentsModel from '@/model/appointment.js'
import PsychologyTeacher from "@/model/psychology-teacher";

const schedule = reactive({
  psychologist_id: '',
  appointment_date: '',
  start_time: '',
  end_time: '',
  address: '',
  repeat: '', // 新增字段
  repeatFrequency: '', // 新增字段
  endDate: '', // 新增字段
})

const psychologists = ref({data: []})

onMounted(async () => {
  // Fetch psychologists from your API and assign to `psychologists` ref

  psychologists.value = await PsychologyTeacher.getPsychologyTeachers({size:999})
})

const submitForm = async () => {
  // Validate form and submit data to your API

  let data = {
    psychologist_id: schedule.psychologist_id,
    appointment_date: new Date(schedule.appointment_date).getTime() / 1000,
    start_time: `${schedule.start_time.getHours().toString().padStart(2, '0')}:${schedule.start_time.getMinutes().toString().padStart(2, '0')}`,
    end_time: `${schedule.end_time.getHours().toString().padStart(2, '0')}:${schedule.end_time.getMinutes().toString().padStart(2, '0')}`,address: schedule.address,
    repeat: schedule.repeat,
    repeatFrequency: schedule.repeatFrequency,
    endDate: new Date(schedule.endDate).getTime() / 1000,
  }


  const res = await AppointmentsModel.addAppointment(data)

  if (res.code === 0) {
    ElMessage.success('Appointment added successfully')
    resetForm()
  } else {
    ElMessage.error('Failed to add appointment')
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
