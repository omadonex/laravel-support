<template>
  <el-button :class="{ 'submit-wide': wide }" type="primary" :native-type="nativeType" @click.stop.prevent="confirmRequest">
    <slot></slot>
  </el-button>
</template>

<script>
  import ConfirmMixin from './confirm';

  export default {
    name: 'OmxElementConfirmButton',
    mixins: [ConfirmMixin],

    props: {
      nativeType: { type: String, default: null },
      wide: { type: Boolean, default: false },
    },
  };
</script>

<style lang="scss" scoped>
  .submit-wide {
    width: 100%;
  }
</style>
