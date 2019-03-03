<template>
  <el-form
      ref="form"
      v-loading="loading"
      :rules="rules"
      :model="formData"
      @submit.native.prevent="trySubmit">

    <slot name="errors" :errorsCustom="errorsCustom">
      <el-alert v-if="errorsCustom.length" type="error" show-icon
                :title="t('hasErrors')"
                :closable="false">
        <div v-for="(error, key) in errorsCustom" :key="'errorCustom-' + key">{{ error }}</div>
      </el-alert>
    </slot>

    <slot name="warnings" :warningsCustom="warningsCustom">
      <el-alert v-if="warningsCustom.length" type="warning" show-icon
                :title="t('hasWarnings')"
                :closable="false">
        <div v-for="(warning, key) in warningsCustom" :key="'warningCustom-' + key">{{ warning }}</div>
      </el-alert>
    </slot>

    <slot></slot>

    <slot name="actions">
      <template v-if="btnSubmitWide">
        <div class="mt--4">
          <confirm-button :wide="true" native-type="submit" @click="trySubmit()" v-if="needConfirm">{{ finalBtnSubmitText }}</confirm-button>
          <el-button class="submit-wide" native-type="submit" type="primary" v-else>{{ finalBtnSubmitText }}</el-button>
        </div>
      </template>
      <template v-else>
        <div :class="[buttonsPositionClass, 'mt--4']">
          <el-button v-if="btnCancelShow" @click="$emit('cancel')">{{ finalBtnCancelText }}</el-button>
          <confirm-button native-type="submit" @click="trySubmit()" v-if="needConfirm">{{ finalBtnSubmitText }}</confirm-button>
          <el-button native-type="submit" type="primary" v-else>{{ finalBtnSubmitText }}</el-button>
        </div>
      </template>
    </slot>
  </el-form>
</template>

<script>
  import ConfirmButton from '../../custom/Confirm/ConfirmButton.vue';
  import AjaxFormMixin from '../../../mixins/ajaxForm';
  import TranslateMixin from '../../../../mixins/translate';

  export default {
    name: 'OmxElementAjaxForm',
    mixins: [AjaxFormMixin, TranslateMixin],
    components: { ConfirmButton },

    data() {
      return {
        translate__ns: {
          default: 'vendor.support.components.ajaxForm',
        },
      };
    },

    props: {
      buttonsCenter: { type: Boolean, default: false },
      btnSubmitWide: { type: Boolean, default: false },
      btnCancelShow: { type: Boolean, default: false },
      btnSubmitText: { type: String, default: null },
      btnCancelText: { type: String, default: null },
    },

    computed: {
      finalBtnSubmitText() {
        return this.btnSubmitText || this.t('submit', 'omxCommon');
      },

      finalBtnCancelText() {
        return this.btnCancelText || this.t('cancel', 'omxCommon');
      },

      buttonsPositionClass() {
        return this.buttonsCenter ? 'text--center' : 'text--right';
      },
    },

    methods: {
      forceValidate(field) {
        this.$refs.form.validateField(field);
      },

      /**
       * Позволяет очистить валидацию формы от ошибок полей,
       * а если передан флаг isFullClear - то и от общих ошибок
       * @param isFullClear
       */
      clearValidate(isFullClear = false) {
        this.$refs.form.clearValidate();
        if (isFullClear) {
          this.clearMessages();
        }
      },

      trySubmit() {
        this.$refs.form.validate()
          .then(() => { this.submit(); })
          .catch(() => {});
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
        const $field = this.$refs.form.fields.filter(field => field.prop === prop)[0];
        // если поле существует - ставим ему ошибку
        if ($field) {
          this.setFieldError($field, errorMessage);
        } else {
          // в противном случае, кидаем в список общих ошибок формы
          this.errorsCustom.push(errorMessage);
        }
      },

      /**
       * Пытается установить ошибку указанному полю
       * @param $field
       * @param errorMessage
       */
      setFieldError($field, errorMessage) {
        $field.validateMessage = errorMessage;
        $field.validateState = 'error';
      },
    },
  };
</script>

<style lang="scss" scoped>
  .submit-wide {
    width: 100%;
  }
</style>
