<?php

namespace org\octris\core\octsh\app {
    /**
     * Implements installer for applications based on octris framework.
     *
     * @octdoc      c:app/install
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class install extends \org\octris\core\octsh\libs\plugin
    /**/
    {
        /**
         * The entry points to which the current page should allow requests to have to be defined with this
         * property.
         *
         * @octdoc  v:install/$next_page
         * @var     array
         */
        protected $next_pages = array();
        /**/

        /**
         * Prepare page
         *
         * @octdoc  m:install/prepare
         * @param   \org\octris\core\app\page       $last_page      Instance of last called page.
         * @param   string                          $action         Action that led to current page.
         * @return  mixed                                           Returns either page to redirect to or null.
         */
        public function prepare(\org\octris\core\app\page $last_page, $action)
        /**/
        {
        }

        /**
         * Implements dialog.
         *
         * @octdoc  m:install/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
        }
    }
}

// install:: createdirs
//  @echo $(CURSYMDIR)
//  @echo $(project)
//  @ln -snf $(CURSYMDIR)/data              ../../data/$(project)
//  @ln -snf $(CURSYMDIR)/etc               ../../etc/$(project)
//  @ln -snf $(CURSYMDIR)/libs              ../../libs/$(project)
//  @ln -snf $(CURSYMDIR)/locale            ../../locale/$(project)
//  @ln -snf $(CURSYMDIR)/templates         ../../templates/$(project)
//  @ln -snf $(CURSYMDIR)/tools             ../../tools/$(project)
//  @if [ "$(project)" != "org.octris.core" ]; then \
//      if [ -e $(CURSYMDIR)/host/index.php ]; then \
//          ln -snf $(CURSYMDIR)/host/index.php                 ../../host/$(project)/index.php; \
//      fi; \
//      if [ -d $(CURSYMDIR)/host/robots.txt ]; then \
//          ln -snf $(CURSYMDIR)/host/robots.txt                ../../host/$(project)/robots.txt; \
//      fi; \
//      if [ -d $(CURSYMDIR)/host/error ]; then \
//          ln -snf $(CURSYMDIR)/host/error                     ../../host/$(project)/error; \
//      fi; \
//      if [ -d $(CURSYMDIR)/resources ]; then \
//          ln -snf $(CURSYMDIR)/resources                      ../../host/$(project)/resources; \
//      fi; \
//  fi; \
// 
//  @if [ "$(project)" = "org.octris.core" ]; then \
//      mkdir -p ../../cache; \
//      mkdir -p ../../data; \
//      mkdir -p ../../etc; \
//      mkdir -p ../../host; \
//      mkdir -p ../../libs; \
//      mkdir -p ../../locale; \
//      mkdir -p ../../log; \
//      mkdir -p ../../templates; \
//      mkdir -p ../../tools; \
//  elif [ ! -d ../../host ]; then \
//      echo ""; \
//      echo "You have to install 'org.octris.core' first!"; \
//      echo ""; \
//      exit 1; \
//  elif [ -d $(CURSYMDIR)/host ]; then \
//      mkdir -p ../../host/$(project); \
//      mkdir -p -m 0777 ../../host/$(project)/libsjs; \
//      mkdir -p -m 0777 ../../host/$(project)/styles; \
//      mkdir -p -m 0777 ../../log/$(project); \
//  fi; \
//  mkdir -p -m 0777 ../../cache/$(project)/data; \
//  mkdir -p -m 0777 ../../cache/$(project)/templates_c; \
