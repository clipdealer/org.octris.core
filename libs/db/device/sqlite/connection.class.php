<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\db\device\sqlite {
    /**
     * SQLite connection handler.
     *
     * @octdoc      c:sqlite/connection
     * @copyright   copyright (c) 2012 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class connection extends \SQLite3 implements \org\octris\core\db\device\connection_if
    /**/
    {
        /**
         * Constructor.
         *
         * @octdoc  m:connection/__construct
         * @param   array                       $options            Connection options.
         */
        public function __construct(array $options)
        /**/
        {
            parent::__construct($options['file'], $options['flags'], $options['key']);
        }

        /**
         * Initialize prepared statement.
         *
         * @octdoc  m:connection/prepare
         * @param   string                      $sql                SQL statement to use as prepared statement.
         * @return  \org\octris\core\db\sqlite\statement            Instance of prepared statement.
         */
        public function prepare($sql)
        /**/
        {
            return new \org\octris\core\db\sqlite\statement($this, $sql);
        }
    }
}
