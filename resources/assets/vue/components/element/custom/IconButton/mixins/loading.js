export default {
  props: {
    callingObj: { type: Object, default: null },
    loadingPropName: { type: String, default: null },
    loadingKey: { type: String, default: null },
  },

  computed: {
    disabled() {
      if (!this.callingObj) {
        return false;
      }

      if (!this.loadingPropName) {
        return this.callingObj['loading'];
      }

      if (!this.loadingKey) {
        return this.callingObj[this.loadingPropName];
      }

      return this.callingObj[this.loadingPropName][this.loadingKey];
    },
  },
};
