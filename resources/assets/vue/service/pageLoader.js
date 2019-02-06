const PageLoaderService = {
  data() {
    return {
      Data__pageLoader: {
        config: {
          store: false,
        },
      },
    };
  },

  methods: {
    pageLoader__useStore() {
      this.Data__pageLoader.config.store = true;
    },
  },
};

export default {
  install(Vue, options) {
    Vue.mixin(PageLoaderService);
  },
};
