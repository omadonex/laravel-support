import Middleware from "../classes/Middleware";

class AuthRedirectMiddleware extends Middleware {
  static get key() {
    return 'auth';
  }

  handle($root, actions) {
    if (!$root.auth__loggedIn) {
      return $root.route('login');
    }

    return true;
  }
}

export default AuthRedirectMiddleware;