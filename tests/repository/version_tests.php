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
 * Tests for the commit message parser
 */
class pchRepositoryVersionTests extends PHPUnit_Framework_TestCase
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

    public function testVersionAuthor()
    {
        $repo = new pchRepositoryVersion( __DIR__ . '/../data/repo/', 1 );
        $this->assertEquals(
            'kore',
            $repo->author
        );
    }

    public function testVersionDate()
    {
        $repo = new pchRepositoryVersion( __DIR__ . '/../data/repo/', 1 );
        $this->assertEquals(
            '2009-09-15 20:03:23 +0200 (Tue, 15 Sep 2009)',
            $repo->date
        );
    }

    public function testVersionLog()
    {
        $repo = new pchRepositoryVersion( __DIR__ . '/../data/repo/', 1 );
        $this->assertEquals(
            '- Added: A single test file to the test repository',
            $repo->log
        );
    }
}

