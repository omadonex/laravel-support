import Model from '../../classes/Model';

export default {
  data() {
    return {
      translate__ns: {
        rules: "app.validation",
        omxCommon: 'vendor.support.common',
        omxCurrency: 'vendor.support.currency',
      },
    };
  },

  computed: {
    p__translate__transNs() {
      let data = {};
      for (const prop in this.translate__ns) {
        data[prop] = `${this.$root.DataLang.currLang}.${this.translate__ns[prop]}`;
      }

      return data;
    },
  },

  methods: {
    t(...args) {
      return this.translate__t(...args);
    },

    translate__t(...args) {
      let ns = 'default';
      let replaceArr = [];
      if (args.length === 2) {
        if (Array.isArray(args[1])) {
          replaceArr = args[1];
        } else {
          ns = args[1];
        }
      } else if (args.length === 3) {
        ns = args[1];
        replaceArr = args[2];
      }

      let text = this.$root.getProp(this.$root.DataLang.translations, `${this.p__translate__transNs[ns]}.${args[0]}`);

      replaceArr.forEach((replaceStr, index) => {
        text = text.replace(`:s${index}`, replaceStr);
      });

      return text;
    },

    translate__tFieldError(keyField, keyRule, ruleParams, ns = "fields") {
      let finalKeyField = keyField.replace(Model.objSeparator, '.');
      const transAttr = this.translate__t(finalKeyField, ns);
      const transRule = this.translate__t(keyRule, "rules");

      let trans = transRule.replace(":attribute", `"${transAttr}"`);
      if (ruleParams !== undefined) {
        ruleParams.forEach((item) => {
          trans = trans.replace(item.name, item.value);
        });
      }

      return trans;
    },
  },
};
