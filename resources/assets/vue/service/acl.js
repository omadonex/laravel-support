const AclService = {
  data() {
    return {
      Data__acl: {
        roles: {
          ROOT: 'root',
          USER: 'user',
        },
        privileges: {},
      },
    };
  },

  methods: {
    /**
     * По умолчанию в Acl доступно 2 роли Root и User и 0 привилегий
     * Эта функция позволяет использовать конфиг с дополнительными ролями и привилегиями, настроенными под приложение
     * Она объединяет эти данные с данными по умолчанию, используется в entries/pages.js
     * @param config
     */
    acl__useConfig(config) {
      for (let role in config.roles) {
        this.Data__acl.roles[role] = config.roles[role];
      }
      this.Data__acl.privileges = config.privileges;
    },

    /**
     * Проверка ролей
     * Проверяет наличие ВСЕХ запрошенных ролей у текущего пользователя
     * Если отсутствует хотя бы одна, то возвращает false
     * @param roles - array | string
     * @returns {boolean}
     */
    acl__hasRoles(roles) {
      let checkRoles = Array.isArray(roles) ? roles : new Array(roles);
      let userRoles = this.$root.DataUser.roles.map(item => item.id);

      let countRoles = checkRoles.length;
      checkRoles = checkRoles.filter(value => -1 !== userRoles.indexOf(value));

      return checkRoles.length === countRoles;
    },

    /**
     * Проверяет текущий пользователь Root или нет
     * @returns {*|boolean}
     */
    acl__isRoot() {
      return this.acl__hasRoles(this.Data__acl.roles.ROOT);
    },

    /**
     * Проверяет текущий ользователь User или нет
     * @returns {*|boolean}
     */
    acl__isUser() {
      return this.acl__hasRoles(this.Data__acl.roles.USER);
    },

    /**
     * Проверка привилегий
     * Проверяет наличие ВСЕХ запрошенных привилегий у текущего пользователя
     * Если отсутствует хотя бы одна, то возвращает false
     * У Root считаем, что есть все привилегии
     *
     * @param privileges - array | string
     * @returns {*}
     */
    acl__check(privileges) {
      if (!this.$root.auth__loggedIn) {
        return false;
      }

      if (this.acl__isRoot()) {
        return true;
      }

      let checkPrivileges = Array.isArray(privileges) ? privileges : new Array(privileges);
      let userPrivileges = this.$root.DataUser.privileges.map(item => item.id);

      let countPrivileges = checkPrivileges.length;
      checkPrivileges = checkPrivileges.filter(value => -1 !== userPrivileges.indexOf(value));

      return checkPrivileges.length === countPrivileges;
    },

    /**
     * Комбинированная проверка ролей
     * Если передан массив то работает как ИЛИ для всех вхождений массива
     * Если вхождение массива тоже является массивом, то для него работает правило И
     * Например, [['a', 'b'], ['b', 'c'], 'd'] - вернет true если текущий пользователь имеет роли 'a' и 'b' ИЛИ 'b' и 'с' ИЛИ 'd'
     * Если передан массив строк или просто строка, то работает также как и acl__hasRoles
     * Последний параметр отвечает строгому режиму для ROOT, если не передан, то считается, что ROOT обладает всеми нужными ролями и возвращает true
     * @param rolesCombined - array of array | array | string
     * @param rootStrict - false | true
     * @returns {*}
     */
    acl__checkRoles(rolesCombined, rootStrict) {
      if (!this.$root.auth__loggedIn) {
        return false;
      }

      if (!rootStrict && this.acl__isRoot()) {
        return true;
      }

      let checkRolesCombined = Array.isArray(rolesCombined) ? rolesCombined : new Array(rolesCombined);

      for (let roles in checkRolesCombined) {
        if (this.acl__hasRoles(checkRolesCombined[roles])) {
          return true;
        }
      }

      return false;
    },
  },
};

export default {
  install(Vue, options) {
    Vue.mixin(AclService);
  },
};