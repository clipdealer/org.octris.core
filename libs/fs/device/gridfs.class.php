<?php

namespace org\octris\core\device {
    /****c* device/gridfs
     * NAME
     *      gridfs
     * FUNCTION
     *      stream wrapper for accessing gridFS of mongodb
     * COPYRIGHT
     *      copyright (c) 2010 by Harald Lapp
     * AUTHOR
     *      Harald Lapp <harald@octris.org>
     ****
     */

    class gridfs extends \org\octris\core\fs\device {
        /****v* gridfs/$defaults
         * SYNOPSIS
         */
        protected static $defaults = array(
            'host'      => 'localhost',
            'username'  => '',
            'password'  => ''
        );
        /*
         * FUNCTION
         *      default settings for accessing gridFS
         ****
         */
        
        /****v* gridfs/$filename
         * SYNOPSIS
         */
        protected $filename;
        /*
         * FUNCTION
         *      filename of file to access
         ****
         */
        
        /****v* gridfs/$metadata
         * SYNOPSIS
         */
        protected $metadata = array();
        /*
         * FUNCTION
         *      stores files' metadata
         ****
         */
        
        /****v* gridfs/$ref
         * SYNOPSIS
         */
        protected $ref = array();
        /*
         * FUNCTION
         *      internal reference pointers of various types
         ****
         */
        
        /****m* gridfs/connect
         * SYNOPSIS
         */
        public static function connect($host, $username = '', $password = '')
        /*
         * FUNCTION
         *      set default host, username and password setting
         * INPUTS
         *      * $host (string) -- hostname of mongodb to connect to
         *      * $username (string) -- username to use for connection
         *      * $password (string) -- password to use for connection
         ****
         */
        {
            self::$defaults['host']     = $host;
            self::$defaults['username'] = $username;
            self::$defaults['password'] = $password;
        }
        
        /****m* gridfs/getConnection
         * SYNOPSIS
         */
        protected function getConnection()
        /*
         * FUNCTION
         *      return connection for mongodb
         * INPUTS
         *      * $url (string) -- connection URL
         ****
         */
        {
            
        }
        
        /****m* gridfs/openfs
         * SYNOPSIS
         */
        protected function openfs($filename)
        /*
         * FUNCTION
         *      open gridfs
         * INPUTS
         *      
         * OUTPUTS
         *      
         ****
         */
        {
            $filename = '/' . ltrim(preg_replace('|^.+?://|', '', $filename), '/');

            $filter = array('filename' => $filename);

            $cn     = $this->getConnection();
            $gridfs = $cn->getGridFS();
            $gridfs->ensureIndex(array('filename' => 1), array('unique' => 1));

            if (($attrib = $gridfs->findOne($filter))) {
                if (($this->mode & self::T_WRITE) == self::T_WRITE) {
                    $gridfs->storeBytes('', array('filename' => $filename));

                    $attrib = $gridfs->findOne($filter);
                } 
            } elseif ($this->mode == self::OP_WRITE) {
                $document = array(
                    '$set' => array(
                        'length' => 0,
                    )
                );

                $gridfs->update($filter, array(array('$set' => array('length' => 0))));
                $gridfs->chunks->remove(array("files_id" => $attrib->file['_id']));
                /* The file is reset, so we set empty length */
                $attr->file['length'] = 0;
            }

            $this->metadata = $attrib->file;
            $this->ref      = array(
                'gridfs' => $gridfs,
                'chunks' => $gridfs->chunks;
                
            );

            /* load grid and chunks references */
            $this->grid     = $grid;
            $this->chunks   = $grid->chunks; 
            $this->cursor   = $this->chunks->find(array("files_id" => $this->file_id));
            $this->chunk_id = -1;
            $this->offset   = 0;
            $this->cache_offset = 0; 
            $this->total_chunks = $this->cursor->count();

            return true;
        }
        
        /****m* gridfs/stream_open
         * SYNOPSIS
         */
        function stream_open($path, $mode, $options, &$opened_path)
        /*
         * FUNCTION
         *      open file or URL
         * INPUTS
         *      * $path (string) -- path or URL
         *      * $mode (string) -- mode to use to open the file
         *      * $options (int) -- additional flags
         *      * $opended_path (string) -- complete path of file opened
         ****
         */
        {
            if (!$this->parseMode($mode) || !$this->openfs($path)) {
                return false;
            }
            
            $this->stream_seek(0, $this->mode == self:)
            // seek
        }
            /* Open file or create */
            if (!$this->mongo_fs_open($filename)) {
                return false;
            }

            /* Set initial fp position */
            if (!$this->stream_seek(0, $this->mode==self::OP_APPEND ? SEEK_END : SEEK_SET)) {
                $this->set_error("Initial offset falied");
                return false;
            }

            /* succeed */
            return true;
        }
        // }}}

        // stream_read(int $bytes) {{{
        /**
         *  stream_read
         *
         */
        final function stream_read($bytes)
        {
            $cache      = & $this->cache;
            $offset     = & $this->cache_offset; 
            $chunk_size = $this->chunk_size;
            $cache_size = & $this->cache_size;
            $data       = "";

            if ($offset + $bytes >= $chunk_size) {
                $data  .= substr($cache, $offset);
                $bytes -= strlen($data);
                $this->stream_seek($chunk_size * ($this->chunk_id+1), SEEK_SET);
            }

            if ($bytes > 0) {
                $data  .= substr($cache, $offset, $bytes);
                $bytes = strlen($data);
                $offset       += $bytes; 
                $this->offset += $bytes;
            }

            return $data;
        }
        // }}}
    }
}

    // int stream_seek($offset, $whence) {{{
    /**
     *
     *
     */
    final public function stream_seek($offset, $whence)
    {
        $size = $this->size;
        if ($this->mode != self::OP_READ) {
            /* We might want go to the next new chunk */
            $size += 1;
            if ($this->chunk==null) {
                /* if the current chunk is not synced */
                /* yet we might want to move to the next chunk */
                /* (of course this function call flush() ) */
                $size += $this->chunk_size;
            }
        }
        switch ($whence) {
        case SEEK_SET:
            if ($offset > $size || $offset < 0) {
                $this->set_error("Can't offset to {$offset} (Filesize: {$size})");
                return false;
            }
            break;
        case SEEK_CUR:
            $offset += $this->offset;
            if ($offset > $size) {
                $this->set_error("Can't offset to {$offset} (Filesize: {$size})");
                return false;
            }
            break;
        case SEEK_END:
            $offset += $this->size;
            if ($offset > $size) {
                $this->set_error("Can't offset to {$offset} (Filesize: {$size})");
                return false;
            }
            break;
        default:
            return false;
        }
        
        $chunk_new = floor($offset / $this->chunk_size);
        $chunk_cur = $this->chunk_id;

        if ($chunk_new != $chunk_cur) {
            /* Save the old chunk, if any */
            if ($this->mode != self::OP_READ) {
                $this->stream_flush();
            }

            /* Delete current cursor and re-query it */
            $this->cursor->reset();

            $this->cursor = $this->chunks->find(array("files_id" => $this->file_id, "n" => $chunk_new));
            if ($this->cursor->count() == 0) {
                /* The requested chunk doesn't exits */
                if ($this->mode == self::OP_READ) {
                    $this->set_error("Fatal error while reading file chunk {$chunk_new}");
                    return false;
                }
                $this->cache      = str_repeat("X", $this->chunk_size);
                $this->cache_size = 0;
                $this->chunk      = null;
                $this->total_chunks++;
            } else {
                $this->cursor->next();
                $this->chunk      = $this->cursor->current();
                $this->cache      = $this->chunk['data']->bin;
                $this->cache_size = strlen($this->cache);
            }
            /* New Chunk ID */
            $this->chunk_id = $chunk_new; 
        }
        $this->cache_offset = $offset%$this->chunk_size;
        $this->offset       = $offset;

        return true;
    }
    // }}}

    // bool stream_flush() {{{
    /**
     *  If the file is opened in write mode and the 
     *  IO cache had changed this function will put 
     *  replace the file chunk at MongoDB.
     *
     */
    final function stream_flush()
    {
        if ($this->mode == self::OP_READ) {
            return false;
        }

        if ($this->chunk_id < 0 || !$this->cache_dirty) {
            return true;
        } 

        $cache = substr($this->cache, 0, $this->cache_size);

        if ($this->chunk == null) {
            $document = array(
                'files_id' => $this->file_id,
                'n' => $this->chunk_id,
                'data' => new MongoBinData($cache),
            );

            /* save the current chunk */
            $this->chunks->insert($document, true);
            $this->chunk = $document;
            
            $this->size += $this->cache_size;
        } else {
            $document = array(
                '$set' => array(
                    'data' => new MongoBinData($cache),
                ),
            );
            $filter = array(
                '_id' => $this->chunk['_id']
            );

            $this->chunks->update($filter, $document);

            if ($this->total_chunks == $this->chunk_id+1) {
                $this->size = ($this->chunk_id) * $this->chunk_size + $this->cache_size;
            }
        }

        /* flag the current cache as not-dirty */
        $this->cache_dirty = false;

        return true;
    }
    // }}}

    // stream_close() {{{
    /**
     *  fclose($fp):
     *
     *  This close the current file, also if the file is opened in 
     *  write, append or read/write mode, and the file had changed
     *  it would regenerate the md5 checksum and update it
     *
     */
    final function stream_close()
    {
        if ($this->mode == self::OP_READ) {
            return true;
        }
        $this->stream_flush();
        $command = array(
            "filemd5" => $this->file_id, 
            "root" => "fs",
        );
        $result = $this->_getConnection()->command( $command );

        if (true) {
            /* silly test to see if we count the size correctly */
            /* when it becames more stable I'll remove it */
            $size = $this->chunks->group(array(), array("size" => 0), new MongoCode("function (b,a) { a.size += b.data.len-4; }"), array("files_id" => $this->file_id)); 

            if ($size['retval'][0]['size'] != $this->size) {
                print_r(array($size['retval'][0]['size'], $this->size));
            }
        }

        if ($result['ok'] != 1) {
            $this->set_error("Imposible to get MD5 from MongoDB".$result['errmsg']);
            return false;
        }

        $document = array(
            '$set' => array(
                'length' => $this->size,
                'md5' => $result['md5'],
            ),
        );

        $this->grid->update(array('_id' => $this->file_id), $document);
        return true;
    }
    // }}}

    // stream_write($data) {{{
    /**
     *  Write into $data in the current file
     */
    final function stream_write($data)
    {
        if ($this->mode == self::OP_READ) {
            $this->set_error("Impossible to write in READ mode");
            return false;
        }
        $cache      = & $this->cache;
        $offset     = & $this->cache_offset; 
        $chunk_size = $this->chunk_size;
        $cache_size = & $this->cache_size;
        $data_size  = strlen($data);
        $wrote      = 0;

        /* flag the current cache chunk as dirty */
        $this->cache_dirty = true;

        if ($offset + $data_size >= $chunk_size) {
            $wrote += $chunk_size - $offset;
            if ($wrote > 0) {
                $cache  = substr($cache, 0, $offset);
                $cache .= substr($data, 0, $wrote);
            }
            $cache_size    = strlen($cache);
            $this->offset += $wrote;

            /* Move to the next chunk, stream_seek */
            /* will automatically sync it to mongodb */
            if (!$this->stream_seek($chunk_size * ($this->chunk_id+1), SEEK_SET)) {
                throw new MongoException("Offset falied");
            }

            if ($wrote > 0) {
                $data      = substr($data, $wrote);
                $data_size = strlen($data);
                /* The new chunk must be flagged as dirty */
                $this->cache_dirty = true;
            }
        }

        if ($data_size > 0) {
            $left    = substr($cache, 0, $offset);
            $right   = substr($cache, $offset + $data_size);
            $cache   = $left.$data.$right;
            $offset += $data_size; 
            $wrote  += $data_size;
            $this->offset += $data_size;
        }

        if($offset > $cache_size) {
            $cache_size = $offset;
        }

        return $wrote;
    }
    // }}}

    // stream_tell() {{{
    /**
     *  Return the current file pointer position
     */
    final function stream_tell()
    {
        return $this->offset;
    }
    // }}}

    // stream_eof() {{{
    /**
     *  Tell if the file pointer is at the end
     */
    final function stream_eof()
    {
        return $this->offset >= $this->size;
    }
    // }}}

    // stream_fstat() {{{
    /**
     *  Return stat info about the current file
     */
    final function stream_stat()
    {
        return array(
            'size' => $this->size,
        );
    }
    // }}}

    // unlink($file) {{{
    /**
     *  Remove the given file
     */
    final function unlink($file)
    {
        /* Set a fake mode, in order to see if the file exists */
        $this->mode = self::OP_READ;

        /* Open file or create */
        if (!$this->mongo_fs_open($file)) {
            return false;
        }

        $this->grid->remove(array("_id" => $this->file_id));
        $this->chunks->remove(array("files_id" => $this->file_id));

        return true;
    }
    // }}}

    // storeFile($filename, $name) {{{
    /**
     *  Simple wrap to the native "storeFile" method
     *
     *  @param string $filename File to upload
     *  @param string $name     Name for the uploaded file
     *
     *  @return 
     */
    public static function uploadFile($filename, $name=null) 
    {
        if ($name == null) {
            $name = basename($filename);
        }
        $f = new ActiveMongoFS;
        $db = $f->_getConnection();
        return $db->getGridFS()->storeFile($filename, array('filename' => $name));
    }
    // }}}

    function url_stat($file)
    {
        /* Set a fake mode, in order to see if the file exists */
        $this->mode = self::OP_READ;

        /* Open file or create */
        if (!$this->mongo_fs_open($file)) {
            return false;
        }
        return $this->stream_stat();
    }

}

/* Register the STREAM class */
stream_wrapper_register("gridfs", "MongoFS")
    or die("Failed to register protocol");

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: sw=4 ts=4 fdm=marker
 * vim<600: sw=4 ts=4
 */
