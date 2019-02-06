import { Routes } from '../../scripts/routes';

const CommonService = {
  methods: {
    /**
     * Обертка вокруг прокидки Laravel Route в js
     * @param args
     * @returns {*}
     */
    route(...args) {
      return Routes(...args);
    },

    /**
     * Позволяет выводить данные через console.log прямо из Vue template
     * @param args
     */
    console(...args) {
      console.log(...args);
    },

    /**
     * Полная замена подстроки в строке
     * @param search
     * @param replace
     * @param subject
     */
    strReplaceAll: (search, replace, subject) => {
      const reg = new RegExp(search, 'g');
      return subject.replace(reg, replace);
    },

    /**
     * Делает первую букву заглавной
     * @param string
     */
    fUpCase: string => `${string.charAt(0).toUpperCase()}${string.slice(1)}`,

    /**
     * Получить свойство объекта в стиле "Dot notation"
     */
    getProp(obj, prop) {
      if (typeof obj === 'undefined') {
        return undefined;
      }

      const index = prop.indexOf('.');
      if (index > -1) {
        return this.getProp(obj[prop.substring(0, index)], prop.substr(index + 1));
      }

      return obj[prop];
    },

    propExists(obj, prop) {
      if (typeof obj === 'undefined') {
        return undefined;
      }

      const lastIndex = prop.lastIndexOf('.');
      if (lastIndex > -1) {
        return this.getProp(obj, prop.substring(0, lastIndex)).hasOwnProperty(prop.substr(lastIndex + 1));
      }

      return obj.hasOwnProperty(prop);
    },

    getCamelName(dotName) {
      const dotParts = dotName.split('.');
      const countParts = dotParts.length;
      let name = dotParts[0];

      for (let i = 1; i < countParts; i += 1) {
        name += this.$root.fUpCase(dotParts[i]);
      }

      return name;
    },

    getIndex(itemList, value, propName) {
      let prop = propName || 'id';

      return itemList.findIndex(item => item[prop] === value);
    },
  },
};

export default {
  install(Vue, options) {
    Vue.mixin(CommonService);
  },
};
