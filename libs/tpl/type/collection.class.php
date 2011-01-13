<?php

namespace org\octris\core\tpl\type {
    /**
     * Collection type specialized for template engine.
     *
     * @octdoc      c:type/collection
     * @copyright   copyright (c) 2010-2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class collection extends \org\octris\core\type\collection
    /**/
    {
        /**
         * Overwrite getIterator from parent class to return a special iterator containing meta-data required 
         * for usage in a template.
         *
         * @octdoc  m:collection/getIterator
         * @return  \org\octris\core\tpl\type\collection\iterator       Iterator instance.
         */
        function getIterator()
        /**/
        {
            return new collection\iterator($this->data);
        }
    }
}