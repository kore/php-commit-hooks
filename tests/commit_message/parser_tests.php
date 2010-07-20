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
class pchCommitMessageParserTests extends PHPUnit_Framework_TestCase
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

    public function testEmptyCommit1()
    {
        $parser = new pchCommitMessageCheck();
        $this->assertEquals(
            array(
                new pchIssue( E_ERROR, null, null, 'Empty commit message.' )
            ),
            $parser->parse( '' )
        );

        $this->assertSame( null, $parser->getResult() );
    }

    public function testEmptyCommit2()
    {
        $parser = new pchCommitMessageCheck();
        $this->assertEquals(
            array(
                new pchIssue( E_ERROR, null, null, 'Empty commit message.' )
            ),
            $parser->parse( "\n" )
        );

        $this->assertSame( null, $parser->getResult() );
    }

    public function testEmptyCommit3()
    {
        $parser = new pchCommitMessageCheck();
        $this->assertEquals(
            array(
                new pchIssue( E_ERROR, null, null, 'Empty commit message.' )
            ),
            $parser->parse( "\n\t  \n \n\n" )
        );

        $this->assertSame( null, $parser->getResult() );
    }

    public function testCommentOnly()
    {
        $parser = new pchCommitMessageCheck();

        $return = $parser->parse( "# Done misc stuff\n\n# ?\n" );

        $this->assertSame(
            array(),
            $return
        );
    }

    public function testCommentMultilineOnly()
    {
        $parser = new pchCommitMessageCheck();

        $return = $parser->parse( "# Done misc stuff\n#\n# And some more\n" );

        $this->assertSame(
            array(),
            $return
        );
    }

    public function testDifferentLineBreaks()
    {
        $parser = new pchCommitMessageCheck();

        $return = $parser->parse( "# Done misc stuff\r\n#\r# And some more\n" );

        $this->assertSame(
            array(),
            $return
        );
    }

    public function testCompleteComplexCommitMessage()
    {
        $parser = new pchCommitMessageCheck();

        $return = $parser->parse( '
# This is more complex commit message:
- Fixed #23: This was a hard one!
- Closed #42: And this one was even
  harder!
# Now some implemented messages
#
- Implemented #5: This feature makes me proud.
- Implemented: Some more stuff
# And there is also something better documented now!
- Documented #17: I documented:
  - Foo
  - Bar
  - Blubb
- Documented: And dumdideldi!
- Tested: Everything!
# This was complex enough!
- Translated: The german stuff
- Added: A new logo variant
# Another comment
        ' );

        $this->assertSame( array(), $return );
        $this->assertEquals(
            array(
                array(
                    'type' =>'Fixed',
                    'bug'  =>'23',
                    'text' => 'This was a hard one!',
                ),
                array(
                    'type' =>'Fixed',
                    'bug'  =>'42',
                    'text' =>'And this one was even harder!',
                ),
                array(
                    'type' =>'Implemented',
                    'bug'  =>'5',
                    'text' =>'This feature makes me proud.',
                ),
                array(
                    'type' =>'Implemented',
                    'bug'  => null,
                    'text' =>'Some more stuff',
                ),
                array(
                    'type' =>'Documented',
                    'bug'  =>'17',
                    'text' =>'I documented: - Foo - Bar - Blubb',
                ),
                array(
                    'type' =>'Documented',
                    'bug'  => null,
                    'text' =>'And dumdideldi!',
                ),
                array(
                    'type' =>'Tested',
                    'bug'  => null,
                    'text' =>'Everything!',
                ),
                array(
                    'type' =>'Translated',
                    'bug'  => null,
                    'text' =>'The german stuff',
                ),
                array(
                    'type' =>'Added',
                    'bug'  => null,
                    'text' =>'A new logo variant',
                ),
            ),
            $parser->getResult()
        );
    }

    public function testBrokenComment()
    {
        $parser = new pchCommitMessageCheck( array(
            'Done'   => pchCommitMessageCheck::REQUIRED,
            'Tested' => pchCommitMessageCheck::OPTIONAL,
            'Fixed'  => pchCommitMessageCheck::PROHIBITED,
        ) );

        $error = <<<'EOERROR'
Invalid commit message: "#Foo..."

Allowed are messages following this grammar:

Message       ::= Statement+ | Statement* Comment+
Statement     ::= Done | Tested | Fixed
Comment       ::= '# ' TextLine | '#\n'

Done          ::= '- Done'         BugNr  ': ' TextLine Text?
Tested        ::= '- Tested'       BugNr? ': ' TextLine Text?
Fixed         ::= '- Fixed'               ': ' TextLine Text?

Text          ::= '  ' TextLine Text?
BugNr         ::= ' #' [1-9]+[0-9]*
TextLine      ::= [\x20-\x7E]+ "\n"

EOERROR;

        $this->assertEquals(
            array(
                new pchIssue( E_ERROR, null, null, $error )
            ),
            $parser->parse( '#Foo' )
        );

        $this->assertSame( null, $parser->getResult() );
    }

    public function testUnknownLiteral()
    {
        $parser = new pchCommitMessageCheck( array(
            'Done'   => pchCommitMessageCheck::REQUIRED,
            'Tested' => pchCommitMessageCheck::OPTIONAL,
            'Fixed'  => pchCommitMessageCheck::PROHIBITED,
        ) );

        $error = <<<'EOERROR'
Invalid commit message: "- Unknown: Literal..."

Allowed are messages following this grammar:

Message       ::= Statement+ | Statement* Comment+
Statement     ::= Done | Tested | Fixed
Comment       ::= '# ' TextLine | '#\n'

Done          ::= '- Done'         BugNr  ': ' TextLine Text?
Tested        ::= '- Tested'       BugNr? ': ' TextLine Text?
Fixed         ::= '- Fixed'               ': ' TextLine Text?

Text          ::= '  ' TextLine Text?
BugNr         ::= ' #' [1-9]+[0-9]*
TextLine      ::= [\x20-\x7E]+ "\n"

EOERROR;

        $this->assertEquals(
            array(
                new pchIssue( E_ERROR, null, null, $error )
            ),
            $parser->parse( '- Unknown: Literal' )
        );

        $this->assertSame( null, $parser->getResult() );
    }

    public function testMissingBugNumber1()
    {
        $parser = new pchCommitMessageCheck();
        $this->assertEquals(
            array(
                new pchIssue( E_ERROR, null, null, 'Missing bug number in line: "- Fixed: Foo..."' )
            ),
            $parser->parse( '- Fixed: Foo' )
        );

        $this->assertSame( null, $parser->getResult() );
    }

    public function testMissingBugNumber2()
    {
        $parser = new pchCommitMessageCheck();
        $this->assertEquals(
            array(
                new pchIssue( E_ERROR, null, null, 'Missing bug number in line: "- Closed: Foo..."' )
            ),
            $parser->parse( '- Closed: Foo' )
        );

        $this->assertSame( null, $parser->getResult() );
    }

    public function testMissingBugNumber3()
    {
        $parser = new pchCommitMessageCheck();
        $this->assertEquals(
            array(
                new pchIssue( E_ERROR, null, null, 'Missing bug number in line: "- Refs: Foo..."' )
            ),
            $parser->parse( '- Refs: Foo' )
        );

        $this->assertSame( null, $parser->getResult() );
    }

    public function testSuperflousBugNumber()
    {
        $parser = new pchCommitMessageCheck();
        $this->assertEquals(
            array(
                new pchIssue( E_ERROR, null, null, 'Superflous bug number in line: "- Tested #23: Foo..."' )
            ),
            $parser->parse( '- Tested #23: Foo' )
        );

        $this->assertSame( null, $parser->getResult() );
    }

    public function testMissingText()
    {
        $parser = new pchCommitMessageCheck();
        $this->assertEquals(
            array(
                new pchIssue( E_ERROR, null, null, 'Textual description missing in line "- Fixed #23..."' )
            ),
            $parser->parse( '- Fixed #23' )
        );

        $this->assertSame( null, $parser->getResult() );
    }

    public function testTooLongText()
    {
        $parser = new pchCommitMessageCheck();
        $this->assertEquals(
            array(
                new pchIssue( E_ERROR, null, null, 'Too long line: "- Implemented: This is far too..."' )
            ),
            $parser->parse( '- Implemented: This is far too long text to be in one line, as you may remmeber, this is not allowed.' )
        );

        $this->assertSame( null, $parser->getResult() );
    }

    public function testTooLongComment()
    {
        $parser = new pchCommitMessageCheck();
        $this->assertEquals(
            array(
                new pchIssue( E_ERROR, null, null, 'Too long line: "# This is far too long comment..."' )
            ),
            $parser->parse( '# This is far too long comment line to be in one line, as you may remmeber, this is not allowed.' )
        );

        $this->assertSame( null, $parser->getResult() );
    }

    public function testTextLineWithoutStatement()
    {
        $parser = new pchCommitMessageCheck();
        $this->assertEquals(
            array(
                new pchIssue( E_ERROR, null, null, 'No statement precedes text line: "  All testlines must be precee..."' )
            ),
            $parser->parse( "# Comment\n  All testlines must be preceeded by some statement." )
        );

        $this->assertSame( null, $parser->getResult() );
    }

    public static function getCommitMessages()
    {
        return array(
            array(
                "- Fixed #23: This was a hard one!\n",
                array( array(
                    'type' => 'Fixed',
                    'bug'  => '23',
                    'text' => 'This was a hard one!',
                ) ),
            ),
            array(
                "- Closed #42: And this one was even\n  harder!\n",
                array( array(
                    'type' => 'Fixed',
                    'bug'  => '42',
                    'text' => 'And this one was even harder!',
                ) ),
            ),
            array(
                "- Closed #42: And this one was even\n  harder!",
                array( array(
                    'type' => 'Fixed',
                    'bug'  => '42',
                    'text' => 'And this one was even harder!',
                ) ),
            ),
            array(
                "- Implemented #5: This feature makes me proud.\n",
                array( array(
                    'type' => 'Implemented',
                    'bug'  => '5',
                    'text' => 'This feature makes me proud.',
                ) ),
            ),
            array(
                "- Implemented #5: This feature makes me proud.",
                array( array(
                    'type' => 'Implemented',
                    'bug'  => '5',
                    'text' => 'This feature makes me proud.',
                ) ),
            ),
            array(
                "- Documented: And dumdideldi!\n",
                array( array(
                    'type' => 'Documented',
                    'bug'  => null,
                    'text' => 'And dumdideldi!',
                ) ),
            ),
            array(
                "- Tested: Everything!\n",
                array( array(
                    'type' => 'Tested',
                    'bug'  => null,
                    'text' => 'Everything!',
                ) ),
            ),
            array(
                "- Translated: The german stuff\n",
                array( array(
                    'type' => 'Translated',
                    'bug'  => null,
                    'text' => 'The german stuff',
                ) ),
            ),
            array(
                "- Added: A new logo variant\n",
                array( array(
                    'type' => 'Added',
                    'bug'  => null,
                    'text' => 'A new logo variant',
                ) ),
            ),
        );
    }

    /**
     * Test valid commit messages from data provider
     * 
     * @dataProvider getCommitMessages
     */
    public function testValidCommitMessages( $message, $expectation )
    {
        $parser = new pchCommitMessageCheck();

        $this->assertEquals(
            array(),
            $parser->parse( $message )
        );

        $this->assertEquals(
            $expectation,
            $parser->getResult()
        );
    }

    public static function getCustomCommitMessages()
    {
        return array(
            array(
                "- Done #23: This was a hard one!",
                array( array(
                    'type' => 'Done',
                    'bug'  => '23',
                    'text' => 'This was a hard one!',
                ) ),
            ),
            array(
                "- Tested #23: This was a hard one!",
                array( array(
                    'type' => 'Tested',
                    'bug'  => '23',
                    'text' => 'This was a hard one!',
                ) ),
            ),
            array(
                "- Tested: And dumdideldi!\n",
                array( array(
                    'type' => 'Tested',
                    'bug'  => null,
                    'text' => 'And dumdideldi!',
                ) ),
            ),
            array(
                "- Fixed: And dumdideldi!\n",
                array( array(
                    'type' => 'Fixed',
                    'bug'  => null,
                    'text' => 'And dumdideldi!',
                ) ),
            ),
        );
    }

    /**
     * Test valid commit messages from data provider
     * 
     * @dataProvider getCustomCommitMessages
     */
    public function testValidCustomCommitMessages( $message, $expectation )
    {
        $parser = new pchCommitMessageCheck( array(
            'Done'   => pchCommitMessageCheck::REQUIRED,
            'Tested' => pchCommitMessageCheck::OPTIONAL,
            'Fixed'  => pchCommitMessageCheck::PROHIBITED,
        ) );

        $this->assertEquals(
            array(),
            $parser->parse( $message )
        );

        $this->assertEquals(
            $expectation,
            $parser->getResult()
        );
    }
}

