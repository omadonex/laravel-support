<template>
  <confirm-icon-destroy
      class="fl--right"
      :confirmText="finalConfirmText"
      :disabled="disabled"
      @confirm="deleteModel()"></confirm-icon-destroy>
</template>

<script>
  import IconButtonLoadingMixin from '../IconButton/mixins/loading';
  import TranslateMixin from '../../../../mixins/translate';

  import ConfirmIconDestroy from './ConfirmIconDestroy.vue';

  export default {
    name: 'OmxElementConfirmIconDestroyModel',
    mixins: [IconButtonLoadingMixin, TranslateMixin],
    components: { ConfirmIconDestroy },

    data() {
      return {
        translate__ns: {
          default: 'vendor.support.components.confirm',
        },
      };
    },
    
    props: {
      model: { type: Object, required: true },
      modelList: { type: Array, required: true },
      routePart: { type: String, required: true },
      confirmText: { type: String, default: null },
      messageText: { type: String, default: null },
    },

    computed: {
      finalConfirmText() {
        return this.confirmText || this.t('confirmTextDestroy');
      },

      finalMessageText() {
        return this.messageText || this.t('message.destroy', 'omxCommon');
      },
    },

    methods: {
      deleteModel() {
        const modelIndex = this.$root.getIndex(this.modelList, this.model.id);
        if (modelIndex === -1) {
          this.$root.notify__showError();
          return false;
        }

        this.$root.smartAjax__call({
          callingObject: this.callingObj || this,
          method: 'delete',
          url: this.$root.route(`${this.routePart}.destroy`, this.model.id, false).url(),
          loadingPropName: this.loadingPropName,
          loadingKey: this.loadingKey,
        })
          .then((result) => {
            if (result) {
              this.$root.notify__showInfo(this.finalMessageText);
              this.modelList.splice(modelIndex, 1);
              this.$emit('destroyed');
            }
          });
      }
    }
  };
</script>
