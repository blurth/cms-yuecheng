<template>
<!--  <div>
    <iframe :src="iframeSrc" style="width:100%; height:1080px;"></iframe>
  </div>-->

  <div class="welcome-container">
    <h1>欢迎登录</h1>
  </div>
</template>


<script>
import { computed } from 'vue';
import { useStore } from 'vuex';

export default {
  setup() {
    const store = useStore();

    const iframeSrc = computed(() => {
      // 从Vuex获取school_id
      const schoolId = store.state.user.school_id;
      console.log(schoolId);
      return `https://dashboard.jixiangjiaoyu.com/?school_id=${schoolId}`;
    });

    return {
      iframeSrc,
    };
  },
}

</script>

<style scoped lang="scss">
/* Your style here */
  .welcome-container {
    text-align: center; /* 使h1中的文字居中 */
    padding: 20px; /* 添加一些内边距 */
  }

  .welcome-container h1 {
    font-size: 48px; /* 设置文字大小为48px */
    margin: 0; /* 移除默认的外边距 */
  }
</style>
