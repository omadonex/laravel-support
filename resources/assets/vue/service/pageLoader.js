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

    pageLoader__initStoreData(data) {
      for (let prop in data) {
        this.$store.commit('page/pl__updateData', {
          prop: prop,
          data: data[prop],
        });
      }
    },
  },
};

export default {
  install(Vue, options) {
    Vue.mixin(PageLoaderService);
  },
};
