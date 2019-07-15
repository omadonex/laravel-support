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
    p__pageLoader__usingStore() {
      return this.$root.Data__pageLoader.config.store;
    },

    pageLoader__ready() {
      return this.$root.appFromBrowser || !this.p__pageLoader__loading;
    },

    pageLoader__pageData() {
      return this.p__pageLoader__dataGetProp(this.p__pageLoader__page);
    },

    pageLoader__globalData() {
      return this.p__pageLoader__dataGetProp(this.p__pageLoader__const.GLOBAL_DATA_KEY);
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
    p__pageLoader__dataKeyExists(dotKey) {
      let obj = this.p__pageLoader__usingStore ? this.$store.state.page.data : this.$root.DataMain;

      return this.$root.propExists(obj, dotKey);
    },

    p__pageLoader__dataGetProp(dotKey) {
      let obj = this.p__pageLoader__usingStore ? this.$store.state.page.data : this.$root.DataMain;

      return this.$root.getProp(obj, dotKey);
    },

    p__pageLoader__dataDeleteProp(prop, dotKey) {
      if (this.p__pageLoader__usingStore) {
        this.$store.commit('page/pl__deleteData', {
          prop: prop,
          dotKey: dotKey,
        });
      } else {
        let obj = this.$root.getProp(this.$root.DataMain, dotKey);
        delete obj[prop];
      }
    },

    p__pageLoader__dataSet(prop, value, dotKey) {
      if (this.p__pageLoader__usingStore) {
        this.$store.commit('page/pl__updateData', {
          prop: prop,
          data: value,
          dotKey: dotKey,
        });
      } else {
        let obj = dotKey ? this.$root.getProp(this.$root.DataMain, dotKey) : this.$root.DataMain;
        this.$set(obj, prop, value);
      }
    },

    p__pageLoader__setProp(prop, value, dotKey, global) {
      let key = global ? this.p__pageLoader__const.GLOBAL_DATA_KEY : this.p__pageLoader__page;
      let keyFull = dotKey ? `${key}.${dotKey}` : key;
      this.p__pageLoader__dataSet(prop, value, keyFull);
      //TODO omadonex: сделать разделение для добавление нового свойства и обновление значения
      //TODO проверить работу vuex store
    },

    pageLoader__setPropPage(prop, value, dotKey) {
      return this.p__pageLoader__setProp(prop, value, dotKey, false);
    },

    pageLoader__setPropGlobal(prop, value, dotKey) {
      return this.p__pageLoader__setProp(prop, value, dotKey, true);
    },

    pageLoader__getStorePropKey(prop, global) {
      let key = global ? this.p__pageLoader__const.GLOBAL_DATA_KEY : this.p__pageLoader__page;

      return `${key}.${prop}`;
    },

    pageLoader__init(args) {
      if (!this.$root.appFromBrowser) {
        if (!this.p__pageLoader__dataKeyExists(this.p__pageLoader__const.GLOBAL_DATA_KEY)) {
          this.p__pageLoader__dataSet(this.p__pageLoader__const.GLOBAL_DATA_KEY, {});
        }

        if (!this.p__pageLoader__dataKeyExists(this.p__pageLoader__page)) {
          this.p__pageLoader__dataSet(this.p__pageLoader__page, {});
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

      if (this.$root.auth__loggedIn) {
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
        params[this.p__pageLoader__const.PARAM_PAGINATE] = callParams.hasOwnProperty('paginate') ? callParams.paginate : true;
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

      if (factData.item.force && this.p__pageLoader__dataKeyExists(`${factData.keyData}.${callParams.propName}`)) {
        this.p__pageLoader__dataDeleteProp(callParams.propName, factData.keyData);
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
              if (!this.p__pageLoader__dataKeyExists(`${factData.keyData}.${callParams.propName}`)) {
                this.p__pageLoader__dataSet(callParams.propName, {}, factData.keyData);
              }

              let dotKey = `${factData.keyData}.${callParams.propName}`;

              if ((callParams.queryToSubGroup === true) && callParams.query && callParams.query.hasOwnProperty(callParams.queryPropName)) {
                if (!this.p__pageLoader__dataKeyExists(`${factData.keyData}.${callParams.propName}.${callParams.query[callParams.queryPropName]}`)) {
                  this.p__pageLoader__dataSet(callParams.query[callParams.queryPropName], {}, `${factData.keyData}.${callParams.propName}`);
                }
                dotKey = `${factData.keyData}.${callParams.propName}.${callParams.query[callParams.queryPropName]}`;
              }

              this.p__pageLoader__dataSet(params.page, result.data, dotKey);
              this.p__pageLoader__dataSet('meta', result.meta, dotKey);
            } else {
              this.p__pageLoader__dataSet(callParams.propName, result.data, factData.keyData);
            }

            if (factData.item.hasOwnProperty('onLoad')) {
              this.$root[factData.item.onLoad](result.data, this);
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
          if (!this.p__pageLoader__dataKeyExists(`${factData.keyData}.${propName}`)) {
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