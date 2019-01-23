<template>
  <div style="height: 100%; position: relative;">
    <slot name="loader" :loading="!value">
      <div v-if="!value" class="page-loading" v-loading="!value"></div>
    </slot>
    <template v-if="value">
      <breadcrumbs :pageData="$root.getPageData()"></breadcrumbs>
      <div class="header header--h1">{{ $root.getPageData().header }}</div>
      <slot></slot>
    </template>
  </div>
</template>

<script>
  import Breadcrumbs from "../Breadcrumbs/Breadcrumbs.vue";

  export default {
    name: 'OmxElementPage',
    components: { Breadcrumbs },

    data() {
      return {
        pageData: null,
      }
    },

    props: {
      value: { type: Boolean, required: true },
    },
  };
</script>

<style lang="scss">
  .page-loading {
    height: 100%;
    .el-loading-mask {
      background-color: unset;
    }
  }
</style>
