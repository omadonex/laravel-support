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
};

export default {
  namespaced: true,
  state,
  mutations,
};