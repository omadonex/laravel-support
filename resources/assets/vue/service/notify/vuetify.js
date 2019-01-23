export default {
  data() {
    return {
      snackbar: {
        timeout: 5000,
        color: 'success',
        multiLine: false,
        vertical: false,
        value: false,
        text: '',
        position: {
          x: 'right',
          y: 'bottom',
        },
      }
    };
  },

  methods: {
    showSnackbar(text, color) {
      this.snackbar.text = text;
      this.snackbar.color = color;

      const sideHor = (this.snackbar.position.x === 'right');
      const sideVer = (this.snackbar.position.y === 'bottom');
      this.snackbar.right = sideHor;
      this.snackbar.left = !sideHor;
      this.snackbar.bottom = sideVer;
      this.snackbar.top = !sideVer;
      this.snackbar.value = true;
    },

    showSnackbarSuccess(text) {
      this.showSnackbar(text, 'success');
    },

    showSnackbarInfo(text) {
      this.showSnackbar(text, 'info');
    },

    showSnackbarError(text) {
      this.showSnackbar(text, 'error');
    },
  },
};
