import { getProp } from '../../../scripts/helpers';

const state = {
  data: {},
  index: 0,
};

const mutations = {
  pl__UpdateData(state, payload) {
    let prop = payload.prop;
    let dotKey = payload.dotKey;
    if (dotKey) {
      let obj = getProp(state.data, dotKey);
      obj[prop] = payload.data;
    } else {
      state.data = { ...state.data, [prop]: payload.data };
    }
  },

  pl__AddItemToList(state, payload) {
    //TODO omadonex: если список пагинированный, то можно подумать насчет вставки записи в нужное место
    //TODO но в этом случае придется уведомлялки пользователю отправлять о добавлении записи, иначе он может ее не найти
    let obj = getProp(state.data, payload.propKey);
    let list = Array.isArray(obj) ? obj : obj[obj.meta.current_page];
    const index = payload.creating ? list.length : list.findIndex(item => item.id === payload.item.id);
    if (index > -1) {
      list.splice(index, 1, payload.item);
    }
  },

  modifyList__pushItem(state, payload) {
    let list = getProp(state.data, payload.propKey);
    list.push(payload.item);
  },

  modifyList__removeItem(state, payload) {
    let list = getProp(state.data, payload.propKey);
    list.splice(payload.index, 1);
  }
};

export default {
  namespaced: true,
  state,
  mutations,
};