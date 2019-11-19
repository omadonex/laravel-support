import Routes from "../vendor/tightenco/ziggy/route";

const global = {};

global.ssr = typeof window === "undefined";
global.utils = {};

if (global.ssr) {
  global.window = {};
  global.window.location = context.location;
  global.document = {};
  global.csrfToken = context.csrfToken;

  global.appData = context.appData;
} else {
  global.window = window;
  global.document = document;
  const meta = document.head.querySelector('meta[name="csrf-token"]');
  global.csrfToken = meta ? meta.content : null;

  global.appData = window.appData;
}

global.route = {};
global.route.Routes = Routes;
global.route.data = global.appData.vendor.ziggy;

export {global};