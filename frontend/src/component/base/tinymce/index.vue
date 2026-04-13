<template>
  <div>
    <editor id="tinymceEditor" :init="tinymceInit" api-key="73usbwsc5nqjftom8gm3fwfib1g29r6bfnvrvyuwdokrvzxo" v-model="content" :key="tinymceFlag"></editor>
  </div>
</template>
<script>
import Editor from '@tinymce/tinymce-vue'
import { post } from '@/lin/plugin/axios'
import dataModel from "@/model/data";

export default {
  name: 'TinymceEditor',
  props: {
    defaultContent: {
      type: String,
      default: '',
    },
    height: {
      type: Number,
      default: 500,
    },
    width: {
      type: Number,
      default: undefined,
    },
    showMenubar: {
      type: Boolean,
      default: true,
    },
    toolbar: {
      type: String,
      default: ` undo redo
      | formatselect
      | bold italic strikethrough forecolor backcolor formatpainter
      | link image | alignleft aligncenter alignright alignjustify
      | numlist bullist outdent indent
      | removeformat
      | preview fullscreen code`,
    },
  },
  components: {
    Editor,
  },
  data() {
    return {
      qiniutoken: null,
      content: '',
      tinymceFlag: 1,
      tinymceInit: {},
    }
  },
  created() {

    const getQiniuToken = async () => {
      this.qiniutoken = await dataModel.getQiniuToken()
    }

    getQiniuToken()



    this.tinymceInit = {
      language: 'zh_CN',
      height: this.height,
      branding: true, // 去水印
      statusbar: false, // 隐藏编辑器底部的状态栏
      elementpath: false, // 禁用编辑器底部的状态栏
      toolbar: this.toolbar,
      paste_data_images: true, // 允许粘贴图像
      browser_spellcheck: true, // 拼写检查
      menubar: this.showMenubar, // 隐藏最上方menu
      plugins: `print fullpage searchreplace autolink directionality visualblocks
        visualchars template codesample charmap hr pagebreak nonbreaking anchor toc insertdatetime
        wordcount textpattern help advlist table lists paste preview fullscreen image imagetools code link`,
      async images_upload_handler(blobInfo, success, failure) {
        const file = new File([blobInfo.blob()], blobInfo.filename(), {
          type: 'image/*',
        })
        const formData = new FormData()
        formData.append('file', file)
        formData.append('token', localStorage.getItem('qiniuToken')) // 添加七牛云token

        // 发送请求到七牛云的上传URL
        fetch('https://up-z0.qiniup.com', {
          method: 'POST',
          body: formData,
        })
            .then(response => response.json())
            .then(data => {
              if (data.key) {
                // 七牛云返回的文件名在 key 字段
                success('https://qn.jixiangjiaoyu.com/' + data.key)
              } else {
                failure('Upload to Qiniu cloud failed')
              }
            })
            .catch(err => failure(err))
      },
    }
  },
  mounted() {
    if (this.defaultContent) {
      this.content = this.defaultContent
    }
  },
  watch: {
    content: {
      handler() {
        this.$emit('change', this.content)
      },
    },
    defaultContent: {
      handler() {
        this.content = this.defaultContent
      },
      immediate: true,
    },
  },
  activated() {
    this.tinymceFlag++
  },
}
</script>

<style lang="scss">
.tox-notification {
  display: none !important;
}
</style>
