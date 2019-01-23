<template>
    <icon-button :disabled="disabled"
                 :icon="icon"
                 :iconSize="iconSize"
                 :iconColor="iconColor"
                 :iconTitle="iconTitle"
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
      icon: { type: String, required: true },
      iconColor: { type: String, default: null },
      iconSize: { type: String, default: 'icn--normal' },
      iconTitle: { type: String, default: null },
      disabled: { type: Boolean, default: false },
    },
  };
</script>
