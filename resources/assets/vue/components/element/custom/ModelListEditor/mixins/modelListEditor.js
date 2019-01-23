/**
 * Общий mixin для всех используемых форм, позволяет формировать окончательный набор данных из свойства `form`
 */

export default {
  data() {
    return {
      modelListEditor__form: {
        type: 'store',
        title: '',
      }
    };
  },

  methods: {
    modelListEditor__new(formTitle) {
      this.modelListEditor__form.title = formTitle;
      this.modelListEditor__form.type = 'store';
      this.$refs.modelListEditor.newModel();
    },

    modelListEditor__edit(model, formTitle) {
      this.modelListEditor__form.title = formTitle;
      this.modelListEditor__form.type = 'update';
      this.$refs.modelListEditor.editModel(model);
    },
  },
};
