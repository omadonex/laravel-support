import TranslateMixin from '../../../../mixins/translate';

export default {
  mixins: [TranslateMixin],

  data() {
    return {
      translate__ns: {
        default: 'vendor.support.components.confirm',
      },
    };
  },

  props: {
    confirmTitle: { type: String, default: null },
    confirmText: { type: String, default: null },
    btnConfirmText: { type: String, default: null },
    btnCancelText: { type: String, default: null },
    type: { type: String, default: 'warning' },
  },

  computed: {
    finalConfirmTitle() {
      return this.confirmTitle || this.t('confirmTitle');
    },

    finalConfirmText() {
      return this.confirmText || this.t('confirmText');
    },

    finalBtnConfirmText() {
      return this.btnConfirmText || this.t('confirm', 'omxCommon');
    },

    finalBtnCancelText() {
      return this.btnCancelText || this.t('cancel', 'omxCommon');
    },
  },

  methods: {
    confirmRequest() {
      this.$confirm(
        this.finalConfirmText,
        this.finalConfirmTitle,
        {
          confirmButtonText: this.finalBtnConfirmText,
          cancelButtonText: this.finalBtnCancelText,
          type: this.type,
        }
      )
        .then(() => { this.$emit('confirm'); })
        .catch(() => {});
    },
  },
};