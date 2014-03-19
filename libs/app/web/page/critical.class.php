<?php

/*
 * This file is part of the 'org.octris.core' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace org\octris\core\app\web\page {
    /**
     * Special page for handling critical errors.
     *
     * @octdoc      c:page/critical
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class critical extends \org\octris\core\app\web\page
    /**/
    {
        /**
         * Template filename of page for rendering critical error information.
         *
         * @octdoc  p:critical/$template
         * @type    string
         */
        protected $template = 'critical.html';
        /**/

        /**
         * Instance of a logger.
         *
         * @octdoc  p:critical/$logger
         * @type    \org\octris\core\logger
         */
        private $logger = null;
        /**/

        /**
         * Identifier to print on the webpage. The identifier may be send by a
         * user to the support. On the one hand it helps communicating between
         * user and support, on the other hand the identifier helps to locate
         * the error in the logging backend.
         *
         * @octdoc  p:critical/$identifier
         * @type    string
         */
        private $identifier = '';
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:critical/__construct
         */
        public function __construct()
        /**/
        {
            parent::__construct();
        }

        /**
         * Configure a logger instance to log critical exception to.
         *
         * @octdoc  m:critical/setLogger
         * @param   \org\octris\core\logger     $logger         Logger instance.
         */
        public function setLogger(\org\octris\core\logger $logger)
        /**/
        {
            $this->logger = $logger;
        }

        /**
         * Set exception to handle.
         *
         * @octdoc  m:critical/setException
         * @param   \Exception                  $exception      Exception to handle.
         * @param   array                       $data           Additional data to include in error report.
         */
        public function setException(\Exception $exception, array $data = array())
        /**/
        {
            $this->identifier = base64_encode(uniqid(gethostname() . '.', true));

            throw $exception;

            if (!is_null($this->logger)) {
                try {
                    $this->logger->log(
                        \org\octris\core\logger::T_CRITICAL,
                        $exception,
                        array(
                            '_identifier' => $this->identifier
                        )
                    );
                } catch(\Exception $e) {
                }
            }
        }

        /**
         * Implements abstract prepare methof of parent class.
         *
         * @octdoc  m:critical/prepare
         * @param   \org\octris\core\app\page       $last_page      Instance of last called page.
         * @param   string                          $action         Action that led to current page.
         * @return  mixed                                           Returns either page to redirect to or null.
         */
        public function prepare(\org\octris\core\app\page $last_page, $action)
        /**/
        {
        }

        /**
         * Renders critical error page.
         *
         * @octdoc  m:critical/render
         */
        public function render()
        /**/
        {
            $tpl = $this->getTemplate();
            $tpl->setValue('identifier', $this->identifier);
            $tpl->render($this->template);
        }
    }
}
