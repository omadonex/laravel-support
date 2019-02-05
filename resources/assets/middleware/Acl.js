import Middleware from "../classes/Middleware";

class AclMiddleware extends Middleware {
  static get key() {
    return 'acl';
  }

  handle($root, next, actions) {
    if ((actions.roles && !$root.acl__checkRoles(actions.roles))
      || (actions.privileges && !$root.acl__check(actions.privileges))) {
      next({ name: "app.error.404" });

      return false;
    }

    return true;
  }
}

export default AclMiddleware;