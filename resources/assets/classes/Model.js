class Model {
  static get objSeparator() {
    return '__';
  }

  /**
   * Функция упаковки данных. На вход принимает сформированные данные, которые передаются в AjaxForm
   * Fillable поля, в названии которых присутствует `objSeparator` превращается в объекты, с заданным уровнем вложенности
   * Используется только при передаче данных на сервер в AjaxForm, для удобства манипуляции ими на сервере
   * @param data
   * @returns {*}
   */
  static packData(data) {
    for (let field in this.fillable) {
      if (this.fillable[field].obj) {
        let splitFieldArr = field.split(this.objSeparator);
        let obj = data;
        for (let i = 0; i < splitFieldArr.length; i++) {
          let key = splitFieldArr[i];

          if (i === splitFieldArr.length - 1) {
            obj[key] = data[field];
          } else {
            if (!obj.hasOwnProperty(key)) {
              obj[key] = {};
            }
            obj = obj[key];
          }
        }
        delete data[field];
      }
    }

    return data;
  }
}

export default Model;