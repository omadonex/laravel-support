<template>
  <el-breadcrumb separator-class="el-icon-arrow-right" class="page__breadcrumb">
    <el-breadcrumb-item v-if="parentEnabled" :to="{ name: parentRouteName }">{{ t('breadcrumb', parentPage) }}</el-breadcrumb-item>
    <el-breadcrumb-item
        v-for="(breadcrumb, index) in $root.$route.meta.breadcrumbs"
        :key="'breadcrumb-' + index"
        :to="{ name: breadcrumb.name }">
      {{ t('breadcrumb', breadcrumb.page) }}
    </el-breadcrumb-item>
    <el-breadcrumb-item>{{ pageData.breadcrumb }}</el-breadcrumb-item>
  </el-breadcrumb>
</template>

<script>
  import TranslateMixin from '../../../../mixins/translate';

  export default {
    name: 'OmxElementBreadcrumbs',
    mixins: [TranslateMixin],

    data() {
      return {
        translate__ns: {
          default: 'app.pages',
        },
      };
    },

    props: {
      pageData: { type: Object, required: true },
      parentEnabled: { type: Boolean, default: true },
      parentModule: { type: String, default: 'app' },
      parentRouteNameBase: { type: String, default: 'index' },
    },

    computed: {
      parentPage() {
        return this.$root.getCamelName(this.parentRouteName);
      },

      parentRouteName() {
        return `${this.parentModule}.${this.parentRouteNameBase}`;
      },
    },

    created() {
      this.$root.$route.meta.breadcrumbs.forEach((breadcrumb) => {
        if (!(breadcrumb in this.translate__ns)) {
          const camelNameBase = this.$root.getCamelName(breadcrumb.nameBase);
          this.translate__ns[breadcrumb.page] = `${breadcrumb.module}.pages.${camelNameBase}`;
        }
      });
      this.translate__ns[this.parentPage] = `${this.parentModule}.pages.${this.parentRouteNameBase}`;
    },
  };
</script>
