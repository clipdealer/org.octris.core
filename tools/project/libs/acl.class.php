<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\project\libs {
    /**
     * Extends OCTRiS ACLs for manipulation using 'updateacl' tool
     *
     * @octdoc      c:app/acl
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class acl extends \org\octris\core\auth\acl
    /**/
    {
        /**
         * "Merge" resources, that means: remove all resources, that are not present in
         * the specified list of resources and add new resources for entries from the
         * specified list, that do not already exist.
         *
         * @octdoc  m:acl/diffResources
         * @param   array       $resources      List of resources to include / exclude.
         */
        public function mergeResources(array $resources)
        /**/
        {
            // remove not longer existing resources
            $tmp = $this->resources;

            array_map(function($k) use (&$tmp, $resources) {
                if (!isset($resources[$k])) {
                    unset($tmp[$k]);
                }
            }, array_keys($tmp));

            // add new resources
            foreach ($resources as $k => $v) {
                if (!isset($tmp[$k])) {
                    $tmp[$k] = new \org\octris\core\auth\acl\resource($v['name'], $v['actions']);
                }
            }

            // set updates list
            ksort($tmp);

            $this->resources = $tmp;
        }

        /**
         * "Merge" roles, that means: remove all roles, that are not present in
         * the specified list of roles and add new rolesfor entries from the
         * specified list, that do not already exist.
         *
         * @octdoc  m:acl/mergeRoles
         * @param   array       $roles          List of roles to include / exclude.
         */
        public function mergeRoles(array $roles)
        /**/
        {
            // remove not longer existing roles
            $tmp = $this->roles;

            array_map(function($k) use (&$tmp, $roles) {
                if (!isset($roles[$k])) {
                    unset($tmp[$k]);
                }
            }, array_keys($tmp));

            // add new roles
            foreach ($roles as $k => $v) {
                if (!isset($tmp[$k])) {
                    $tmp[$k] = new \org\octris\core\auth\acl\role($v['name']);
                }
            }

            // set updates list
            ksort($tmp);

            $this->roles = $tmp;
        }
    }
}
