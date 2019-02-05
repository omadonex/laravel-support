const state = {
  data: {},
  index: 0,
};

const mutations = {
  updateData(state, data) {
    for (let prop in data) {
      state.data = { ...state.data, [prop]: data[prop] };
    }
  }
}

export default {
  namespaced: true,
  state,
  mutations,
};