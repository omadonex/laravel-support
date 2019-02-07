/**
 * Делает первую букву заглавной
 * @param str
 * @returns {string}
 */
function fUpCase(str) {
  return `${str.charAt(0).toUpperCase()}${str.slice(1)}`;
}

/**
 * Превращает строку в стиле "Dot notation" в camelCase. Например, ab.cd.ef => abCdEf
 * @param dotName
 * @returns {string}
 */
function getCamelName(dotName) {
  const dotParts = dotName.split('.');
  const countParts = dotParts.length;
  let name = dotParts[0];

  for (let i = 1; i < countParts; i += 1) {
    name += fUpCase(dotParts[i]);
  }

  return name;
}

/**
 * Возвращает index элемента списка, у которого значение указанного свойства совпадает с value
 * @param itemList
 * @param value
 * @param propName
 * @returns {*}
 */
function getIndex(itemList, value, propName) {
  let prop = propName || 'id';

  return itemList.findIndex(item => item[prop] === value);
}

/**
 * Получение свойства объекта в стиле "Dot notation"
 * @param obj
 * @param prop
 * @returns {*|*|*}
 */
function getProp(obj, prop) {
  if (typeof obj === 'undefined') {
    return undefined;
  }

  const index = prop.indexOf('.');
  if (index > -1) {
    return getProp(obj[prop.substring(0, index)], prop.substr(index + 1));
  }

  return obj[prop];
}

/**
 * Проверка существования свойства объекта, заданного в стиле "Dot notation"
 * @param obj
 * @param prop
 * @returns {*|*}
 */
function propExists(obj, prop) {
  if (typeof obj === 'undefined') {
    return undefined;
  }

  const lastIndex = prop.lastIndexOf('.');
  if (lastIndex > -1) {
    return getProp(obj, prop.substring(0, lastIndex)).hasOwnProperty(prop.substr(lastIndex + 1));
  }

  return obj.hasOwnProperty(prop);
}

/**
 * Полная замена подстроки в строке
 * @param search
 * @param replace
 * @param subject
 * @returns {*}
 */
function strReplaceAll(search, replace, subject) {
  const reg = new RegExp(search, 'g');

  return subject.replace(reg, replace);
}

export {
  fUpCase,
  getCamelName,
  getIndex,
  getProp,
  propExists,
  strReplaceAll,
};