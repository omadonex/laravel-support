export default {
  methods: {
    getThis() {
      return this;
    },

    getEventBus() {
      return window.EventBus;
    }
  },
};
