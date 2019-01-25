export default {
  data() {
    return {
      pageLoader__data: {},
      p__pageLoader__const: {
        PARAM_ENABLED: '__enabled',
        PARAM_PAGINATE: '__paginate',
        PARAM_RELATIONS: '__relations',
        PARAM_TRASHED: '__trashed',
        GLOBAL_DATA_KEY: 'global',
      },
      p__pageLoader__loading: false,
      p__pageLoader__page: this.$root.$route.meta.page,
      p__pageLoader__states: {},
    };
  },

  computed: {
    pageLoader__ready() {
      return this.$root.FromBrowser || !this.p__pageLoader__loading;
    },

    pageLoader__pageData() {
      return this.$root.DataMain[this.p__pageLoader__page];
    },

    pageLoader__globalData() {
      return this.$root.DataMain[this.p__pageLoader__const.GLOBAL_DATA_KEY];
    },
  },

  watch: {
    pageLoader__ready(val) {
      if (val) {
        this.p__pageLoader__initPageAfter();
      }
    },
  },

  methods: {
    pageLoader__init(args) {
      if (!this.$root.FromBrowser) {
        if (!this.$root.DataMain.hasOwnProperty(this.p__pageLoader__const.GLOBAL_DATA_KEY)) {
          this.$set(this.$root.DataMain, this.p__pageLoader__const.GLOBAL_DATA_KEY, {});
        }

        if (!this.$root.DataMain.hasOwnProperty(this.p__pageLoader__page)) {
          this.$set(this.$root.DataMain, this.p__pageLoader__page, {});
        }

        let props = this.p__pageLoader__getPropsForLoading(false, args);
        if (props.length > 0) {
          this.p__pageLoader__loadPageData(props);
        }
      } else {
        this.p__pageLoader__initPageAfter();
      }
    },

    /**
     * @param callParams
     * url *
     * propName *
     * list *
     * enabled
     * relations
     * paginate
     * paginatePage
     * trashed
     * loadingPropName
     * query
     * queryToSubGroup
     * queryPropName
     * method
     * @returns {Promise.<TResult>}
     */
    pageLoader__load(callParams) {
      let params = {};

      if (this.$root.LoggedIn) {
        params.userId = this.$root.DataUser.id;
      }

      if (callParams.hasOwnProperty('enabled')) {
        params[this.p__pageLoader__const.PARAM_ENABLED] = callParams.enabled;
      }

      if (callParams.hasOwnProperty('relations')) {
        params[this.p__pageLoader__const.PARAM_RELATIONS] = callParams.relations;
      }

      if (callParams.hasOwnProperty('trashed')) {
        params[this.p__pageLoader__const.PARAM_TRASHED] = callParams.trashed;
      }

      if (callParams.list) {
        params[this.p__pageLoader__const.PARAM_PAGINATE] = callParams.hasOwnProperty('paginate') ? callParams.paginate || true;
        if (params[this.p__pageLoader__const.PARAM_PAGINATE]) {
          params.page = callParams.paginatePage || 1;
        }
      }

      if (callParams.query) {
        for (const key in callParams.query) {
          params[key] = callParams.query[key];
        }
      }

      let factData = this.p__pageLoader__getFactData(callParams.propName);

      if (factData.item.force && this.$root.DataMain[factData.keyData].hasOwnProperty(callParams.propName)) {
        delete this.$root.DataMain[factData.keyData][callParams.propName];
      }

      return this.$root.smartAjax__call({
        callingObject: this,
        method: callParams.method || 'get',
        url: callParams.url,
        params: params,
        loadingPropName: callParams.loadingPropName
      })
        .then((result) => {
          if (result) {
            if (callParams.list && params[this.p__pageLoader__const.PARAM_PAGINATE]) {
              if (!this.$root.DataMain[factData.keyData].hasOwnProperty(callParams.propName)) {
                this.$set(this.$root.DataMain[factData.keyData], callParams.propName, {});
              }

              let propObj = this.$root.DataMain[factData.keyData][callParams.propName];

              if ((callParams.queryToSubGroup === true) && callParams.query && callParams.query.hasOwnProperty(callParams.queryPropName)) {
                if (!this.$root.DataMain[factData.keyData][callParams.propName].hasOwnProperty(callParams.query[callParams.queryPropName])) {
                  this.$set(this.$root.DataMain[factData.keyData][callParams.propName], callParams.query[callParams.queryPropName], {});
                }
                propObj = this.$root.DataMain[factData.keyData][callParams.propName][callParams.query[callParams.queryPropName]];
              }

              this.$set(propObj, params.page, result.data);
              this.$set(propObj, 'meta', result.meta);
            } else {
              this.$set(this.$root.DataMain[factData.keyData], callParams.propName, result.data);
            }
          }

          return result;
        });
    },

    p__pageLoader__getFactData(propName) {
      let item = this.pageLoader__data[propName];
      let global = false;
      let globalParamsFuncName = null;
      let keyData = this.p__pageLoader__page;
      let stateName = propName;
      if (item.global === true) {
        item = this.$root.Data__plGlobal[propName];
        global = true;
        globalParamsFuncName = item.paramsFuncName;
        keyData = this.p__pageLoader__const.GLOBAL_DATA_KEY;
        stateName = `${keyData}__${stateName}`;
      }

      return {
        item: item,
        global: global,
        globalParamsFuncName: globalParamsFuncName,
        keyData: keyData,
        stateName: stateName,
      }
    },

    p__pageLoader__getPropsForLoading(deferred, args) {
      let props = [];
      for (let propName in this.pageLoader__data) {
        let factData = this.p__pageLoader__getFactData(propName);
        if (factData.item.deferred === deferred) {
          let propArgs = (args && args.hasOwnProperty(propName)) ? args[propName] : undefined;
          if (!this.$root.DataMain[factData.keyData].hasOwnProperty(propName)) {
            let setLoading = !deferred;
            props.push({propName: propName, propArgs: propArgs, setLoading: setLoading, global: factData.global, globalParamsFuncName: factData.globalParamsFuncName});
          } else if (!factData.item.once) {
            let setLoading = !deferred && factData.item.force;
            props.push({propName: propName, propArgs: propArgs, setLoading: setLoading, global: factData.global, globalParamsFuncName: factData.globalParamsFuncName});
          }
        }
      }

      return props;
    },

    p__pageLoader__initPageAfter() {
      let props = this.p__pageLoader__getPropsForLoading(true);
      if (props.length > 0) {
        this.p__pageLoader__loadPageData(props);
      }
    },

    p__pageLoader__loadPageData(props) {
      let propsWithLoading = props.filter((item) => {
        return item.setLoading === true;
      });
      if (propsWithLoading.length > 0) {
        this.p__pageLoader__loading = true;
      }

      this.p__pageLoader__states = [];
      props.forEach((item) => {
        let factData = this.p__pageLoader__getFactData(item.propName);

        if (item.setLoading) {
          this.p__pageLoader__states[factData.stateName] = true;
        }

        let propItem = factData.item;
        let loadFunc = null;
        if (factData.global) {
          loadFunc = (obj) => {
            let loadParams = obj.$root[propItem.paramsFuncName]();
            return obj.pageLoader__load(loadParams);
          }
        } else {
          loadFunc = (obj) => {
            return obj[propItem.funcName](item.propArgs);
          }
        }

        loadFunc(this)
          .then(() => {
            if (item.setLoading) {
              this.p__pageLoader__evalLoadingState(factData.stateName);
            }
          })
          .catch(() => {
            if (item.setLoading) {
              this.p__pageLoader__evalLoadingState(factData.stateName);
            }
          });
      });
    },

    p__pageLoader__evalLoadingState(stateName) {
      this.p__pageLoader__states[stateName] = false;

      let loading = false;
      for (let name in this.p__pageLoader__states) {
        if (this.p__pageLoader__states[name] === true) {
          loading = true;
        }
      }

      this.p__pageLoader__loading = loading;
    },
  },
}