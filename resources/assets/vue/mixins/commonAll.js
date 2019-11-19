export default {
  data() {
    return {
      eventBus: omx.global.utils.EventBus,
    }
  },

  methods: {
    getThis() {
      return this;
    },
  },
};
