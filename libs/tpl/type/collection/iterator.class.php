<?php

namespace org\octris\core\tpl\type\collection {
    /****c* collection/iterator
     * NAME
     *      iterator
     * FUNCTION
     *      implements functionality for iterating a tpl-type collection
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class iterator extends \ArrayIterator {
        /****m* iterator/getItem
         * SYNOPSIS
         */
        protected function getItem($pos)
        /*
         * FUNCTION
         *      get item in current position
         * INPUTS
         *      * $pos (int) -- position to return item for
         * OUTPUTS
         *      (mixed) item value
         ****
         */
        {
            $item = $this->values[$pos];
        
            switch (gettype($item)) {
            case 'array':
                $item = new self($item);
                break;
            case 'object':
                $item = new self((array)$item);
                break;
            }
        
            return (object)array(
                'item'      => $item,
                'key'       => $this->keys[$pos],
                'pos'       => $pos,
                'count'     => $this->count,
                'is_first'  => ($pos == 0),
                'is_last'   => ($pos == $this->count - 1)
            );
        }
    }
}
