<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\auth {
    /**
     * Simple implementation of access control lists.
     *
     * @octdoc      c:app/acl
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class acl
    /**/
    {
        /**
         * Configured access control lists.
         *
         * @octdoc  v:acl/$resources
         * @var     array
         */
        protected $resources = array();
        /**/

        /**
         * Roles configured in ACL.
         *
         * @octdoc  v:acl/$roles
         * @var     array
         */
        protected $roles = array();
        /**/

        /**
         * Instance of authentication library.
         *
         * @octdoc  v:acl/$auth
         * @var     \org\octris\core\auth
         */
        protected $auth;
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:acl/__construct
         * @param   \org\octris\core\auth   $auth           Instance of authentication library.
         */
        public function __construct(\org\octris\core\auth $auth)
        /**/
        {
            $this->auth = $auth;
        }

        /**
         * Add a resource to the ACL. In context of the octris framework a resource is
         * a PHP namespace or a PHP class name, typically the namespace of a module or
         * the class name of a page. The actions array can be for example the next
         * actions allowed for a page specified as resource.
         *
         * @octdoc  m:acl/addResource
         * @param   string                              $name       Name of resource to add.
         * @param   array                               $actions    Actions the resource can perform.
         * @return  \org\octris\core\auth\acl\resource              Instance of an ACL resource.
         */
        public function addResource($name, array $actions)
        /**/
        {
            return $this->resources[$name] = new \org\octris\core\auth\acl\resource($name, $actions);
        }

        /**
         * Add a role to the ACL.
         *
         * @octdoc  m:acl/addRole
         * @param   string                          $name       Name of the role.
         * @param   \org\octris\core\auth\acl\role  $parent     Optional role to inherit.
         * @return  \org\octris\core\auth\acl\role              Instance of an ACL role.
         */
        public function addRole($name, \org\octris\core\auth\acl\role $parent = null)
        /**/
        {
            return $this->roles[$name] = new \org\octris\core\auth\acl\role($name, $parent);
        }

        /**
         * Test whether a specified role is authorized for some action on a resource
         * and if the authenticated identity is member of the specified role.
         *
         * @octdoc  m:acl/isAuthorized
         * @param   string          $role                   Name of role.
         * @param   string          $action                 Name of action.
         * @param   string          $resource               Name of resource.
         */
        public function isAuthorized($role, $action, $resource)
        /**/
        {
            $return = false;

            do {
                if (!isset($this->roles[$role]) || !isset($this->resources[$resource])) {
                    // there is no such role or resource available
                    break;
                }

                if (!($identity = $this->auth->getIdentity())) {
                    // no identity in authentication object available
                    break;
                }

                if (!$identity->isValid()) {
                    if ($role != 'guest') {
                        // identity is not validly authenticated and the role to authorize is no guest role.
                        break;
                    }
                } else {
                    // TODO: roles from identity ... compare with specified role
                    break;
                }

                if ($this->resources[$resource]->hasAction($action)) {
                    // test if role is privileged
                    $return = $this->roles[$role]->isPrivileged($this->resource[$resource], $action);
                }
            } while(false);

            return $return;
        }
    }
}
