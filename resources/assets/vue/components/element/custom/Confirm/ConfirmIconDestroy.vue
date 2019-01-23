<template>
  <icon-button :disabled="disabled"
               :icon="icon"
               :iconSize="iconSize"
               :iconColor="iconColor"
               :iconTitle="finalIconTitle"
               @clicked="confirmRequest"></icon-button>
</template>

<script>
  import ConfirmMixin from './confirm';
  import IconButton from '../IconButton/IconButton.vue';

  export default {
    name: 'OmxElementConfirmIconDestroy',
    mixins: [ConfirmMixin],
    components: { IconButton },

    props: {
      icon: { type: String, default: 'mdi mdi-delete' },
      iconColor: { type: String, default: 'color--danger' },
      iconSize: { type: String, default: 'icn--normal' },
      iconTitle: { type: String, default: null },
      disabled: { type: Boolean, default: false },
    },

    computed: {
      finalIconTitle() {
        return this.iconTitle || this.t('delete', 'omxCommon');
      },
    },
  };
</script>
