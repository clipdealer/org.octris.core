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
         * the specified list of resources, add new resources for entries from the
         * specified list, that do not already exist and remove policies for resources,
         * that do not longer exist.
         *
         * @octdoc  m:acl/merge
         * @param   array       $resources      List of resources to include / exclude.
         * @return  array                       Status information about removed / added resources, policies, etc.
         */
        public function merge(array $resources)
        /**/
        {
            $result = array(
                'del_resources' => 0,       // resources deleted
                'add_resources' => 0,       // resources added
                'del_policies'  => 0,       // policies deleted
                'prc_roles'     => 0,       // processed roles
                'tot_resources' => 0,       // total resources
                'tot_policies'  => 0,       // total policies
                'tot_roles'     => 0,       // total roles
            );

            // remove not longer existing resources
            $tmp = $this->resources;

            array_map(function($k) use (&$tmp, &$result, $resources) {
                if (!isset($resources[$k])) {
                    unset($tmp[$k]);
                    ++$result['del_resources'];
                }
            }, array_keys($tmp));

            // add new resources
            foreach ($resources as $k => $v) {
                if (!isset($tmp[$k])) {
                    $tmp[$k] = new \org\octris\core\auth\acl\resource($v['name'], $v['actions']);
                    ++$result['add_resources'];
                }
            }

            $result['tot_resources'] = count($tmp);

            ksort($tmp);

            $this->resources = $tmp;

            // remove not longer existing policies
            foreach ($this->roles as $role) {
                $reflection = new \ReflectionClass('\org\octris\core\auth\acl\role');
                $property   = $reflection->getProperty('policies');
                $property->setAccessible(true);

                $policies = $property->getValue($role);

                $flag = false;

                array_map(function($k) use (&$policies, &$result, &$flag, $resources) {
                    if (!isset($resources[$k])) {
                        unset($policies[$k]);
                        ++$result['del_policies'];
                        $flag = true;
                    }
                }, array_keys($policies));

                if ($flag) {
                    ++$result['prc_roles'];

                    $property->setValue($role, $policies);
                }

                $result['tot_policies'] += count($policies);
            }

            $result['tot_roles'] = count($this->roles);

            return $result;
        }

        /**
         * Save ACL.
         *
         * @octdoc  m:acl/save
         * @param   string              $filename           Filename to save ACL to.
         */
        public function save($filename)
        /**/
        {
            $result = array(
                'roles'     => array(),
                'resources' => array(),
                'policies'  => array()
            );

            // export roles
            foreach ($this->roles as $name => $role) {
                $reflection = new \ReflectionClass('\org\octris\core\auth\acl\role');
                $property   = $reflection->getProperty('parents');
                $property->setAccessible(true);

                $tmp = array_map(function($v) {
                    return (string)$v;
                }, $property->getValue($role));

                $result['roles'][] = array(
                    $name => $tmp
                );
            }

            // export resources
            foreach ($this->resources as $name => $resource) {
                $reflection = new \ReflectionClass('\org\octris\core\auth\acl\resource');
                $property   = $reflection->getProperty('actions');
                $property->setAccessible(true);

                $actions = $property->getValue($resource);

                $result['resources'][] = array(
                    $name => $actions
                );
            }

            // export policies
            foreach ($this->resources as $name => $resource) {
                $policy = $resource->getPolicy();

                $tmp = array(
                    'default' => ($policy == self::T_ALLOW
                                    ? 'ALLOW'
                                    : 'DENY')
                );

                $reflection = new \ReflectionClass('\org\octris\core\auth\acl\resource');
                $property   = $reflection->getProperty('actions');
                $property->setAccessible(true);

                if (count($actions = $property->getValue($resource)) > 0) {
                    $tmp['actions'] = array_map(function($v) {
                        return array($v => array());
                    }, $actions);

                    foreach ($this->roles as $role) {
                        foreach ($tmp['actions'] as &$action) {
                            $key = key($action);

                            if (!is_null($policy = $role->getPolicy($resource, $key))) {
                                $action[$key][(string)$role] = ($policy == self::T_ALLOW
                                                                ? 'ALLOW'
                                                                : 'DENY');
                            }
                        }
                    }
                }

                $result['resources'][] = array(
                    $name => $tmp
                );
            }

            file_put_contents($filename, yaml_emit($result));
        }
    }
}
