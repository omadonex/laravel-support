import { mapGetters, mapMutations } from "vuex";

export default {
  computed: {
    ...mapGetters([
      'appLoggedIn',
      'appFromBrowser',
      'appSiteUrl',
    ]),
  },

  methods: {
    ...mapMutations([
      'xSetFromBrowser',
      'xSetLoggedIn',
      'xSetSiteUrl',
    ]),
  }
};
