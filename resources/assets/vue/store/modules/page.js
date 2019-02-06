import cloneDeep from 'clone-deep';

function getProp(obj, prop) {
  if (typeof obj === 'undefined') {
    return undefined;
  }

  const index = prop.indexOf('.');
  if (index > -1) {
    return this.getProp(obj[prop.substring(0, index)], prop.substr(index + 1));
  }

  return obj[prop];
}

const state = {
  data: {},
  index: 0,
};

const mutations = {
  updateData(state, payload) {
    let prop = payload.prop;
    let dotKey = payload.dotKey;
    if (dotKey) {
      let obj = getProp(state.data, dotKey);
      obj = {...obj, [prop]: payload.data };
    } else {
      state.data = { ...state.data, [prop]: payload.data };
    }
  },
};

export default {
  namespaced: true,
  state,
  mutations,
};