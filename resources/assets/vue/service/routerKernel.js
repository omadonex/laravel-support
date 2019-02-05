const RouterKernelService = {
  data() {
    return {
      Data__routerKernel: {},
    };
  },

  methods: {
    routerKernel__registerMiddleware(middleware, key) {
      this.Data__routerKernel[key] = middleware;
    },

    routerKernel__processMiddleware(to, from, next) {

    },
  },
};

export default {
  install(Vue, options) {
    Vue.mixin(RouterKernelService);
  },
};
