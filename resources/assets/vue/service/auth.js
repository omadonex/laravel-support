const AuthService = {
  computed: {
    auth__loggedIn() {
      return this.appLoggedIn;
    },

    auth__needActivation() {
      return this.auth__loggedIn && !this.DataUser.activated;
    },
  },
};

export default {
  install(Vue, options) {
    Vue.mixin(AuthService);
  },
};
