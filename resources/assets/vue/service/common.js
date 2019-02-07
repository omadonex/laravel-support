import { Routes } from '../../scripts/routes';
import { fUpCase, getCamelName, getIndex, getProp, propExists, strReplaceAll } from "../../scripts/helpers";

const CommonService = {
  methods: {
    /**
     * Позволяет выводить данные через console.log прямо из Vue template
     * @param args
     */
    console(...args) {
      console.log(...args);
    },

    /**
     * Делает первую букву заглавной
     * @param str
     * @returns {string}
     */
    fUpCase(str) {
      return fUpCase(str);
    },

    /**
     * Превращает строку в стиле "Dot notation" в camelCase. Например, ab.cd.ef => abCdEf
     * @param dotName
     * @returns {string}
     */
    getCamelName(dotName) {
      return getCamelName(dotName);
    },

    /**
     * Возвращает index элемента списка, у которого значение указанного свойства совпадает с value
     * @param itemList
     * @param value
     * @param propName
     * @returns {*}
     */
    getIndex(itemList, value, propName) {
      return getIndex(itemList, value, propName);
    },

    /**
     * Получение свойства объекта в стиле "Dot notation"
     * @param obj
     * @param prop
     * @returns {*|*|*}
     */
    getProp(obj, prop) {
      return getProp(obj, prop);
    },

    /**
     * Проверка существования свойства объекта, заданного в стиле "Dot notation"
     * @param obj
     * @param prop
     * @returns {*|*}
     */
    propExists(obj, prop) {
      return propExists(obj, prop);
    },

    /**
     * Обертка вокруг прокидки Laravel Route в js
     * @param args
     */
    route(...args) {
      return Routes(...args);
    },

    /**
     * Полная замена подстроки в строке
     * @param search
     * @param replace
     * @param subject
     * @returns {*}
     */
    strReplaceAll(search, replace, subject) {
      return strReplaceAll(search, replace, subject);
    },
  },
};

export default {
  install(Vue, options) {
    Vue.mixin(CommonService);
  },
};
