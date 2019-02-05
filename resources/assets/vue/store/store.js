import Vuex from 'vuex';
import page from './modules/page';

const state = {
  loggedIn: false,
  fromBrowser: true,
};

const mutations = {
  xSetFromBrowser(state, value) {
    state.fromBrowser = value;
  },

  xSetLoggedIn(state, value) {
    state.loggedIn = value;
  }
};

const getters = {
  appLoggedIn: state => {
    return state.loggedIn;
  },

  appFromBrowser: state => {
    return state.fromBrowser;
  }
};

const debug = process.env.NODE_ENV !== 'production';

export default new Vuex.Store({
  state,
  mutations,
  getters,
  modules: {
    page,
  },
  strict: debug,
});
