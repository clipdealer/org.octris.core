<?php

namespace {{$namespace}}\app {
    /**
     * Entry page.
     *
     * @octdoc      c:app/entry
     * @copyright   copyright (c) {{$year}} by {{$company}}
     * @author      {{$author}} <{{$email}}>
     */
    class entry extends \org\octris\core\app\page
    /**/
    {
        /**
         * The entry points to which the current page should allow requests to have to be defined with this
         * property.
         *
         * @octdoc  v:entry/$next_page
         * @var     array
         */
         protected $next_pages = array(
             'default' => '{{$namespace}}\app\index',
         );
        /**/

        /**
         * The constructor is used to setup common settings for example validation rulesets must be defined 
         * through the page object constructor.
         *
         * @octdoc  m:entry/__construct
         * @param   \org\octris\core\app    $app    Instance of application.
         */
        public function __construct(\org\octris\core\app $app) 
        /**/
        {
            parent::__construct($app);
        }

        /**
         * Prepare rendering of a page. this method is called _BEFORE_ rendering a page.
         *
         * @octdoc  m:entry/prepareRender
         * @param   \org\octris\core\app            $app        Instance of application.
         * @param   \org\octris\core\app\page       $last_page  Instance of the page that was active before this one
         * @return  null|\org\octris\core\app\page              A page can be returned.
         */
        public function prepareRender(\org\octris\core\app $app, \org\octris\core\app\page $last_page, $action)
        /**/
        {
            
        }

        /**
         * This method is used to populate a template with data and render it. This method should never be reached for
         * the entry page. Otherwise the application is propably broken.
         *
         * @octdoc  m:entry/render
         * @param   \org\octris\core\app            $app        Instance of application.
         */
        public function render(\org\octris\core\app $app)
        /**/
        {
            die('error!');
        }
    }
}
