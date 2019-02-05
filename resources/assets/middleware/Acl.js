import Middleware from "../classes/Middleware";

class AclMiddleware extends Middleware {
  static get key() {
    return 'acl';
  }

  handle($root, actions) {
    if ((actions.roles && !$root.acl__checkRoles(actions.roles))
      || (actions.privileges && !$root.acl__check(actions.privileges))) {
      return { name: "app.error.404" };
    }

    return true;
  }
}

export default AclMiddleware;