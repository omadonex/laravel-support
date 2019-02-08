export default {
  data() {
    return {
      eventBus: window.EventBus,
    }
  },

  methods: {
    getThis() {
      return this;
    },
  },
};
