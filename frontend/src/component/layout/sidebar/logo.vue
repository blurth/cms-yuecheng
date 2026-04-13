<template>
  <div :class="!elMenuCollapse ? 'logo' : 'mobile-logo'">
    <img :src="logoSource" alt="logo" />
  </div>
</template>

<script setup>
import {computed, ref} from 'vue';
import logo from 'assets/image/logo.png';
import mobileLogo from 'assets/image/mobile-logo.png';
import schoolModel from "@/model/school";


const props = defineProps({
  elMenuCollapse: {
    type: Boolean,
    required: true,
  },
});

const logoUrl = ref(''); // 用于存储 logo 地址的响应式变量

// 用于从接口获取 logo 地址
async function fetchLogo() {

    //const res = await schoolModel.getMySchoolLogo();
    logoUrl.value = "https://qn.jixiangjiaoyu.com/2025/9/301aed9f9519c5252aeeefd4050391a7241759199343962.jpg"; // 更新 logo 地址

}

fetchLogo(); // 获取 logo 地址

const logoSource = computed(() => (!props.elMenuCollapse ? logoUrl.value : mobileLogo));

</script>

<style lang="scss" scoped>
.logo {
  width: $sidebar-width;
  height: $header-height;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 30px;
  color: #fff;
  background-color: #122150;
  transition: all 0.3s linear;
  position: sticky;
  top: 0;
  left: 0;
  z-index: 99;

  img {
    width: 70px;
    border-radius: 50%;
  }
}

.mobile-logo {
  width: 64px;
  height: 86px;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #122150;
  transition: all 0.3s linear;

  img {
    width: 40px;
    height: 40px;
  }
}
</style>
