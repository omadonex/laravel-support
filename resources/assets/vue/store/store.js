import Vuex from 'vuex';
import page from './modules/page';

const state = {
  loggedIn: false,
  fromBrowser: true,
  siteUrl: null,
};

const mutations = {
  xSetFromBrowser(state, value) {
    state.fromBrowser = value;
  },

  xSetLoggedIn(state, value) {
    state.loggedIn = value;
  },

  xSetSiteUrl(state, value) {
    state.siteUrl = value;
  },
};

const getters = {
  appLoggedIn: state => {
    return state.loggedIn;
  },

  appFromBrowser: state => {
    return state.fromBrowser;
  },

  appSiteUrl: state => {
    return state.siteUrl;
  },
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
