<template>
  <icon-button :disabled="disabled"
               :icon="icon"
               :iconSize="iconSize"
               :iconColor="getIconColor(scope.row.enabled)"
               :iconTitle="scope.row.enabled ? t('disable', 'omxCommon') : t('enable', 'omxCommon')"
               @clicked="toggleEnabled(scope.row)"></icon-button>
</template>

<script>
  import IconButtonLoadingMixin from '../IconButton/mixins/loading';
  import TranslateMixin from '../../../../mixins/translate';
  import IconButton from '../IconButton/IconButton.vue';

  export default {
    name: 'OmxElementEnabledIcon',
    mixins: [IconButtonLoadingMixin, TranslateMixin],
    components: { IconButton },

    props: {
      scope: { type: Object, required: true },
      routePart: { type: String, required: true },
      icon: { type: String, default: 'mdi mdi-power' },
      iconSize: { type: String, default: 'icn--normal' },
      iconColorEnable: { type: String, default: 'color--success' },
      iconColorDisable: { type: String, default: 'color--warning' },
      messageEnabled: { type: String, default: null },
      messageDisabled: { type: String, default: null },
    },

    computed: {
      finalMessageEnabled() {
        return this.messageEnabled || this.t('message.enabled', 'omxCommon');
      },

      finalMessageDisabled() {
        return this.messageDisabled || this.t('message.disabled', 'omxCommon');
      },
    },

    methods: {
      getIconColor(enabled) {
        return enabled ? this.iconColorDisable : this.iconColorEnable;
      },

      toggleEnabled(row) {
        const prevValue = row.enabled;
        row.enabled = !row.enabled;
        const route = row.enabled ? `${this.routePart}.enable` : `${this.routePart}.disable`;
        const message = row.enabled ? this.finalMessageEnabled: this.finalMessageDisabled;

        this.$root.smartAjax__call({
          callingObject: this.callingObj || this,
          method: 'post',
          url: this.$root.route(route, row.id, false).url(),
          loadingPropName: this.loadingPropName,
          loadingKey: this.loadingKey,
        })
          .then((result) => {
            if (result) {
              this.$root.notify__showSuccess(message);
            } else {
              row.enabled = prevValue;
            }
          })
          .catch((error) => {
            row.enabled = prevValue;
          });

        this.$emit('clicked');
      },
    },
  };
</script>
