<?php

namespace org\octris\core\project\app {
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
         * @octdoc  v:clear/$next_page
         * @var     array
         */
        protected $next_pages = array(
            'execute'   => '\org\octris\core\project\app\create'
        );
        /**/

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
         * @octdoc  m:clear/prepare
         * @param   \org\octris\core\app\page       $last_page      Instance of last called page.
         * @param   string                          $action         Action that led to current page.
         * @return  mixed                                           Returns either page to redirect to or null.
         */
        public function prepare(\org\octris\core\app\page $last_page, $action)
        /**/
        {
            if ($action != 'execute') {
                return;
            }

            // perform project creation
            $prj = new config('org.octris.core', 'project.create');
            
        }

        /**
         * Validate create parameters.
         *
         * @octdoc  m:create/validate
         * @param   \org\octris\core\app\cli\page   $last_page      Instance of last called page.
         * @param   string                          $action         Action to select ruleset for.
         * @param   array                           $parameters     Parameters to validate.
         * @return  \org\octris\core\app\cli\page                   Returns page to display errors for.
         */
        public function validate(\org\octris\core\app\cli\page $last_page, $action, array $parameters = array())
        /**/
        {
            $project = array_shift($parameters);
            
            if (validate::test($project, validate::T_PROJECT)) {
                if (!is_dir(app::getPath(app::T_PATH_WORK, $project))) {
                    $last_page->addError("unable to locate project '$project'");
                } else {
                    $this->project = $project;
                }
            } else {
                $last_page->addError("usage: 'create project [company=...] [author=...] [email=...]'");
            }
            
            return (count($last_page->errors) == 0
                    ? null
                    : $last_page);
        }

        /**
         * Implements dialog.
         *
         * @octdoc  m:clear/dialog
         * @param   string                          $action         Action that led to current page.
         */
        public function dialog($action)
        /**/
        {
            $prj = new config('org.octris.core', 'project.create');

            $prj->defaults(array(
                'info.company' => '',
                'info.author'  => '',
                'info.email'   => '',
                'info.domain'  => $domain
            ));

            if ($domain != '') {
                $prj['info.domain'] = $domain;
            }

            // collect information and create configuration for new project
            $filter = $prj->filter('info');

            if (posix_isatty(STDIN)) {
                foreach ($filter as $k => $v) {
                    $prj[$k] = stdio::getPrompt(sprintf("%s [%%s]: ", $k), $v);
                }
            } else {
                
            }

            $prj->save();
        }
    }
}

// process commandline parameters
if ($_GET->validate(
    'p', 
    \org\octris\core\validate::T_PATTERN, 
    array('pattern' => )
)) {
    $tmp    = explode('.', $_GET['p']->value);
    $module = array_pop($tmp);
    $domain = implode('.', array_reverse($tmp));
} else {
    $module = '';
    $domain = '';
}

// initialization
use \org\octris\core\app\cli as cli;
use \org\octris\core\app\cli\stdio as stdio;
use \org\octris\core\config as config;
use \org\octris\core\tpl as tpl;

print "\n";
$module = stdio::getPrompt('module [%s]: ', $module, true);
$year   = stdio::getPrompt('year [%s]: ', date('Y'), true);

if ($module == '' || $year == '') {
    die("'module' and 'year' are required!\n");
}

$ns = implode('\\', array_reverse(explode('.', $prj['info.domain']))) . '\\' . $module;

$data = array_merge($prj->filter('info')->getArrayCopy(true), array(
    'year'      => $year,
    'module'    => $module,
    'namespace' => $ns,
    'directory' => str_replace('\\', '.', $ns)
));

// setup destination directory
$dir = cli::getPath(cli::T_PATH_WORK, '') . '/' . $data['directory'];

if (is_dir($dir)) {
    die(sprintf("there seems to be already a project at '%s'\n", $dir));
}

// process skeleton and write project files
$tpl = new tpl();
$tpl->addSearchPath(__DIR__ . '/data/skel/web/');
$tpl->setValues($data);

$box = new tpl\sandbox();

$src = __DIR__ . '/data/skel/web/';
$len = strlen($src);

mkdir($dir, 0755);

$directories = array();
$iterator    = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($src)
);

foreach ($iterator as $filename => $cur) {
    $rel  = substr($filename, $len); 
    $dst  = $dir . '/' . $rel;
    $path = dirname($dst);
    $base = basename($filename);
    $ext  = preg_replace('/^\.?[^\.]+?(\..+|)$/', '\1', $base);
    $base = basename($filename, $ext);
    
    if (substr($base, 0, 1) == '$' && isset($data[$base = ltrim($base, '$')])) {
        // resolve variable in filename
        $dst = $path . '/' . $data[$base] . $ext;
    }
    
    if (!is_dir($path)) {
        // create destination directory
        mkdir($path, 0755, true);
    }
    
    if (!is_binary($filename)) {
        $cmp = $tpl->fetch($rel, tpl\sandbox::T_CONTEXT_TEXT);

        file_put_contents($dst, $cmp);
    } else {
        copy($filename, $dst);
    }
}

print "done.\n";
