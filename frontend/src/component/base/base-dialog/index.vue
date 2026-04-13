<template>
  <div class="dialog-box" :class="title.includes('详情') || title.includes('Details') ? 'detail-dialog-box' : ''">
    <el-dialog
      :title="title"
      v-model="dialogVisible"
      :before-close="handleClickBtnCancel"
      :show-close="showClose"
      :draggable="draggable"
      :close-on-click-modal="false"
      :append-to-body="false"
      :width="dialogWidth"
      :top="dialogTop"
      :fullscreen="fullscreen"
    >
      <div class="dialog-body-box" ref="dialogBody">
        <slot></slot>
      </div>
      <template #footer>
        <div class="dialog-footer text-right">
          <!-- 自定义按钮 -->
          <slot name="custom-btn"></slot>
          <el-button class="btn-cancel btn-common" size="small" @click="handleClickBtnCancel" v-if="showCancel"
            >取消</el-button
          >
          <el-button
            class="btn-sure ml-4"
            type="default"
            size="small"
            @click="handleClickBtnSure"
            v-if="showSure && !(title.includes('详情') || title.includes('Details'))"
            >{{ btnSure || '确认' }}</el-button
          >
        </div>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import { ElMessageBox } from 'element-plus'
import debounce from '@/utils/debounce'
import { computed, watch, defineComponent, getCurrentInstance } from 'vue'

export default defineComponent({
  name: 'BaseDialog', // 基础弹框
  props: {
    isVisible: {
      type: Boolean,
      default: false,
    },
    title: {
      type: String,
      default: '',
    },
    btnSure: {
      type: String,
      default: '',
    },
    btnCancel: {
      type: String,
      default: '',
    },
    dialogTop: {
      type: String,
      default: '15vh',
    },
    dialogWidth: {
      type: String,
      default: '800px',
    },
    showCancel: {
      type: Boolean,
      default: true,
    },
    showSure: {
      type: Boolean,
      default: true,
    },
    showClose: {
      type: Boolean,
      default: true,
    },
    draggable: {
      type: Boolean,
      default: false,
    },
    fullscreen: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['handleClickBtnSure', 'handleClickBtnCancel', 'click-btn-sure', 'click-btn-cancel'],
  // emits: {
  //     handleClickBtnSure: null,
  //     handleClickBtnCancel: null,
  // },
  components: {
    ElMessageBox,
  },
  setup(props, { emit }) {
    const proxy = getCurrentInstance()
    // 注意此时需要单独定义
    const dialogVisible = computed(() => props.isVisible)

    watch(
      () => props.dialogVisible,
      val => {
        val && scrollTop()
      },
    )

    // 弹框滚动到顶部
    const scrollTop = () => {
      proxy.$nextTick(() => {
        const { dialogBody } = proxy.$refs
        dialogBody.scrollTop = 0
      })
    }

    // 确认按钮点击处理
    const handleClickBtnSure = debounce(() => {
      emit('click-btn-sure')
    }, 2000)

    // 取消按钮点击处理
    const handleClickBtnCancel = () => {
      emit('click-btn-cancel')
    }

    return {
      dialogVisible,
      handleClickBtnSure,
      handleClickBtnCancel,
    }
  },
})
</script>
<style lang="scss">
@import '@/assets/css/global.scss';

.dialog-box {
  // color: $app-main-font-color;

  /* 详情弹框body中的所有元素禁止鼠标事件 */
  &.detail-dialog-box {
    .el-dialog__body {
      form {
        * {
          pointer-events: none;
        }

        .document-pdf {
          background-color: #f8f8f8;
        }

        .el-input__wrapper {
          background-color: #f8f8f8 !important;
        }

        .el-button.is-disabled {
          .svg-icon use {
            fill: #ccc;
          }
        }
      }

      .allow {
        pointer-events: all;
      }

      .file-list {
        margin-top: -25px;
      }
    }
  }

  .el-dialog {
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    margin: 0 !important;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    max-height: calc(100% - 30px);
    max-width: calc(100% - 30px);
  }

  .el-dialog__body {
    background: #fff;
    color: $app-main-font-color;
    font-weight: normal;
    padding: 0 !important;
    // max-height: 450px;
    overflow: hidden;
    width: 100%;
    flex: 1;

    .dialog-body-box {
      overflow: auto;
      max-height: 425px;
      padding: 20px 24px;
      font-size: 12px;
    }
  }

  .el-dialog__header,
  .el-dialog__footer {
    background: #fff;

    .el-dialog__close {
      color: #999;
      font-size: 22px;

      &:hover {
        color: $app-theme-color;
      }
    }
  }

  .el-dialog__headerbtn {
    top: 0;
    width: 56px;
    height: 56px;
    padding-right: 4px;
  }

  .el-dialog__header {
    border-bottom: 1px solid $app-border-color;
    margin: 0;
    height: 56px;

    .el-dialog__title {
      display: inline-block;
      // color: $app-main-font-color;
      font-size: 16px;
      font-weight: bold;
      margin: -4px 0 0 4px;
    }
  }

  .el-dialog__footer {
    padding: 20px 24px;
    border-top: 1px solid $app-border-color;

    .el-button {
      width: 80px;
      height: 28px;
      font-size: 12px;
      border-radius: 2px;

      &.btn-sure {
        border-color: transparent !important;
      }

      &.btn-cancel {
        color: #444 !important;
        border-color: #d8dce6;

        &:hover {
          background: #fff;
          border: 1px solid $app-theme-color;
          color: $app-theme-color;
        }
      }
    }
  }
}
</style>
