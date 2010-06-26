<?php

namespace org\octris\core\tpl\type {
    /****c* type/collection
     * NAME
     *      collection
     * FUNCTION
     *      collection type special for template engine
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class collection extends \org\octris\core\type\collection {
        /****m* collection/getIterator
         * SYNOPSIS
         */
        function getIterator()
        /*
         * FUNCTION
         *      overwrite getIterator from parent class to return a special 
         *      iterator containing meta-data useful for usage in a template
         * OUTPUTS
         *      (iterator) -- iterator object
         ****
         */
        {
            return new collection\iterator($this->data);
        }
    }
}