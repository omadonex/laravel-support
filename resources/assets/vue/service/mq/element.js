/**
 * Устанавливаем список отслеживаемых состояний,
 * чтобы можно было обращаться в любом месте проекта к ним
 */
const { matchMedia } = window;
const SCREEN_STATES = {
  DESKTOP: matchMedia('(min-width: 768px)'),
  TABLET: matchMedia('(max-width: 767px)'),
  MOBILE: matchMedia('(max-width: 479px)'),
};

const MediaQueriesService = {
  data() {
    return {
      screenStates: SCREEN_STATES,
      isDesktop: SCREEN_STATES.DESKTOP.matches,
      isTablet: SCREEN_STATES.TABLET.matches,
      isMobile: SCREEN_STATES.MOBILE.matches,
    };
  },

  mounted() {
    Object.keys(SCREEN_STATES).forEach((stateName) => {
      // isDesktop, isTablat, isMobile
      const stateMatchesName = `is${this.$root.fUpCase(stateName.toLowerCase())}`;
      SCREEN_STATES[stateName].addListener((mq) => {
        this.$root[stateMatchesName] = mq.matches;
      });
    });
  },
};

export default {
  install(Vue, options) {
    Vue.mixin(MediaQueriesService);
  },
};
