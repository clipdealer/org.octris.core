<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\auth\acl {
    /**
     * ACL role.
     *
     * @octdoc      c:acl/role
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class role {
        /**
         * Name of role.
         *
         * @octdoc  v:role/$name
         * @var     string
         */
        protected $name;
        /**/

        /**
         * Privileges assigned to role.
         *
         * @octdoc  v:role/$privileges
         * @var     array
         */
        protected $privileges = array();
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:role/__construct
         * @param   string          $name               The name of the role.
         */
        public function __construct($name)
        /**/
        {
            $this->name = $name;
        }

        /**
         * Add a privilege to the role.
         *
         * @octdoc  m:role/addPrivilege
         * @param   \org\octris\core\auth\acl\resource  $resource       Resource to grant privilege to.
         * @param   array                               $actions        Allowed actions.
         */
        public function addPrivilege(\org\octris\core\auth\acl\resource $resource, array $actions);
        /**/
        {
            $this->privileges[$resource->getName()] = array(
                'resource' => $resource,
                'actions'  => $actions
            );
        }

        /**
         * Test whether a role may perform an specified action on a specified resource.
         *
         * @octdoc  m:role/isPrivileged
         * @param   \org\octris\core\auth\acl\resource  $resource       Resource to test.
         * @param   string                              $action         Action to test.
         * @return  bool                                                Returns true, if privilege is given.
         */
        public function isPrivileged(\org\octris\core\auth\acl\resource $resource, $action)
        /**/
        {
            $name = $resource->getName();

            return (isset($this->privileges[$name]) && in_array($action, $this->privileges[$name]['actions']));
        }
    }
}
