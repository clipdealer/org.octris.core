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
         * Database connections to handle with this instance.
         *
         * @octdoc  v:mongodb/$cn
         * @var     \org\octris\core\dbo\mongodb\connection
         */
        private $cn;
        /**/
        
        /**
         * Name of collection object belongs to.
         *
         * @octdoc  v:mongodb/$collection
         * @var     string
         */
        private $collection = '';
        /**/
        
        /**
         * Instance of result-set DBO object belongs to.
         *
         * @octdoc  v:mongodb/$result
         * @var     
         */
        private $result;
        /**/
        
        /**
         * Constructor.
         *
         * @octdoc  m:mongodb/__construct
         * @param   \org\octris\core\dbo\mongodb\connection     $cn             Instance of MongoDB connection.
         * @param   string                                      $collection     Name of collection to access.
         * @param                                               $result         Instance of result-set
         */
        protected function __construct(\org\octris\core\dbo\mongodb\connection $cn, $collection, $result)
        /**/
        {
            $this->cn         = $cn;
            $this->collection = $collection;
            $this->result     = $result;
        }

        /**
         * Fetch next item from result-set.
         *
         * @octdoc  m:mongodb/next
         * @return  bool|\org\octris\core\dbo\mongodb\object         Instance of item object or false.
         */
        public function next()
        /**/
        {
            $item = false;
            
            if ($this->result && ($tmp = $this->result->fetchNext())) {
                $class = static::$object_ns . $this->collection;
                $item  = new $class($this->cn, $tmp);
            }

            return $item;
        }
        
        /**
         * Alias for method 'first'.
         *
         * @octdoc  m:mongodb/one
         * @param   \org\octris\core\dbo\mongodb\pool           $pool               Instance of pool handling mongodb database connections.
         * @param   string                                      $collection         Name of collection to query.
         * @param   array                                       $criteria           Criteria to query collection by.
         * @return  bool|\org\octris\core\dbo\mongodb\object                        Instance of item object or 'false'
         */
        public static function one(\org\octris\core\dbo\mongodb\pool $pool, $collection, array $criteria)
        /**/
        {
            return self::first($pool, $collection, $criteria);
        }
        
        /**
         * Queries database and returns first found item.
         *
         * @octdoc  m:mongodb/first
         * @param   \org\octris\core\dbo\mongodb\pool           $pool               Instance of pool handling mongodb database connections.
         * @param   string                                      $collection         Name of collection to query.
         * @param   array                                       $criteria           Criteria to query collection by.
         * @return  bool|\org\octris\core\dbo\mongodb\object                        Instance of item object or 'false'
         */
        public static function first(\org\octris\core\dbo\mongodb\pool $pool, $collection, array $criteria)
        /**/
        {
            $db = self::getAccess($collection);

            $item = false;

            if (($result = $db->query($criteria)) && ($item = $result->fetchNext())) {
                $class = static::$object_ns . $collection;
                $item  = new $class($db, $item);
            }

            return $item;
        }
        
        /**
         * Queries database and returns result-set.
         *
         * @octdoc  m:mongodb/query
         * @param   string      $collection                         Name of collection to query.
         * @param   array       $criteria                           Criteria to query collection by.
         * @param   int         $offset                             Optional offset to start query at.
         * @param   int         $limit                              Optional items to limit result to.
         * @param   array       $sort                               Optional sorting parameters.
         * @param   array       $fields                             Optional fields to return from in result.
         * @param   array       $hint                               Optional query hint.
         * @return  bool|\org\octris\core\dbo\mongodb\object        Instance of item object or 'false'
         */
        public static function query($collection, array $criteria, $offset = 0, $limit = null, array $sort = null, array $fields = array(), array $hint = null)
        /**/
        {
            $db = self::getAccess($collection);

            $result = new static($collection, $db->query($criteria, $offset, $limit, $sort, $fields, $hint));

            return $result;
        }
        
        /**
         * Resolve database reference.
         *
         * @octdoc  m:mongodb/resolve
         * @param   MongoDBRef          $ref                        Reference as MongoDBRef.
         * @return  bool|\org\octris\core\dbo\mongodb\object        Item object or 'false', if reference could not be resolved.
         */
        public static function resolve($ref)
        /**/
        {
            if (!is_array($ref) || !MongoDBRef::isRef($ref)) {
                return false;
            } else {
                $collection = $ref['$ref'];
                $item       = false;

                $db = self::getAccess($collection);

                if ($result = $db->getReference($ref)) {
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
         * @param   string          $code                       Javascript code to execute server-side.
         * @param   array           $args                       Optional arguments to pass to code.
         * @return  bool|\org\octris\core\dbo\mongodb\object    Instance of item object or 'false'
         */
        public static function execute($code, array $args = array())
        /**/
        {
            $db = self::getAccess(null);

            $result = $db->execute($code, $args);

            return $result;
        }

        /**
         * Create new object.
         *
         * @octdoc  m:mongodb/create
         * @param   string          $collection             Collection of object.
         * @param   array           $data                   Optional data to fill object with.
         * @return  \org\octris\core\dbo\mongodb\object     Created object.
         */
        public static function create($collection, array $data = array())
        /**/
        {
            $class = static::$object_ns . $collection;

            return new $class(self::getAccess($collection), $data);
        }
        
        /**
         * Load object by specified ID.
         *
         * @octdoc  m:mongodb/load
         * @param   string          $collection                 Collection the object should be located in.
         * @param   string          $_id                        ID of object.
         * @return  bool|\org\octris\core\dbo\mongodb\object    Instance of item object or 'false'
         */
        public static function load($collection, $_id)
        /**/
        {
            return self::first($collection, array('_id' => new MongoId($_id)));
        }
        
    }
    
}
