import Model from '../../../classes/Model';

export default {
  data() {
    return {
      valid: true,
      loading: false,
      errorsCustom: [],
      warningsCustom: [],
    };
  },

  props: {
    method: { type: String, default: 'post', validator(value) {
        return ['post', 'put', 'patch', 'get', 'delete', null, undefined]
          .indexOf(value) > -1;
      }
    },
    url: { type: String, required: true },
    formData: { type: Object, required: true },
    formModel: { type: String, default: null },
    loadingLong: { type: Boolean, default: false },
    rules: { type: Object, default: () => {} },
    needConfirm: { type: Boolean, default: false },
  },

  methods: {
    clearMessages() {
      this.errorsCustom.splice(0, this.errorsCustom.length);
      this.warningsCustom.splice(0, this.warningsCustom.length);
    },

    submit() {
      //Если передан пармаетр модели, то упаковываем данные
      let finalFormData = this.formModel ? this.$root.cm__getClass(this.formModel).packData(this.formData) : this.formData;
      // очищаем от общих ошибок, не относящихся к конкретным полям,
      // конкретные же поля, сами очистятся
      this.clearMessages();
      this.$root.smartAjax__call({
        callingObject: this,
        method: this.method,
        url: this.url,
        params: finalFormData,
        loadingLong: this.loadingLong,
        catchValidation: true,
      })
        .then((result) => {
          if (result) {
            this.$emit('submitSuccess', result);
          }

          return result;
        })
        .catch((error) => {
          if (error.response.status === 422) {
            this.submitCatch(error);
          }

          return Promise.reject(error);
        });
    },

    submitCatch(error) {
      const { errors, warnings } = error.response.data;
      this.$emit('submitFailed', errors);

      if (errors !== undefined) {
        Object.keys(errors).forEach((fieldKey) => {
          let finalFieldKey = fieldKey.replace('.', Model.objSeparator);
          this.setError(finalFieldKey, errors[fieldKey][0]);
        });
      }

      if (warnings !== undefined) {
        warnings.forEach((warning) => {
          this.warningsCustom.push(warning);
        });
      }
    },
  },
};