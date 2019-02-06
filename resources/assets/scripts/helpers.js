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

function propExists(obj, prop) {
  if (typeof obj === 'undefined') {
    return undefined;
  }

  const lastIndex = prop.lastIndexOf('.');
  if (lastIndex > -1) {
    return this.getProp(obj, prop.substring(0, lastIndex)).hasOwnProperty(prop.substr(lastIndex + 1));
  }

  return obj.hasOwnProperty(prop);
}

export {
  getProp,
  propExists,
};