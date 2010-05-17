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
 * Tests for string streams
 */
class pchBaseStringStreamTests extends PHPUnit_Framework_TestCase
{
    /**
     * Return test suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
	public static function suite()
	{
		return new PHPUnit_Framework_TestSuite( __CLASS__ );
	}

    public function setUp()
    {
        if ( !array_search( 'string', stream_get_wrappers() ) )
        {
            stream_wrapper_register( 'string', 'pchStringStream' );
        }
    }

    public function testGetContents()
    {
        $string = 'foo';
        $stream = fopen( 'string://' . $string, 'r' );
        $this->assertEquals( $string, stream_get_contents( $stream ) );
    }

    public function testGetContents2()
    {
        $string = 'foo';
        $this->assertEquals( $string, file_get_contents( 'string://' . $string ) );
    }

    public function testReadLine()
    {
        $string = ( $line = "foo\r\n" ) . "bar";
        $stream = fopen( 'string://' . $string, 'r' );
        $this->assertEquals( $line, fgets( $stream ) );
    }

    public function testReadLine2()
    {
        $string = ( $line = "foo\n" ) . "bar";
        $stream = fopen( 'string://' . $string, 'r' );
        $this->assertEquals( $line, fgets( $stream ) );
    }

    public function testWriteStream()
    {
        $stream = fopen( 'string://', 'w' );
        fwrite( $stream, $string = 'Hello world!' );
        fseek( $stream, 0 );
        $this->assertEquals( $string, stream_get_contents( $stream ) );
    }

    public function testWriteBinaryStream()
    {
        $stream = fopen( 'string://', 'w' );
        fwrite( $stream, $string = "Hello\x00orld!" );
        fseek( $stream, 0 );
        $this->assertEquals( $string, stream_get_contents( $stream ) );
    }

    public function testAppendStream()
    {
        $stream = fopen( 'string://' . ( $string = 'Hello' ), 'a' );
        fwrite( $stream, $string .= ' world!' );
        fseek( $stream, 0 );
        $this->assertEquals( $string, stream_get_contents( $stream ) );
    }
}
