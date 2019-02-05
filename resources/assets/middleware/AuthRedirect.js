import Middleware from "../classes/Middleware";

class AuthRedirectMiddleware extends Middleware {
  static get key() {
    return 'auth';
  }

  handle($root, actions) {
    if (!$root.appLoggedIn) {
      return $root.route('login');
    }

    return true;
  }
}

export default AuthRedirectMiddleware;