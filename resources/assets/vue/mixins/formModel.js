import Model from "../../classes/Model";

export default {
  data() {
    return {
      /**
       * Работа внутри формы происходит не с оригинальной моделью (p__formAbstract__formData), а с ее копией.
       * Это позволяет всячески изменять копию, а разочаровавшись, просто нажать Отмена и "откатить" их,
       * (на деле никакого отката нет - оригинальная модель просто не обновляется изменными данными)
       * Если же изменения нас устраивают, жмем Сохранить,
       * и только в случае успеха (всплыло событие @submitSuccess) оригинальная модель будет обновлена
       */
      p__formModel__refAjaxForm: 'ajaxForm',
      formModel__ready: false,
      form: {
        method: null,
        url: null,
        fields: {},
        appends: {},
      }
    };
  },

  model: {
    prop: 'formModel__data',
    event: 'submitSuccess',
  },

  props: {
    formModel__data: { type: Object, required: true },
    formModel__class: { type: String, required: true },
    formModel__type: { type: String, default: 'store', validator(value) {
        return ['store', 'update', null, undefined].indexOf(value) > -1;
      }
    },
    formModel__shown: { type: Boolean, required: true },
    formModel__relations: { type: Array, default: [] },
  },

  computed: {
    p__formModel__method() {
      return this.formModel__type === 'store' ? 'post' : 'put';
    },

    formModel__isNewMode() {
      return this.formModel__type === 'store';
    },

    formModel__isEditMode() {
      return this.formModel__type === 'update';
    },
  },

  watch: {
    formModel__shown(value) {
      if (value) {
        this.p__formModel__setInnerForm();
        this.formModel__ready = true;
      }
    }
  },

  methods: {
    p__formModel__getFieldValue(model, field) {
      let fieldValue;
      if (this.formModel__type === 'store') {
        fieldValue = model.fillable[field].default || null;
      } else {
        if (model.fillable[field].modelField) {
          fieldValue = this.$root.getProp(this.formModel__data, model.fillable[field].modelField);
        } else if (model.fillable[field].obj) {
          fieldValue = this.$root.getProp(this.formModel__data, field.replace(Model.objSeparator, '.'));
        } else {
          fieldValue = this.formModel__data[field];
        }
      }

      return fieldValue;
    },

    p__formModel__setInnerForm() {
      let model = this.$root.cm__getClass(this.formModel__class);
      let modelId = this.formModel__type === 'store' ? null : this.formModel__data.id;

      this.form.method = this.p__formModel__method;
      this.form.url = this.$root.route(model.urlNames[this.formModel__type], modelId, false).url();

      let fields = {};
      for (let field in model.fillable) {
        fields[field] = {
          value: this.p__formModel__getFieldValue(model, field),
          rules: model.fillable[field].rules,
        }
      }
      this.form.fields = fields;

      if (this.formModel__relations.length) {
        this.form.appends = {
          __relations: this.formModel__relations,
        };
      }
    },

    /**
     * Кидаем во внешний мир событие submitSuccess, с новыми данными
     * ( которое ко всему прочему еще и обновит модель, так как указано в model.event )
     */
    formModel__submitSuccess(data) {
      this.formModel__reset();
      this.$emit('submitSuccess', data);
    },

    /**
     * Обработчик закрытия формы
     */
    formModel__cancel() {
      this.formModel__reset();
      this.$emit('cancel');
    },

    formModel__reset() {
      this.pr__formModel__clear();
      this.formModel__ready = false;
    },

    /**
     * Отдельный метод, чтобы можно было переопределить в потомках, расширив по мере надобности
     */
    pr__formModel__clear() {
      this.$refs[this.p__formModel__refAjaxForm].clearValidate(true);
    },
  },

  created() {
    this.p__formModel__setInnerForm();
    this.formModel__ready = true;
  },
};
