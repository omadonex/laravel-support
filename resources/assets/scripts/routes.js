import cloneDeep from 'clone-deep';
import { getCamelName } from "./helpers";

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
  generate(routesInfo, module) {
    let data = [];

    routesInfo.forEach((routeInfo) => {
      let routeName = module ? `${module}.${routeInfo.name}` : routeInfo.name;
      let params = (routeInfo.params !== undefined) ? routeInfo.params : {};
      let dynamic = (routeInfo.dynamic !== undefined) ? routeInfo.dynamic : [];
      dynamic.forEach((item) => {
        params[item] = `dynamic_${item}`;
      });
      let path = Routes(routeName, params, false).url();
      dynamic.forEach((item) => {
        path = path.replace(`dynamic_${item}`, `:${item}`);
      });

      let breadcrumbs = [];
      routeInfo.breadcrumbs.forEach((breadcrumbRouteName) => {
        let routeName = module ? `${module}.${breadcrumbRouteName}` : breadcrumbRouteName;
        breadcrumbs.push({
          module: module,
          name: routeName,
          nameBase: breadcrumbRouteName,
          page: getCamelName(routeName),
        });
      });

      data.push({
        path: path,
        component: routeInfo.component,
        name: routeName,
        meta: {
          module: module,
          nameBase: routeInfo.name,
          page: getCamelName(routeName),
          breadcrumbs: breadcrumbs,
          middleware: routeInfo.middleware,
          actions: routeInfo.actions,
        },
      });
    });

    return data;
  };
}

export { Routes, RoutesUtils };
