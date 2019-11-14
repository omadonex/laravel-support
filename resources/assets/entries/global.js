const GLOBAL = {};

GLOBAL.ssr = typeof window === undefined;
GLOBAL.window = GLOBAL.ssr ? {} : window;
GLOBAL.document = GLOBAL.ssr ? {} : document;

export default GLOBAL;