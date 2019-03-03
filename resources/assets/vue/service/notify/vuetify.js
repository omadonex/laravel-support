const NotifyService = {
  data() {
    return {
      Data__notify: {
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
        snackbar: {
          timeout: 5000,
          color: 'success',
          multiLine: false,
          vertical: false,
          value: false,
          text: '',
          right: true,
          left: false,
          top: false,
          bottom: true,
        },
      },
    };
  },

  methods: {
    notify__show(text, type, title, isHtml, position) {
      this.Data__notify.snackbar.text = text;
      this.Data__notify.snackbar.color = type || this.Data__notify.types.SUCCESS;
      if (position) {
        const sideHor = (position === this.Data__notify.positions.BOTTOM_RIGHT) || (position === this.Data__notify.positions.TOP_RIGHT);
        const sideVer = (position === this.Data__notify.positions.BOTTOM_RIGHT) || (position === this.Data__notify.positions.BOTTOM_LEFT);
        this.Data__notify.snackbar.right = sideHor;
        this.Data__notify.snackbar.left = !sideHor;
        this.Data__notify.snackbar.bottom = sideVer;
        this.Data__notify.snackbar.top = !sideVer;
      }
      this.Data__notify.snackbar.value = true;
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