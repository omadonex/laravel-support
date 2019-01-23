const TranslateService = {
  methods: {
    t(key, ns) {
      return this.getProp(this.DataLang.translations, `${this.DataLang.currLang}.${ns}.${key}`);
    },
  },
};

export default {
  install(Vue, options) {
    Vue.mixin(TranslateService);
  },
};
