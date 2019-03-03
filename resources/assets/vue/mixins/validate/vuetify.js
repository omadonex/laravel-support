export default {
  computed: {
    validate__rules() {
      const keys = Object.keys(this.form.fields);
      const rules = {};
      keys.forEach((item) => {
        if (this.form.fields[item].rules !== undefined) {
          rules[item] = [];
          const ruleStrs = this.form.fields[item].rules.split("|");
          ruleStrs.forEach((ruleStr) => {
            const ruleArr = ruleStr.split(':');
            const ruleName = ruleArr[0];
            const ruleParams = ruleArr[1];
            rules[item].push(this[`validate__${ruleName}Rule`](item, ruleParams));
          });
        }
      });

      return rules;
    },
  },

  methods: {
    validate__confirmedRule(field) {
      return v => (v === this.form.fields[`${field}_confirmation`].value) || this.translate__tFieldError(field, 'confirmed');
    },

    validate__emailRule(field) {
      return v => /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) || this.translate__tFieldError(field, 'email');
    },

    validate__minRule(field, ruleParams) {
      const type = this.form.fields[field].type || 'String';
      const minType = type.substr(0, 1).toLowerCase() + type.substr(1);
      const params = [
        { name: ':min', value: ruleParams },
      ];
      return v => (v && v.length >= ruleParams) || this.translate__tFieldError(field, `min.${minType}`, params);
    },

    validate__requiredRule(field) {
      return v => !!v || this.translate__tFieldError(field, 'required');
    },
  },
};
