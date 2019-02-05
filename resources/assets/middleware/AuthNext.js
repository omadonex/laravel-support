import Middleware from "../classes/Middleware";

class AuthNextMiddleware extends Middleware {
  static get key() {
    return 'auth';
  }

  handle($root, actions) {
    if (!$root.LoggedIn) {
      return { name: "login" };
    }

    return true;
  }
}

export default AuthNextMiddleware;