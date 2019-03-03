const AxiosService = {
  mounted() {
    if (this.$root.DataUser.apiToken) {
      this.$root.Data__smartAjax.axios.defaults.headers.common.Authorization = `Bearer ${this.$root.DataUser.apiToken}`;
    }
    if (this.$root.DataLang.currLang) {
      this.$root.Data__smartAjax.axios.defaults.headers.common.Lang = this.$root.DataLang.currLang;
    }
  },
};

export default {
  install(Vue, options) {
    Vue.mixin(AxiosService);
  },
};
