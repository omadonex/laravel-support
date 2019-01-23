<template>
  <icon-button :icon="icon"
               :iconSize="iconSize"
               :iconColor="iconColor"
               :iconTitle="t('edit', 'omxCommon')"
               @clicked="$emit('clicked')"></icon-button>
</template>

<script>
  import TranslateMixin from '../../../../mixins/translate';
  import IconButton from '../IconButton/IconButton.vue';

  export default {
    name: 'OmxElementEditIcon',
    mixins: [TranslateMixin],
    components: { IconButton },

    props: {
      icon: { type: String, default: 'mdi mdi-square-edit-outline' },
      iconSize: { type: String, default: 'icn--normal' },
      iconColor: { type: String, default: null },
    },
  };
</script>
