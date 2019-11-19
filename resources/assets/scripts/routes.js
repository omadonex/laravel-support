import { getCamelName } from "./helpers";

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
      let path = omx.global.route.Routes(routeName, params, false, omx.global.route.data).url();
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

export default RoutesUtils;
