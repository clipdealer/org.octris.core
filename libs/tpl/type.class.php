<?php

namespace org\octris\core\tpl {
    /**
     * Core type class for template engine.
     *
     * @octdoc      c:tpl/type
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class type extends \org\octris\core\type
    /**/
    {
        /**
         * Cast input value to a specified type. In contrast to PHP's built-in settype function this one will try to cast arrays
         * and objects to the template collection type. Additionally this function supports casting explicitly to the template
         * collection type. All other types are handed over to PHP's built-in settype function.
         *
         * @octdoc  m:type/settype
         * @param   mixed       $val            Value to cast.
         * @param   string      $type           Type to cast to.
         * @return  mixed                       Casted value.
         */
        public static function settype($val, $type)
        /**/
        {
            $type = strtolower($type);
            
            if ($type == 'collection') {
                $val = new \org\octris\core\tpl\type\collection($val);
            } else {
                $val = parent::settype($val, $type);
            }
            
            return $val;
        }
    }
}