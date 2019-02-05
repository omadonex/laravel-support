import Middleware from "../classes/Middleware";

class AuthMiddleware extends Middleware {
  static get key() {
    return 'auth';
  }

  handle($root, next, actions) {
    if (!$root.loggedIn) {
      next({ name: "login" });

      return false;
    }

    return true;
  }
}

export default AuthMiddleware;