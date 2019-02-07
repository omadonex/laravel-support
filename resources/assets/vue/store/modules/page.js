import { getProp } from '../../../scripts/helpers';

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
      obj[prop] = payload.data;
    } else {
      state.data = { ...state.data, [prop]: payload.data };
    }
  },

  addItemToList(state, payload) {
    let obj = getProp(state.data, payload.propKey);
    let list = Array.isArray(obj) ? obj : obj[obj.meta.current_page];
    const index = payload.creating ? list.length : list.findIndex(item => item.id === payload.item.id);
    if (index > -1) {
      list.splice(index, 1, payload.item);
    }
  },
};

export default {
  namespaced: true,
  state,
  mutations,
};