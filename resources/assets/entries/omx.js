const global = {};

global.ssr = typeof window === "undefined";
global.window = global.ssr ? {} : window;
global.document = global.ssr ? {} : document;

export {global};