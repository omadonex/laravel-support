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

    routerKernel__processMiddleware(key, actions) {
      return (new this.Data__routerKernel[key]).handle(this.$root, actions);
    },

    routerKernel__init(usingStore) {
      this.$router.beforeEach((to, from, next) => {
        if (this.appFromBrowser) {
          if (usingStore) {
            this.xSetFromBrowser(false);
          } else {
            this.appFromBrowser = false;
          }
        }

        if (to.meta.middleware && Array.isArray(to.meta.middleware)) {
          let middlewareCount = to.meta.middleware.length;
          for (let i = 0; i < middlewareCount; i++) {
            let result = this.routerKernel__processMiddleware(to.meta.middleware[i], to.meta.actions);
            if (result !== true) {
              if (result instanceof Object) {
                next(result);
              } else if (result !== false) {
                window.location.href = result;
              }
              return ;
            }
          }
        }

        //Обновляем сео информацию на странице
        if (this.DataPage[to.meta.page]) {
          const pageData = this.DataPage[to.meta.page];
          document.title = pageData.seo.title;
        }

        next();
      });
    },
  },
};

export default {
  install(Vue, options) {
    Vue.mixin(RouterKernelService);
  },
};
