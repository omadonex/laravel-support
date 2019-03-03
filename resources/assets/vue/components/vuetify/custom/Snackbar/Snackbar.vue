<template>
  <v-snackbar
      v-model="$root.Data__notify.snackbar.value"
      :timeout="$root.Data__notify.snackbar.timeout"
      :color="$root.Data__notify.snackbar.color"
      :multi-line="$root.Data__notify.snackbar.multiLine"
      :vertical="$root.Data__notify.snackbar.vertical"
      :right="$root.Data__notify.snackbar.right"
      :left="$root.Data__notify.snackbar.left"
      :top="$root.Data__notify.snackbar.top"
      :bottom="$root.Data__notify.snackbar.bottom"
      auto-height>
    {{ $root.Data__notify.snackbar.text }}
    <v-btn dark flat @click.native="$root.Data__notify.snackbar.value = false">{{ t('close') }}</v-btn>
  </v-snackbar>
</template>

<script>
  import TranslateMixin from '../../../../mixins/translate';

  export default {
    name: 'OmxVuetifySnackbar',
    mixins: [TranslateMixin],

    data() {
      return {
        translate__ns: {
          default: 'vendor.support.common',
        },
      };
    },
  };
</script>
