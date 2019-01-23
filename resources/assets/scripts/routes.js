import cloneDeep from 'clone-deep';

/**
 * https://github.com/tightenco/ziggy
 * выгружает Laravel Routes и добавляет их window.route
 */
// клонируем для последующего экспорта
const Routes = cloneDeep(window.route);
// удаляем оригинальную ф-ю
window.route = null;
delete window.route;

class RoutesUtils {
  fUpCase(str) {
    return `${str.charAt(0).toUpperCase()}${str.slice(1)}`;
  }

  getCamelName(dotName) {
    const dotParts = dotName.split(".");
    const countParts = dotParts.length;
    let name = dotParts[0];

    for (let i = 1; i < countParts; i += 1) {
      name += this.fUpCase(dotParts[i]);
    }

    return name;
  }

  generate(routesInfo, module) {
    let data = [];
    routesInfo.forEach((routeInfo) => {
      let routeName = module ? `${module}.${routeInfo.name}` : routeInfo.name;
      let path = Routes(routeName, null, false).url();

      let breadcrumbs = [];
      routeInfo.breadcrumbs.forEach((breadcrumbRouteName) => {
        let routeName = module ? `${module}.${breadcrumbRouteName}` : breadcrumbRouteName;
        breadcrumbs.push({
          module: module,
          name: routeName,
          nameBase: breadcrumbRouteName,
          page: this.getCamelName(routeName),
        });
      });

      data.push({
        path: path,
        component: routeInfo.component,
        name: routeName,
        meta: {
          module: module,
          nameBase: routeInfo.name,
          page: this.getCamelName(routeName),
          breadcrumbs: breadcrumbs,
          auth: routeInfo.auth,
          acl: routeInfo.acl,
        }
      });
    });

    return data;
  };
}

export { Routes, RoutesUtils };
