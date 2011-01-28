<?php

/*
 * Copyright (c) 2011, Harald Lapp <harald.lapp@gmail.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Harald Lapp nor the names of its
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 */

namespace org\octris\core {
    /**
     * Implementation of a central registry uses DI container as storage
     *
     * @octdoc      c:core/registry
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald.lapp@gmail.com>
     */
    class registry extends \org\octris\core\type\container
    /**/
    {
        /**
         * Stores instance of registry object.
         *
         * @octdoc  v:registry/$instance
         * @var     \org\octris\core\registry
         */
        private static $instance = null;
        /**/
        
        /**
         * Constructor is protected to prevent instanciating registry.
         *
         * @octdoc  m:registry/__construct
         */
        protected function __construct()
        /**/
        {
        }
        
        /**
         * Clone is private to prevent multipleinstances of registry.
         *
         * @octdoc  m:registry/__clone
         */
        private function __clone()
        /**/
        {
        }

        /**
         * Return instance of registry.
         *
         * @octdoc  m:registry/getInstance
         * @return  \org\octris\core\registry           instance of registry
         */
        public function getInstance()
        /**/
        {
            if (is_null(self::$instance)) {
                self::$instance = new static();
            }
            
            return self::$instance;
        }
    }
}
