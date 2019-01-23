/**
 * Общий mixin для всех используемых форм, позволяет формировать окончательный набор данных из свойства `form`
 */

export default {
  methods: {
    /**
     * Формирует окончательные данные для передачи в запрос или AjaxForm
     * В форме в зависимости от каких-либо параметров могут быть некоторые поля неактивны,
     * за эту часть отвечает функция `form__getFactFieldsNames` (она возвращает массив названий актуальных полей)
     * Также могут быть просто дополнительные поля, которые в форме не отображаются (например токены)
     * эти поля перечисляются в свойстве `appends`
     * @returns {{}}
     */
    form__getFormData() {
      const keys = Object.keys(this.form.fields);
      const data = {};

      const factFieldsNames = this.form__getFactFieldsNames();
      keys.forEach((item) => {
        if (!factFieldsNames || (factFieldsNames.indexOf(item) !== -1)) {
          data[item] = this.form.fields[item].value;
        }
      });

      if (this.form.hasOwnProperty("appends")) {
        const keysAppends = Object.keys(this.form.appends);
        keysAppends.forEach((item) => data[item] = this.form.appends[item]);
      }

      return data;
    },

    /**
     * Если необходимо, можно переопределить в компоненте, чтобы динамически указать поля
     * @returns {null} || []
     */
    form__getFactFieldsNames() {
      return null;
    },
  },
};
