<template>
  <v-card :class="classes">
    <v-toolbar dense dark color="blue-grey" v-if="titleType === 'toolbar'">
      <v-toolbar-title>{{ titleText }}</v-toolbar-title>
    </v-toolbar>
    <v-card-title v-else-if="titleType === 'text'">
      <span class="headline">{{ titleText }}</span>
    </v-card-title>

    <v-card-text>
      <v-form v-model="valid" ref="form" lazy-validation>
        <slot name="errors" :errorsCustom="errorsCustom">
          <v-alert class="mb-3" type="error" :value="errorsCustom.length > 0">
            <div class="mt-1" v-for="(error, key) in errorsCustom" :key="key">{{ error }}</div>
          </v-alert>
        </slot>
        <slot name="warnings" :warningsCustom="warningsCustom">
          <v-alert class="mb-3" type="warning" :value="warningsCustom.length > 0">
            <div class="mt-1" v-for="(warning, key) in warningsCustom" :key="key">{{ warning }}</div>
          </v-alert>
        </slot>
        <slot name="fields" :loading="loading" :errors-fields="errorsFields" :rules="rules"></slot>
      </v-form>
    </v-card-text>

    <v-card-actions>
      <slot name="actions"></slot>
      <v-spacer></v-spacer>
      <v-btn :large="submitLarge"
             :loading="loading"
             :disabled="!valid || loading"
             @click="trySubmit"
             :class="submitClasses">
        {{ submitText }}
        <v-icon :right="submitIcon !== ''" :dark="submitIconDark">{{ submitIcon }}</v-icon>
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
  import AjaxFormMixin from '../../../mixins/ajaxForm';

  export default {
    name: 'OmxVuetifyAjaxForm',
    mixins: [AjaxFormMixin],

    data() {
      return {
        errorsFields: this.initErrorsFields(),
      };
    },

    props: {
      submitLarge: { type: Boolean, default: false },
      submitIcon: { type: String, default: '' },
      submitIconDark: { type: Boolean, default: true },
      submitText: { type: String, default: '' },
      submitClasses: { type: String, default: 'blue-grey white--text' },
      titleType: { type: String, default: 'toolbar' },
      titleText: { type: String, default: '' },
      classes: { type: String, default: 'elevation-12' },
    },

    methods: {
      initErrorsFields() {
        const keys = Object.keys(this.formData);
        const errors = {};
        keys.forEach((item) => {
          errors[item] = [];
        });

        return errors;
      },

      /**
       * Позволяет очистить валидацию формы от ошибок полей,
       * а если передан флаг isFullClear - то и от общих ошибок
       * @param isFullClear
       */
      clearValidate(isFullClear = false) {
        this.$refs.form.reset();
        if (isFullClear) {
          this.clearMessages();
        }
      },

      trySubmit() {
        if (this.$refs.form.validate()) {
          this.submit();
        }
      },

      /**
       * Пытается по свойству prop найти нужное поле формы
       *
       * У компонента <el-form> список полей (<el-form-item>), хранится в МАССИВЕ  fields
       * ищем нужное поле по его свойству prop
       * @param prop
       * @param errorMessage
       */
      setError(prop, errorMessage) {
        const keys = Object.keys(this.errorsFields);
        if (keys.findIndex(item => item === prop) > -1) {
          this.errorsFields[prop] = errorMessage;
        } else {
          this.errorsCustom.push(errorMessage);
        }
      },
    },
  };
</script>
