import cloneDeep from 'clone-deep';

const state = {
  data: {},
  index: 0,
};

const mutations = {
  updateData(state, data) {
    for (let prop in data) {
      state.data = { ...state.data, [prop]: cloneDeep(data[prop]) };
    }
  }
};

export default {
  namespaced: true,
  state,
  mutations,
};