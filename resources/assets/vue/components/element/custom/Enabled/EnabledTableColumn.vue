<template>
  <el-table-column prop="enabled" :label="t('enabled', 'omxCommon')" :width="width" sortable align="center">
    <template slot-scope="scope">
      <i :class="[icon, iconSize, iconColor]" :title="t('enabled', 'omxCommon')" v-if="scope.row.enabled"></i>
    </template>
  </el-table-column>
</template>

<script>
  import TranslateMixin from '../../../../mixins/translate';

  export default {
    name: 'OmxElementEnabledTableColumn',
    mixins: [TranslateMixin],

    props: {
      icon: { type: String, default: 'mdi mdi-check' },
      iconSize: { type: String, default: 'icn--normal' },
      iconColor: { type: String, default: 'color--success' },
      width: { type: Number, default: 120 },
    },
  };
</script>
