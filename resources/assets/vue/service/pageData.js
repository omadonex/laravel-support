const PageDataService = {
  methods: {
    getPageData(routeName) {
      const finalRouteName = routeName || this.$root.$route.name;
      const camelName = this.$root.getCamelName(finalRouteName);

      if (camelName in this.$root.DataPage) {
        return this.$root.DataPage[camelName];
      }

      return this.$root.DataPage['default'];
    },
  },
};

export default {
  install(Vue, options) {
    Vue.mixin(PageDataService);
  },
};
