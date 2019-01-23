export default {
  computed: {
    validate__rules() {
      const keys = Object.keys(this.form.fields);
      const rules = {};
      keys.forEach((item) => {
        if (this.form.fields[item].rules !== undefined) {
          rules[item] = [];
          const ruleStrs = this.form.fields[item].rules.split("|");
          const ruleTrigger = (this.form.fields[item].trigger !== undefined) ? this.form.fields[item].trigger : "blur";
          ruleStrs.forEach((ruleStr) => {
            const ruleArr = ruleStr.split(":");
            const ruleName = ruleArr[0];
            const ruleParams = ruleArr[1];
            rules[item].push({
              trigger: ruleTrigger,
              validator: this[`validate__${ruleName}Rule`](item, ruleParams),
            });
          });
        }
      });

      return rules;
    },
  },

  methods: {
    validate__confirmedRule(field) {
      return (r, v, c) => (v === this.form.fields[`${field}_confirmation`].value) ? c() : c(new Error(this.translate__tFieldError(field, "confirmed")));
    },

    validate__emailRule(field) {
      return (r, v, c) => (/^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v)) ? c() : c(new Error(this.translate__tFieldError(field, "email")));
    },

    validate__minRule(field, ruleParams) {
      const type = (this.form.fields[field].type !== undefined) ? this.form.field[field].type : "String";
      const minType = type.substr(0, 1).toLowerCase() + type.substr(1);
      const params = [
        {name: ":min", value: ruleParams},
      ];

      return (r, v, c) => (v && v.length >= ruleParams) ? c() : c(new Error(this.translate__tFieldError(field, `min.${minType}`, params)));
    },

    validate__requiredRule(field) {
      return (r, v, c) => (!!v) ? c() : c(new Error(this.translate__tFieldError(field, "required")));
    },
  },
};
