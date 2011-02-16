<?php

namespace org\octris\core\project\app {
    use \org\octris\core\app as app;
    use \org\octris\core\config as config;
    use \org\octris\core\app\cli\stdio as stdio;
    use \org\octris\core\validate as validate;
    
    /**
     * Create new project.
     *
     * Usage:
     *      create project [company=...] [author=...] [email=...]
     *
     * @octdoc      c:app/clear
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    class create extends \org\octris\core\octsh\libs\plugin
    /**/
    {
        /**
         * The entry points to which the current page should allow requests to have to be defined with this
         * property.
         *
         * @octdoc  v:create/$next_page
         * @var     array
         */
        protected $next_pages = array();
        /**/

        /**
         * Name of project to install.
         *
         * @octdoc  v:create/$project
         * @var     string
         */
        protected $project = '';
        /**/

        /**
         * Command parameters.
         *
         * @octdoc  v:create/$param
         * @var     array
         */
        protected $param = array();
        /**/

        /**
         * Constructor.
         *
         * @octdoc  m:create/__construct
         */
        public function __construct()
        /**/
        {
            parent::__construct();
            
            $is_tty = posix_isatty(STDIN);

            $this->addValidator(
                'request',
                'create', 
                array(
                    'type'              => validate::T_OBJECT,
                    'keyrename'         => array('project'),
                    'properties'        => array(
                        'project'       => array(
                            'type'       => validate::T_CHAIN,
                            'chain'      => array(
                                array(
                                    'type'      => validate::T_PROJECT,
                                    'invalid'   => 'project name is invalid',
                                ),
                                array(
                                    'type'      => validate::T_CALLBACK,
                                    'callback'  => function($project, $validator) {
                                        $work = app::getPath(app::T_PATH_WORK, $project);
                                    
                                        if (!($return = !is_dir($work))) {
                                            $validator->addError('project does already exist');
                                        }

                                        return $return;
                                    }
                                )
                            ),
                            'required'  => 'usage: create <name-of-project>, example: create org.octris.example'
                        ),
                        'company'   => array(
                            'type'      => validate::T_PRINTABLE,
                            'invalid'   => 'Company name is invalid',
                            'required'  => ($is_tty ? NULL : 'Company name is required')
                        ),
                        'author'    => array(
                            'type'      => validate::T_PRINTABLE,
                            'invalid'   => 'Author name is invalid',
                            'required'  => ($is_tty ? NULL : 'Author name is required')
                        ),
                        'email'     => array(
                            'type'      => validate::T_PRINTABLE,
                            'invalid'   => 'E-mail address is invalid',
                            'required'  => ($is_tty ? NULL : 'E-mail address is required')
                        )
                    )
                )
            );
        }

        /**
         * Helper method to test whether a file is binary or text file.
         *
         * @octdoc  m:clear/isBinary
         * @param   string          $file               File to test.
         * @param   string          $size               Optional block size to test.
         * @return  bool                                Returns true for binaries.
         */
        protected function isBinary($file, $size = 512)
        /**/
        {
            $return = false;

            if (is_file($file) && ($fp = fopen($file, 'r'))) {
                $blk = fread($fp, $size);
                fclose($fp); 

                clearstatcache();

                $return = (
                    substr_count($blk, '^ -~', "^\r\n") / $size > 0.3 ||
                    substr_count($blk, "\x00") > 0 
                ); 
            }

            return $return;
        }

        /**
         * Prepare page
         *
         * @octdoc  m:create/prepare
         * @param   \org\octris\core\app\page       $last_page      Instance of last called page.
         * @param   string                          $action         Action that led to current page.
         * @return  mixed                                           Returns either page to redirect to or null.
         */
        public function prepare(\org\octris\core\app\page $last_page, $action)
        /**/
        {
            list($is_valid, $data, $errors) = $this->applyValidator('request', $action);
            
            if (!$is_valid) {
                $last_page->addErrors($errors);
                
                return $last_page;
            }

            // handle project configuration
            $prj = new config('org.octris.core', 'project.create');

            $prj->defaults(array(
                'info.company' => (isset($data['company']) ? $data['company'] : ''),
                'info.author'  => (isset($data['author']) ? $data['author'] : ''),
                'info.email'   => (isset($data['email']) ? $data['email'] : '')
            ));

            if ($domain != '') {
                $prj['info.domain'] = $domain;
            }

            // collect information and create configuration for new project
            $filter = $prj->filter('info');

            foreach ($filter as $k => $v) {
                $prj[$k] = stdio::getPrompt(sprintf("%s [%%s]: ", $k), $v);
            }

            // $prj->save();
        }

        /**
         * Validate parameters.
         *
         * @octdoc  m:help/validate
         * @param   string                          $action         Action that led to current page.
         * @return  
         */
        public function validate()
        /**/
        {
            return true;
        }

        /**
         * Implements dialog.
         *
         * @octdoc  m:create/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
        }
    }
}

// process commandline parameters
// if ($_GET->validate(
//     'p', 
//     \org\octris\core\validate::T_PATTERN, 
//     array('pattern' => )
// )) {
//     $tmp    = explode('.', $_GET['p']->value);
//     $module = array_pop($tmp);
//     $domain = implode('.', array_reverse($tmp));
// } else {
//     $module = '';
//     $domain = '';
// }
// 
// // initialization
// use \org\octris\core\app\cli as cli;
// use \org\octris\core\app\cli\stdio as stdio;
// use \org\octris\core\config as config;
// use \org\octris\core\tpl as tpl;
// 
// print "\n";
// $module = stdio::getPrompt('module [%s]: ', $module, true);
// $year   = stdio::getPrompt('year [%s]: ', date('Y'), true);
// 
// if ($module == '' || $year == '') {
//     die("'module' and 'year' are required!\n");
// }
// 
// $ns = implode('\\', array_reverse(explode('.', $prj['info.domain']))) . '\\' . $module;
// 
// $data = array_merge($prj->filter('info')->getArrayCopy(true), array(
//     'year'      => $year,
//     'module'    => $module,
//     'namespace' => $ns,
//     'directory' => str_replace('\\', '.', $ns)
// ));
// 
// // setup destination directory
// $dir = cli::getPath(cli::T_PATH_WORK, '') . '/' . $data['directory'];
// 
// if (is_dir($dir)) {
//     die(sprintf("there seems to be already a project at '%s'\n", $dir));
// }
// 
// // process skeleton and write project files
// $tpl = new tpl();
// $tpl->addSearchPath(__DIR__ . '/data/skel/web/');
// $tpl->setValues($data);
// 
// $box = new tpl\sandbox();
// 
// $src = __DIR__ . '/data/skel/web/';
// $len = strlen($src);
// 
// mkdir($dir, 0755);
// 
// $directories = array();
// $iterator    = new RecursiveIteratorIterator(
//     new RecursiveDirectoryIterator($src)
// );
// 
// foreach ($iterator as $filename => $cur) {
//     $rel  = substr($filename, $len); 
//     $dst  = $dir . '/' . $rel;
//     $path = dirname($dst);
//     $base = basename($filename);
//     $ext  = preg_replace('/^\.?[^\.]+?(\..+|)$/', '\1', $base);
//     $base = basename($filename, $ext);
//     
//     if (substr($base, 0, 1) == '$' && isset($data[$base = ltrim($base, '$')])) {
//         // resolve variable in filename
//         $dst = $path . '/' . $data[$base] . $ext;
//     }
//     
//     if (!is_dir($path)) {
//         // create destination directory
//         mkdir($path, 0755, true);
//     }
//     
//     if (!is_binary($filename)) {
//         $cmp = $tpl->fetch($rel, tpl\sandbox::T_CONTEXT_TEXT);
// 
//         file_put_contents($dst, $cmp);
//     } else {
//         copy($filename, $dst);
//     }
// }
// 
// print "done.\n";
