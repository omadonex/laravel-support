const NotifyService = {
  data() {
    return {
      Data__notify: {
        transNs: 'vendor.support.common',
        types: {
          ERROR: 'error',
          INFO: 'info',
          SUCCESS: 'success',
          WARNING: 'warning',
        },
        positions: {
          BOTTOM_LEFT: "bottom-left",
          BOTTOM_RIGHT: "bottom-right",
          TOP_LEFT: "top-left",
          TOP_RIGHT: "top-right",
        },
      },
    };
  },

  methods: {
    notify__show(text, type, title, isHtml, position) {
      let finalType = type || this.Data__notify.types.SUCCESS;
      this.$notify({
        title: title || this.t(finalType, this.Data__notify.transNs),
        message: text,
        type: finalType,
        dangerouslyUseHTMLString: isHtml,
        position: position || this.Data__notify.positions.BOTTOM_RIGHT,
      });
    },

    notify__showError(text, title, isHtml, position) {
      this.notify__show(text, this.Data__notify.types.ERROR, title, isHtml, position);
    },

    notify__showInfo(text, title, isHtml, position) {
      this.notify__show(text, this.Data__notify.types.INFO, title, isHtml, position);
    },

    notify__showSuccess(text, title, isHtml, position) {
      this.notify__show(text, this.Data__notify.types.SUCCESS, title, isHtml, position);
    },

    notify__showWarning(text, title, isHtml, position) {
      this.notify__show(text, this.Data__notify.types.WARNING, title, isHtml, position);
    },

    notify__process() {
      this.DataLive.notify.forEach((item) => {
        this.notify__show(item.text, item.type, item.title, item.isHtml, item.position);
      });
    },
  },
};

export default {
  install(Vue, options) {
    Vue.mixin(NotifyService);
  },
};

