<?php

namespace org\octris\core\dbo {
    /**
     * MongoDB DBO interface.
     *
     * @octdoc      c:dbo/mongodb
     * @copyright   copyright (c) 2011 by Harald Lapp
     * @author      Harald Lapp <harald@octris.org>
     */
    abstract class mongodb extends \org\octris\core\dbo
    /**/
    {
        /**
         * Namespace of objects to create from result items.
         *
         * @octdoc  v:mongodb/$object_ns
         * @var     string
         */
        protected static $object_ns = '';
        /**/
        
        /**
         * Connection pool.
         *
         * @octdoc  v:mongodb/$pool
         * @var     \org\octris\core\dbo\mongodb\pool
         */
        private static $pool = null;
        /**/
        
        /*
         * Prevent creating of object instance from this class.
         */
        protected function __construct() {}

        /**
         * Return instance of database access layer.
         *
         * @octdoc  m:mongodb/getAccess
         * @param   string      $type                               Type of access (master / slave).
         * @return  \org\octris\core\dbo\mongodb\connection         Connection.
         */
        public static function getAccess($type)
        /**/
        {
            if (is_null(self::$pool)) {
                self::$pool = new \org\octris\core\dbo\mongodb\pool($cfg->getSet(static::$dsn));
            }
            
            return self::$pool->connect($type);
        }

        /**
         * Alias for method 'first'.
         *
         * @octdoc  m:mongodb/one
         * @param   string                                      $collection         Name of collection to query.
         * @param   array                                       $criteria           Optional criteria to query collection by.
         * @return  bool|\org\octris\core\dbo\mongodb\object                        Instance of item object or 'false'
         */
        public static function one($collection, array $criteria = array())
        /**/
        {
            return self::first($collection, $criteria);
        }
        
        /**
         * Queries database and returns first found item.
         *
         * @octdoc  m:mongodb/first
         * @param   string                                      $collection         Name of collection to query.
         * @param   array                                       $criteria           Optional c riteria to query collection by.
         * @return  bool|\org\octris\core\dbo\mongodb\object                        Instance of item object or 'false'
         */
        public static function first($collection, array $criteria = array())
        /**/
        {
            $item = false;

            if (($result = self::query($collection, $criteria)) && count($result) > 0) {
                $item = current($result);
            }

            return $item;
        }
        
        /**
         * Queries database and returns result-set.
         *
         * @octdoc  m:mongodb/query
         * @param   string                                      $collection         Name of collection to query.
         * @param   array                                       $criteria           Optional criteria to query collection by.
         * @param   int                                         $offset             Optional offset to start query at.
         * @param   int                                         $limit              Optional items to limit result to.
         * @param   array                                       $sort               Optional sorting parameters.
         * @param   array                                       $fields             Optional fields to return from in result.
         * @param   array                                       $hint               Optional query hint.
         * @return  \org\octris\core\dbo\mongodb\result                             Instance of item object or 'false'
         */
        public static function query($collection, array $criteria = array(), $offset = 0, $limit = null, array $sort = null, array $fields = array(), array $hint = null)
        /**/
        {
            $result = array();
            
            $cn = self::getAccess(\org\octris\core\dbo::T_DBO_SELECT);
            
            if (($cursor = $cn->query($collection, $criteria, $offset, $limit, $sort, $fields, $hint))) {
                $result = new \org\octris\core\dbo\mongodb\result(self::$pool, $cursor, self::$object_ns, $collection);
            }

            return $result;
        }
        
        /**
         * Resolve database reference.
         *
         * @octdoc  m:mongodb/resolve
         * @param   MongoDBRef                                  $ref                Reference as MongoDBRef.
         * @return  bool|\org\octris\core\dbo\mongodb\object                        Item object or 'false', if reference could not be resolved.
         */
        public static function resolve($ref)
        /**/
        {
            if (self::isref($value)) {
                return false;
            } else {
                $collection = $ref['$ref'];
                $item       = false;

                if ($result = $cn->getReference($ref)) {
                    $class = static::$object_ns . $collection;
                    $item  = new $class($db, $result);
                }

                return $item;
            }
        }

        /**
         * Execute server-side code and returns result-set.
         *
         * @octdoc  m:mongodb/execute
         * @param   string                                      $code               Javascript code to execute server-side.
         * @param   array                                       $args               Optional arguments to pass to code.
         * @return  bool|\org\octris\core\dbo\mongodb\object                        Instance of item object or 'false'
         */
        public static function execute($code, array $args = array())
        /**/
        {
            $cn = self::getAccess(\org\octris\core\dbo::T_DBO_UPDATE);

            $result = $cn->execute($code, $args);

            return $result;
        }

        /**
         * Create new object.
         *
         * @octdoc  m:mongodb/create
         * @param   string                                      $collection         Collection of object.
         * @param   array                                       $data               Optional data to fill object with.
         * @return  \org\octris\core\dbo\mongodb\object                             Created object.
         */
        public static function create($collection, array $data = array())
        /**/
        {
            $cn = self::getAccess(\org\octris\core\dbo::T_DBO_UPDATE);

            $class = static::$object_ns . $collection;

            return new $class($cn, $data);
        }
        
        /**
         * Load object by specified ID.
         *
         * @octdoc  m:mongodb/load
         * @param   string                                      $collection         Collection the object should be located in.
         * @param   string                                      $_id                ID of object.
         * @return  bool|\org\octris\core\dbo\mongodb\object                        Instance of item object or 'false'
         */
        public static function load($collection, $_id)
        /**/
        {
            return self::first($cn, $collection, array('_id' => new MongoId($_id)));
        }
    }
}
