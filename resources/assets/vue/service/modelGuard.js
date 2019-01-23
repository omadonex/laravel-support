const ModelGuardService = {
  data() {
    return {
      Data__modelGuard: {
        UNSAFE_SEEDING: 'omx_unsafe_seeding',
        PROTECTED_GENERATE: 'omx_protected_generate',
      }
    };
  },

  methods: {
    modelGuard__check(data) {
      return this.modelGuard__checkUnsafeSeeding(data) || this.modelGuard__checkProtectedGenerate(data);
    },

    modelGuard__checkUnsafeSeeding(data) {
      if (data.hasOwnProperty(this.Data__modelGuard.UNSAFE_SEEDING)) {
        return data[this.Data__modelGuard.UNSAFE_SEEDING];
      }

      return false;
    },

    modelGuard__checkProtectedGenerate(data) {
      if (data.hasOwnProperty(this.Data__modelGuard.PROTECTED_GENERATE)) {
        return data[this.Data__modelGuard.PROTECTED_GENERATE];
      }

      return false;
    },
  },
};

export default {
  install(Vue, options) {
    Vue.mixin(ModelGuardService);
  },
};
