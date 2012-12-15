<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\device\riak {
    /**
     * Riak data object
     *
     * @octdoc      c:riak/subobject
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class subobject extends \org\octris\core\db\type\subobject
    /**/
    {
        /**
         * Cast values to Riak specific types.
         *
         * @octdoc  m:subject/castTo
         */
        protected function castTo($value)
        /**/
        {
            if (is_object($value) && !($value instanceof self)) {
                if ($value instanceof \org\octris\core\type\number) {
                    $return = ($value->isDecimal()
                                ? (float)(string)$value
                                : (string)$value);
                } elseif ($value instanceof \org\octris\core\type\money) {
                    $return = (float)(string)$value;
                } elseif ($value instanceof \DateTime) {
                    $return = explode('.', $value->format('Y.m.d H:M:S'));
                } elseif ($value instanceof \org\octris\core\db\device\riak\ref) {
                    $return = $value;
                } else {
                    $return = (string)$value;
                }
            } else {
                $return = $value;
            }

            return $return;
        }

        /**
         * Cast values from Riak to PHP types.
         *
         * @octdoc  m:subject/castFrom
         */
        public function castFrom($value)
        /**/
        {
            return $value;
        }

        /**
         * Create a new instance of subobject.
         *
         * @octdoc  a:subject/createSubObject
         * @param   array           $data                   Data to create subobject from.
         * @return  \org\octris\core\db\type\subobject      Created subobject.
         */
        public function createSubObject(array $data)
        /**/
        {
            return new self($data);
        }
    }
}
