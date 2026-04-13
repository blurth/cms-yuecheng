<script setup>
import { ref, reactive, watch } from "vue";
import { Loading } from "@element-plus/icons-vue";
import ExcelJS from "exceljs";
import {ElMessage, ElMessageBox, ElNotification} from "element-plus";
import { getToken } from "lin/util/token";
import Appointment from "@/model/appointment";
import PsychologyTeacher from "@/model/psychology-teacher";

const dialogVisible = ref(false);
const surveyDialogVisible = ref(false);
const surveyViewDialogVisible = ref(false);
const currentSurveyData = ref(null);
const pageCount = ref(10);
const data = ref(null);
const notifyT = ref(null);
const teacherOptions = ref([]);
const userform = ref({});
const currentAppointmentId = ref(null);

const statusList = ref([
  {
    id: 1,
    title: "已预约",
  },
  {
    id: 2,
    title: "未预约",
  },
]);

const listQuery = ref({
  page: 1,
  size: 10,
  name: "",
  psychologist_id: null,
  start_time: "",
  end_time: "",
});

const dialogForm = reactive({
  id: "",
  name: "",
  student_number: "",
  audit: "",
});

const surveyForm = reactive({
  name: "",
  gender: "",
  identity: "",
  schoolClass: "",
  source: "",
  topics: [],
  symptoms: "",
  goals: "",
  judgment: "",
  next: "",
  nextTime: "",
});

const loading = ref(false);
const list = ref([]);
const totalNum = ref(0);
const activeName = ref("first");
const dateValue = ref([]);

watch(
    () => dateValue.value,
    (val) => {
      if (val) {
        listQuery.value.start_time = val[0];
        listQuery.value.end_time = val[1];
      } else {
        listQuery.value.start_time = "";
        listQuery.value.end_time = "";
      }
    }
);

const getData = async () => {
  loading.value = true;
  const res = await Appointment.getList(listQuery.value);
  if (res) {
    totalNum.value = res.total;
    list.value = res.data;
  } else {
    console.error("Error: Response from Appointment.getList is undefined");
  }
  loading.value = false;
};

const getTeacher = async () => {
  const response = await PsychologyTeacher.getPsychologyTeachers();
  teacherOptions.value = response.data;
};

getTeacher();

const handleBtnExport = async () => {
  try {
    const listQueryCopy = { ...listQuery.value };

    notifyT.value = ElNotification({
      title: "导出文件准备中",
      icon: Loading,
      customClass: "upload-loading-box is-loading",
      duration: 1000,
      offset: 100,
      message: "准备工作正在后台进行，你可以选择进行其他操作。",
    });

    const queryParams = new URLSearchParams(listQueryCopy).toString();

    // 使用fetch来获取Excel文件的URL
    //const response = await studentModel.getExamRecordExcel(listQueryCopy);
    // 检查是否需要手动解析Blob

    const axios = require("axios");

    // 设置axios实例
    const api = axios.create({
      baseURL: "https://yuecheng.jixiangjiaoyu.com/v1/", // 替换为您的API基础URL
      responseType: "blob", // 指定响应类型为Blob
      headers: {
        Authorization: getToken("access_token"), // 添加Authorization标头
      },
    });
    //文件名 用学校名称即可

    const fileName = `预约记录.xlsx`;
    // 调用接口
    api
        .get(`appointment/export_excel?${queryParams}`)
        .then((response) => {
          // 获取Blob数据
          const blob = response.data;

          // 创建Blob URL
          const blobUrl = URL.createObjectURL(blob);

          // 创建一个链接并模拟点击下载
          const link = document.createElement("a");
          link.href = blobUrl;
          link.setAttribute("download", fileName);
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);

          ElNotification({
            title: "导出成功",
            type: "success",
            message: "预约记录已成功下载。",
            duration: 5000,
          });
        })

        .catch((error) => {});

    //关闭notifyT.value
  } catch (error) {
    console.error(error);
  }
};

const handleCurrentChange = async (newPage) => {
  listQuery.value.page = newPage;
  await getData();
};

const loadmore = () => {
  console.log(1);
};

const handleLook = async (row) => {
  userform.value = row.user_form;

  dialogVisible.value = true;
};

//cancelAppointment

const cancelAppointment = async (row) => {
  ElMessageBox.confirm('确定要取消预约吗?', '提示', {
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    type: 'warning',
  }).then(async () => {
    const res = await Appointment.cancelAppointment(row.id);
    if (res.code === 0) {
      ElMessage.success("取消成功");
      await getData();
    } else {
      ElMessage.error(res.message);
    }
  }).catch(() => {
    ElMessage.info('已取消操作');
  });
};

const handleSurvey = (row) => {
  currentAppointmentId.value = row.id;

  console.log('完整的row数据:', row);
  console.log('log_form字段:', row.log_form);

  if (row.log_form && row.log_form !== null) {
    // 如果log_form不为null，显示展示内容的弹窗
    currentSurveyData.value = row.log_form;
    surveyViewDialogVisible.value = true;
  } else {
    // 如果log_form为null，显示填写表单的弹窗
    surveyDialogVisible.value = true;
    Object.keys(surveyForm).forEach(key => {
      if (key === 'topics') {
        surveyForm[key] = [];
      } else {
        surveyForm[key] = "";
      }
    });
  }
};

const submitSurvey = async () => {
  try {
    const axios = require("axios");

    const api = axios.create({
      baseURL: "https://yuecheng.jixiangjiaoyu.com/v1/",
      headers: {
        Authorization: getToken("access_token"),
        'Content-Type': 'application/json',
      },
    });

    const response = await api.post('appointment/saveReport', {
      appointment_id: currentAppointmentId.value,
      form: surveyForm
    });

    if (response.data.code === 0) {
      ElMessage.success('调查表保存成功');
      surveyDialogVisible.value = false;
    } else {
      ElMessage.error(response.data.message || '保存失败');
    }
  } catch (error) {
    ElMessage.error('保存失败');
    console.error(error);
  }
};

const handleDelete = async (row) => {
  ElMessageBox.confirm('确定要删除该预约记录吗？此操作不可恢复', '提示', {
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    type: 'warning',
  }).then(async () => {
    try {
      const res = await Appointment.delete(row.id);
      if (res.code === 0) {
        ElMessage.success('删除成功');
        await getData();
      } else {
        ElMessage.error(res.message || '删除失败');
      }
    } catch (error) {
      ElMessage.error('删除失败');
      console.error(error);
    }
  }).catch(() => {
    ElMessage.info('已取消删除');
  });
};

getData();
</script>

<template>
  <div class="container">
    <div class="header">
      <div class="title">预约记录</div>
      <div class="d-flex aligin-items-center">
        <el-select
            clearable
            v-model="listQuery.psychologist_id"
            class="ml-2"
            placeholder="请选择心理老师"
            v-loadmore="loadmore"
        >
          <el-option
              v-for="(item, index) in teacherOptions"
              :key="index"
              :value="item.id"
              :label="item.name"
          ></el-option>
        </el-select>
        <el-date-picker
            v-model="dateValue"
            class="ml-1"
            type="daterange"
            value-format="YYYY-MM-DD"
            start-placeholder="开始时间"
            end-placeholder="结束时间"
        />
        <el-select
            clearable
            v-model="listQuery.status"
            class="ml-2"
            placeholder="请选择预约状态"
        >
          <el-option
              v-for="(item, index) in statusList"
              :key="index"
              :value="item.id"
              :label="item.title"
          ></el-option>
        </el-select>

        <el-input
            class="search-input ml-2"
            placeholder="请输入学生姓名"
            v-model="listQuery.name"
        ></el-input>

        <el-button class="ml-2" type="primary" @click="getData">查询</el-button>
      </div>
    </div>
    <div class="my-2">
      <el-button type="primary" @click="handleBtnExport">导出</el-button>
    </div>
    <el-table stripe :data="list" v-loading="loading">
      <el-table-column align="center" label="预约详情">
        <template #default="scope">
          <div>
            预约时间: {{ scope.row.appointment_date }}
            {{ scope.row.start_time }}--{{ scope.row.end_time }}
          </div>
        </template>
      </el-table-column>

      <el-table-column
          align="center"
          prop="psychologist_name"
          label="心理老师"
      ></el-table-column>

      <el-table-column
          align="center"
          prop="address"
          label="预约地址"
      ></el-table-column>

      <el-table-column align="center" label="预约人">
        <template #default="scope">
          <el-tag v-if="scope.row.user_id" type="success">{{ scope.row.user_form?.name }}</el-tag>
          <el-tag v-else type="info">未预约</el-tag>
        </template>
      </el-table-column>

      <el-table-column align="center" label="预约电话">
        <template #default="scope">
          <el-tag v-if="scope.row.user_id" type="success">{{ scope.row.user_form?.phone }}</el-tag>
          <el-tag v-else type="info">未预约</el-tag>
        </template>
      </el-table-column>

        <el-table-column align="center" label="操作" fixed="right" width="275">
        <template #default="scope">
          <el-button
            plain
            size="small"
            type="primary"
            @click="handleLook(scope.row)"
            >查看预约信息</el-button>

        <el-button
          plain
          size="small"
          type="primary"
          @click="cancelAppointment(scope.row)"
          :disabled="new Date(scope.row.appointment_date) < new Date()"
        >取消预约</el-button>

        <el-button
          plain
          size="small"
          type="success"
          @click="handleSurvey(scope.row)"
        >{{ scope.row.log_form ? '查看记录表' : '填写记录表' }}</el-button>

        <el-button
          plain
          size="small"
          type="danger"
          @click="handleDelete(scope.row)"
        >删除</el-button>
        </template>


      </el-table-column>



    </el-table>

    <!-- 分页 -->
    <div class="pagination">
      <el-pagination
          :total="totalNum"
          :background="true"
          :page-size="pageCount"
          :current-page="listQuery.page"
          layout="total,prev, pager, next, jumper"
          @current-change="handleCurrentChange"
      >
      </el-pagination>
    </div>

    <el-dialog v-model="dialogVisible" title="查看" width="50%">
  <el-tabs v-model="activeName" class="demo-tabs" @tab-click="handleClick">
    <el-tab-pane label="预约信息" name="first">
      <div class="d-flex justify-content-between">
        <div class="info-box" v-if="userform">
          <div>
            <p class="title">基本信息</p>
          </div>
          <div>
            <el-descriptions :column="2" border>
              <el-descriptions-item label="姓名" v-if="userform.name">
                {{ userform.name }}
              </el-descriptions-item>
              <el-descriptions-item label="性别" v-if="userform.gender">
                {{ userform.gender }}
              </el-descriptions-item>
              <el-descriptions-item label="身份" v-if="userform.identity">
                {{ userform.identity }}
              </el-descriptions-item>
              <el-descriptions-item label="年龄范围" v-if="userform.ageRange">
                {{ userform.ageRange }}
              </el-descriptions-item>
              <el-descriptions-item label="电话" v-if="userform.phone">
                {{ userform.phone }}
              </el-descriptions-item>
              <el-descriptions-item label="地区" v-if="userform.town">
                {{ userform.town }}
              </el-descriptions-item>
              <el-descriptions-item label="学校和班级" v-if="userform.schoolClass">
                {{ userform.schoolClass }}
              </el-descriptions-item>
              <el-descriptions-item label="陪同情况" v-if="userform.companion">
                {{ userform.companion }}
              </el-descriptions-item>
              <el-descriptions-item label="咨询来历" v-if="userform.source">
                {{ userform.source }}
              </el-descriptions-item>
              <el-descriptions-item label="问题类型" v-if="userform.troubleType">
                {{ userform.troubleType }}
              </el-descriptions-item>
              <el-descriptions-item label="是否诊断" v-if="userform.diagnosis">
                {{ userform.diagnosis }}
              </el-descriptions-item>
              <el-descriptions-item label="主要议题" v-if="userform.topics && userform.topics.length > 0">
                {{ userform.topics.join(', ') }}
              </el-descriptions-item>
              <el-descriptions-item label="描述/问题详情" v-if="userform.description" :span="2">
                {{ userform.description }}
              </el-descriptions-item>
              <el-descriptions-item label="症状" v-if="userform.symptoms" :span="2">
                {{ userform.symptoms }}
              </el-descriptions-item>
              <el-descriptions-item label="咨询目标与过程" v-if="userform.goals" :span="2">
                {{ userform.goals }}
              </el-descriptions-item>
              <el-descriptions-item label="问题判断与转介" v-if="userform.judgment" :span="2">
                {{ userform.judgment }}
              </el-descriptions-item>
              <el-descriptions-item label="是否预约下一次" v-if="userform.next">
                {{ userform.next }}
              </el-descriptions-item>
              <el-descriptions-item label="预约时间" v-if="userform.nextTime">
                {{ userform.nextTime }}
              </el-descriptions-item>
            </el-descriptions>
          </div>
        </div>
      </div>
    </el-tab-pane>
  </el-tabs>
</el-dialog>

<!-- 调查表弹窗 -->
<el-dialog v-model="surveyDialogVisible" title="个案记录表" width="60%" :before-close="() => surveyDialogVisible = false">
  <el-form :model="surveyForm" label-width="120px" label-position="left">
    <!-- 来访者姓名 -->
    <el-form-item label="姓名" required>
      <el-input v-model="surveyForm.name" placeholder="请输入姓名" />
    </el-form-item>

    <!-- 性别 -->
    <el-form-item label="性别" required>
      <el-radio-group v-model="surveyForm.gender">
        <el-radio label="男">男</el-radio>
        <el-radio label="女">女</el-radio>
      </el-radio-group>
    </el-form-item>

    <!-- 身份 -->
    <el-form-item label="身份">
      <el-input v-model="surveyForm.identity" placeholder="请输入身份" />
    </el-form-item>

    <!-- 学校和班级 -->
    <el-form-item label="学校和班级">
      <el-input v-model="surveyForm.schoolClass" placeholder="请输入学校和班级" />
    </el-form-item>

    <!-- 咨询来历 -->
    <el-form-item label="咨询来历">
      <el-radio-group v-model="surveyForm.source">
        <el-radio label="本人主动">本人主动</el-radio>
        <el-radio label="他人建议">他人建议</el-radio>
        <el-radio label="其他情况">其他情况</el-radio>
      </el-radio-group>
    </el-form-item>

    <!-- 主要议题（多选） -->
    <el-form-item label="主要议题">
      <el-checkbox-group v-model="surveyForm.topics">
        <el-checkbox label="学习问题">学习问题</el-checkbox>
        <el-checkbox label="人际关系">人际关系</el-checkbox>
        <el-checkbox label="情绪问题">情绪问题</el-checkbox>
        <el-checkbox label="家庭问题">家庭问题</el-checkbox>
        <el-checkbox label="恋爱问题">恋爱问题</el-checkbox>
        <el-checkbox label="职业规划">职业规划</el-checkbox>
        <el-checkbox label="性格完善">性格完善</el-checkbox>
        <el-checkbox label="其他">其他</el-checkbox>
      </el-checkbox-group>
    </el-form-item>

    <!-- 症状 -->
    <el-form-item label="症状">
      <el-input
        v-model="surveyForm.symptoms"
        type="textarea"
        :rows="3"
        placeholder="请输入内容"
      />
    </el-form-item>

    <!-- 咨询目标与过程 -->
    <el-form-item label="咨询目标与过程">
      <el-input
        v-model="surveyForm.goals"
        type="textarea"
        :rows="3"
        placeholder="请输入内容"
      />
    </el-form-item>

    <!-- 问题判断与转介 -->
    <el-form-item label="问题判断与转介">
      <el-input
        v-model="surveyForm.judgment"
        type="textarea"
        :rows="3"
        placeholder="请输入内容"
      />
    </el-form-item>

    <!-- 是否预约下一次 -->
    <el-form-item label="是否预约下一次">
      <el-radio-group v-model="surveyForm.next">
        <el-radio label="是">是</el-radio>
        <el-radio label="否">否</el-radio>
      </el-radio-group>
    </el-form-item>

    <el-form-item label="预约时间">
      <el-input v-model="surveyForm.nextTime" placeholder="请输入预约时间" />
    </el-form-item>
  </el-form>

  <template #footer>
    <div class="dialog-footer">
      <el-button @click="surveyDialogVisible = false">取消</el-button>
      <el-button type="primary" @click="submitSurvey">确认保存</el-button>
    </div>
  </template>
</el-dialog>

<!-- 展示user_form内容的弹窗 -->
<el-dialog v-model="surveyViewDialogVisible" title="记录表详情" width="60%" :before-close="() => surveyViewDialogVisible = false">
  <div v-if="currentSurveyData" class="survey-display-form">
    <el-descriptions :column="2" border>
      <el-descriptions-item label="姓名" v-if="currentSurveyData.name">
        {{ currentSurveyData.name }}
      </el-descriptions-item>
      <el-descriptions-item label="性别" v-if="currentSurveyData.gender">
        {{ currentSurveyData.gender }}
      </el-descriptions-item>
      <el-descriptions-item label="身份" v-if="currentSurveyData.identity">
        {{ currentSurveyData.identity }}
      </el-descriptions-item>
      <el-descriptions-item label="学校和班级" v-if="currentSurveyData.schoolClass">
        {{ currentSurveyData.schoolClass }}
      </el-descriptions-item>
      <el-descriptions-item label="咨询来历" v-if="currentSurveyData.source">
        {{ currentSurveyData.source }}
      </el-descriptions-item>
      <el-descriptions-item label="主要议题" v-if="currentSurveyData.topics && currentSurveyData.topics.length > 0">
        {{ currentSurveyData.topics.join(', ') }}
      </el-descriptions-item>
      <el-descriptions-item label="症状" v-if="currentSurveyData.symptoms" :span="2">
        {{ currentSurveyData.symptoms }}
      </el-descriptions-item>
      <el-descriptions-item label="咨询目标与过程" v-if="currentSurveyData.goals" :span="2">
        {{ currentSurveyData.goals }}
      </el-descriptions-item>
      <el-descriptions-item label="问题判断与转介" v-if="currentSurveyData.judgment" :span="2">
        {{ currentSurveyData.judgment }}
      </el-descriptions-item>
      <el-descriptions-item label="是否预约下一次" v-if="currentSurveyData.next">
        {{ currentSurveyData.next }}
      </el-descriptions-item>
      <el-descriptions-item label="预约时间" v-if="currentSurveyData.nextTime">
        {{ currentSurveyData.nextTime }}
      </el-descriptions-item>
    </el-descriptions>
  </div>

  <template #footer>
    <div class="dialog-footer">
      <el-button @click="surveyViewDialogVisible = false">关闭</el-button>
    </div>
  </template>
</el-dialog>
  </div>
</template>

<style lang="scss">
.info-box {
  box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.1);
  width: 60%;
  padding: 5px 10px;
  border-radius: 5px;
  margin: 10px 0;

  .title {
    font-weight: bold;
  }

  .ul-box {
    margin-top: 10px;
    li {
      padding: 8px 0;
      font-size: 12px;
    }
  }
}

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

  .search-input {
    width: 200px;
  }

  .el-select {
    width: 150px;
  }
}
.question-ul-box {
  margin-top: 30px;
  li {
    p:nth-child(1) {
      margin-top: 10px;
    }
    p:nth-child(2) {
      margin-top: 5px;
    }
    padding-bottom: 10px;
    margin-bottom: 5px;
    border-bottom: 1px solid #000;
  }
}
.el-table {
  height: calc(100vh - 270px);
}

.pagination {
  display: flex;
  justify-content: flex-end;
  margin: 20px;
}

.survey-display-form {
  padding: 20px 0;
}
</style>
