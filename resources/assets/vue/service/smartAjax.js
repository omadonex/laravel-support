import axios from 'axios';

const meta = document.head.querySelector('meta[name="csrf-token"]');
const csrfToken = meta ? meta.content : null;
const axiosInstance = axios.create({
  headers: {
    common: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': csrfToken,
    },
  },
});

const SmartAjaxService = {
  data() {
    return {
      Data__smartAjax: {
        csrfToken: csrfToken,
        axios: axiosInstance,
        transNs: 'vendor.support.mixins.smartAjax',
      },
    };
  },

  methods: {
    getCSRFToken() {
      return this.Data__smartAjax.csrfToken;
    },

    __smartAjax__setLoading(callingObject, value, loadingPropName, loadingKey) {
      if (loadingPropName) {
        if (loadingKey) {
          if (!callingObject[loadingPropName].hasOwnProperty(loadingKey)) {
            this.$set(callingObject[loadingPropName], loadingKey);
          }
          callingObject[loadingPropName][loadingKey] = value;
        } else {
          callingObject[loadingPropName] = value;
        }
      } else {
        callingObject.loading = value;
      }
    },

    __smartAjax__error(text, title) {
      if (typeof this.notify__showError === 'function') {
        this.notify__showError(text, title);
      } else {
        console.log('SmartAjaxService Error:');
        console.log(title);
        console.log(text);
      }
    },

    /**
     *
     * @param callParams
     * callingObject *
     * method *
     * url *
     * params
     * files
     * loadingPropName
     * loadingKey
     * loadingLong
     * catchValidation
     * @returns {Promise.<TResult>}
     */
    smartAjax__call(callParams) {
      this.__smartAjax__setLoading(callParams.callingObject, true, callParams.loadingPropName, callParams.loadingKey);

      let finalParams;
      let options = {};

      if (callParams.files) {
        finalParams = new FormData();
        if (callParams.params) {
          for (let param in callParams.params) {
            finalParams.append(param, callParams.params[param]);
          }
        }
        options = {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        };
      } else {
        finalParams = {};
        if (callParams.params) {
          finalParams = (callParams.method === 'delete') ? {data: callParams.params} : callParams.params;
        }
      }

      if (callParams.method === 'get') {
        finalParams = { params: finalParams };
      }

      //TODO omadonex: проверить DELETE и отправку файлов
      return this.Data__smartAjax.axios[callParams.method](callParams.url, finalParams, options)
        .then((response) => {
          if (!callParams.loadingLong) {
            this.__smartAjax__setLoading(callParams.callingObject, false, callParams.loadingPropName, callParams.loadingKey);
          }
          let result = response.data.result;
          if (response.data.status === true) {
            return result;
          }

          this.__smartAjax__error(result.errorMessage, this.t('errorRequest', this.Data__smartAjax.transNs));

          return null;
        })
        .catch((error) => {
          this.__smartAjax__setLoading(callParams.callingObject, false, callParams.loadingPropName, callParams.loadingKey);
          // Error
          if (error.response) {
            // The request was made and the server responded with a status code
            // that falls out of the range of 2xx
            switch (error.response.status) {
              case 422:
                if (!callParams.catchValidation) {
                  //TODO omadonex: переделать эту секцию
                  const { errors, warnings } = error.response.data;

                  let html = '';
                  if (errors !== undefined) {
                    html += '<div class="errors">';
                    for (let param in errors) {
                      html += `<div class="error">${errors[param]}</div>`;
                    }
                    html += '</div>';
                  }

                  if (warnings !== undefined) {
                    html += '<div class="warnings">';
                    for (let param in warnings) {
                      html += `<div class="warning">${warnings[param]}</div>`;
                    }
                    html += '</div>';
                  }

                  this.__smartAjax__error(html, this.t('errorValidation', this.Data__smartAjax.transNs), true);
                }
                break;
              case 500:
                this.__smartAjax__error(this.t('tryAgain', this.Data__smartAjax.transNs), this.t('errorServer', this.Data__smartAjax.transNs));
                break;
              default:
                this.__smartAjax__error(this.t('tryAgain', this.Data__smartAjax.transNs), this.t('errorUnhandled', this.Data__smartAjax.transNs));
            }
          } else if (error.request) {
            // The request was made but no response was received
            // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
            // http.ClientRequest in node.js
            this.__smartAjax__error(this.t('tryAgain', this.Data__smartAjax.transNs), this.t('errorUnexpected', this.Data__smartAjax.transNs));
            //console.log(error.request);
          } else {
            // Something happened in setting up the request that triggered an Error
            this.__smartAjax__error(this.t('tryAgain', this.Data__smartAjax.transNs), this.t('errorUnexpected', this.Data__smartAjax.transNs));
            //console.log('Error', error.message);
          }
          //console.log(error.config);

          return Promise.reject(error);
        });
    }
  },
};

export default {
  install(Vue, options) {
    Vue.mixin(SmartAjaxService);
  },
};