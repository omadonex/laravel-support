<template>
  <v-text-field
          ref="field"
          v-model="fieldValue"
          :prepend-icon="icon"
          :label="label"
          :type="type"
          :rules="rules"
          :required="required"
          :disabled="scopeLoading"
          :error-messages="scopeErrorsMessages"
          @input="input">
  </v-text-field>
</template>

<script>
  export default {
    name: 'OmxVuetifyFormTextField',

    data() {
      return {
        fieldValue: this.value,
      };
    },

    props: {
      value: { required: true },
      field: { type: String, required: true },
      scope: { type: Object, required: true },

      icon: { type: String, default: undefined },
      label: { type: String, default: undefined },
      type: { type: String, default: 'text' },
      required: { type: Boolean, default: true },
    },

    computed: {
      scopeLoading() {
        return this.scope.loading;
      },

      scopeErrorsMessages() {
        return this.scope.errorsFields[this.field];
      },

      rules() {
        if (this.scope.rules.hasOwnProperty(this.field)) {
          return this.scope.rules[this.field];
        }

        return [];
      },
    },

    watch: {
      value(v) {
        this.fieldValue = v;
      }
    },

    methods: {
      input() {
        this.scope.errorsFields[this.field] = [];
        this.$emit('input', this.fieldValue);
      },

      forceValidate() {
        this.$refs.field.validate();
      },
    },
  };
</script>
