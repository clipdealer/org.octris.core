<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\app {
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
         * Constructor.
         *
         * @octdoc  m:acl/__construct
         */
        public function __construct()
        /**/
        {
        }

        /**
         * Add a resource to the ACL. In context of the octris framework a resource is
         * a PHP namespace or a PHP class name, typically the namespace of a module or
         * the class name of a page. The actions array can be for example the next
         * actions allowed for a page specified as resource.
         *
         * @octdoc  m:acl/addResource
         * @param   string          $resource               Resource to add.
         * @param   array           $actions                Actions the resource can perform.
         * @return  \org\octris\core\auth\acl\resource      Instance of an ACL resource.
         */
        public function addResource($resource, array $actions)
        /**/
        {
            return $this->resources[$resource] = new \org\octris\core\auth\acl\resource($resource, $actions);
        }

        /**
         * Add a role to the ACL.
         *
         * @octdoc  m:acl/addRole
         * @param   string          $name                   Name of the role.
         * @return  \org\octris\core\auth\acl\role          Instance of an ACL role.
         */
        public function addRole($name)
        /**/
        {
            return $this->roles[$name] = new \org\octris\core\auth\acl\role($name);
        }

        /**
         * Test whether a specified role is authorized for some action on a resource.
         *
         * @octdoc  m:acl/isAuthorized
         * @param   string          $role                   Name of role.
         * @param   string          $action                 Name of action.
         * @param   string          $resource               Name of resource.
         */
        public function isAuthorized($role, $action, $resource)
        /**/
        {
            if (($return = (isset($this->roles[$role]) && isset($this->resources[$resource])))) {
                if (($return = $this->resources[$resource]->hasAction($action))) {
                    $return = $this->roles[$role]->isPrivileged($this->resource[$resource], $action);
                }
            }

            return $return;
        }
    }
}
