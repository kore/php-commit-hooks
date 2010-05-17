<?php
/**
 * Copyright (c) <2009>, all contributors
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice, 
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, 
 *   this list of conditions and the following disclaimer in the documentation 
 *   and/or other materials provided with the distribution.
 * - Neither the name of the project nor the names of its contributors may 
 *   be used to endorse or promote products derived from this software without 
 *   specific prior written permission.
 *
 * This software is provided by the copyright holders and contributors "as is" 
 * and any express or implied warranties, including, but not limited to, the 
 * implied warranties of merchantability and fitness for a particular purpose 
 * are disclaimed. in no event shall the copyright owner or contributors be 
 * liable for any direct, indirect, incidental, special, exemplary, or 
 * consequential damages (including, but not limited to, procurement of 
 * substitute goods or services; loss of use, data, or profits; or business 
 * interruption) however caused and on any theory of liability, whether in 
 * contract, strict liability, or tort (including negligence or otherwise) 
 * arising in any way out of the use of this software, even if advised of the 
 * possibility of such damage.
 *
 * @package php-commit-hooks
 * @version $Revision$
 * @license http://www.opensource.org/licenses/bsd-license.html New BSD license
 */

/**
 * Stream wrapper for plain strings
 *
 * @package Core
 * @version $Revision$
 * @license http://www.opensource.org/licenses/bsd-license.html New BSD license
 */
class pchStringStream
{
    /**
     * Current position inside the string
     * 
     * @var int
     */
	protected $position = 0;

    /**
     * String, wrapped by the stream 
     * 
     * @var string
     */
	protected $string;
	
    /**
     * Cached length of the string
     * 
     * @var int
     */
    protected $length;

    /**
     * Open stream
     * 
     * @param string $path 
     * @param string $mode 
     * @param mixed $options 
     * @param mixed $opened_path 
     * @return bool
     */
    public function stream_open( $path, $mode, $options, &$opened_path )
    {
        $this->string   = substr( $path, strpos( $path, '//' ) + 2 );
        $this->position = 0;
        $this->length   = strlen( $this->string );

        return true;
    }

    /**
     * Read from stream
     * 
     * @param int $count 
     * @return string
     */
    public function stream_read( $count )
    {
        $ret = substr( $this->string, $this->position, $count );
        $this->position += strlen( $ret );
        return $ret;
    }

    /**
     * Write to stream
     * 
     * @param string $data 
     * @return int
     */
    public function stream_write( $data )
    {
        $left            = substr( $this->string, 0, $this->position );
        $right           = substr( $this->string, $this->position + strlen( $data ) );
        $this->string    = $left . $data . $right;
        $this->position += $written = strlen( $data );
        $this->length    = strlen( $this->string );
        return $written;
    }

    /**
     * Tell current stream position
     * 
     * @return int
     */
    public function stream_tell()
    {
        return $this->position;
    }

    /**
     * Has the stream reached its end?
     * 
     * @return bool
     */
    public function stream_eof()
    {
        return $this->position >= $this->length;
    }

    /**
     * Seek to a defined position in the string
     * 
     * @param int $offset 
     * @param int $whence 
     * @return bool
     */
    public function stream_seek($offset, $whence)
    {
        switch ( $whence ) {
            case SEEK_SET:
                if ( ( $offset < $this->length ) &&
                     ( $offset >= 0 ) )
                {
                     $this->position = $offset;
                     return true;
                }
                else
                {
                     return false;
                }
                break;

            case SEEK_CUR:
                if ( $offset >= 0 )
                {
                     $this->position += $offset;
                     return true;
                }
                else
                {
                     return false;
                }
                break;

            case SEEK_END:
                if ( ( $this->length + $offset ) >= 0 )
                {
                     $this->position = $this->length + $offset;
                     return true;
                }
                else
                {
                     return false;
                }
                break;

            default:
                return false;
        }
    }

    /**
     * Returns information about the stream
     * 
     * @return void
     */
    public function stream_stat()
    {
        return array();
    }
}

