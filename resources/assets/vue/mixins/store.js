import { mapGetters, mapMutations } from "vuex";

export default {
  computed: {
    ...mapGetters([
      'appLoggedIn',
      'appFromBrowser',
    ]),
  },

  methods: {
    ...mapMutations([
      'xSetFromBrowser',
      'xSetLoggedIn',
    ]),
  }
};
