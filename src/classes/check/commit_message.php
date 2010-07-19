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
 * Check for valid commit messages.
 *
 * The check implements by default the following grammar for commit messages, 
 * while it can be customized like shown later.
 *
 * <code>
 *  Message       ::= Statement+ | Statement* Comment+
 *  Statement     ::= Reference | Fixed | Implemented | Documented | Tested
 *                  | Added | Translated
 *
 *  Comment       ::= '# ' TextLine | '#\n'
 *
 *  Reference     ::= '- Refs'         BugNr  ': ' TextLine Text?
 *  Fixed         ::= '- ' FixedString BugNr  ': ' TextLine Text?
 *  Implemented   ::= '- Implemented'  BugNr? ': ' TextLine Text?
 *  Documented    ::= '- Documented'   BugNr? ': ' TextLine Text?
 *  Tested        ::= '- Tested: '                 TextLine Text?
 *  Added         ::= '- Added: '                  TextLine Text?
 *  Translated    ::= '- Translated: '             TextLine Text?
 *
 *  FixedString   ::= 'Fixed' | 'Closed'
 *
 *  Text          ::= '  ' TextLine Text?
 *  BugNr         ::= ' #' [1-9]+[0-9]*
 *  TextLine      ::= [\x20-\x7E]+ "\n"
 * </code>
 *
 * With one additional condition not mentioned in the grammar, that no line
 * should ever exceed 79 characters per line.
 *
 * A textual description of the rules above:
 *
 * <code>
 *  All messages should wrap at 79 characters per line. This means, if you are
 *  writing multiple lines after a message starting with a "- " each following
 *  line should be indented by exactly two spaces.
 *
 *  Including descriptive text in your commit messages is generally important to
 *  offer a good overview on the commit when the issue tracker is not available
 *  (commit mails, history).
 *
 *  All messages may include references to existing issues to add status updates
 *  to the issue, which should look like::
 *
 *      - Refs #<number>: <text>
 *
 *  Where <number> references the ticket and the <text> describes what you did.
 *
 *  Comments
 *  --------
 *
 *  You may always append arbitrary comments in your commit messages, where each
 *  line should start with a number sign (#). Text in these lines won't be
 *  checked.
 *
 *  Bug fix
 *  -------
 *
 *  A bug fix commit message should follow the following scheme::
 *
 *      - Fixed #<number>: <text>
 *
 *  Where <number> references the closed bug and <text> is a description of the
 *  bug and the fix. Keep in mind that the texts will be used for the changelog,
 *  so please check the spelling before committing.
 *
 *  The bug number is not optional, which means that there should be an open bug
 *  in the issue tracker for *each* bug you fix.
 *
 *  For compatibility with other issue tracker you may also use "Closed" instead
 *  of "Fixed" in your message, but "Fixed" is highly preferred.
 *
 *  New features
 *  ------------
 *
 *  If you implemented a new feature, your commit message should look like::
 *
 *      - Implemented[ #<number>]: <text>
 *
 *  Where <text> is a short description of the feature you implemented, and
 *  <number> may optionally reference a feature request in the bug tracker. Keep
 *  in mind that the texts will be used for the changelog, so please check the
 *  spelling before committing.
 *
 *  Documentation
 *  -------------
 *
 *  If you extended your documentation, your commit message should look like::
 *
 *      - Documented[ #<number>]: <text>
 *
 *  Where <number> optionally specifies a documentation request, and the text
 *  describes what you documented.
 *
 *  Additional tests
 *  ----------------
 *
 *  If you added tests for some feature, your commit message should look like::
 *
 *      - Tested: <text>
 *
 *  Where <text> describes the feature(s) you are testing.
 *
 *  Other commits
 *  -------------
 *
 *  If your commit does not match any of the above rules you should only include a
 *  comment in your commit message or extend this document with your commit
 *  message of desire.
 * </code>
 *
 * Even we have a contextfree grammar for the language, we implement the
 * trivial parser using regular expressions.
 *
 * You can customize the allowed keywords in the commit messages and if a bug 
 * number is optional for each keyword, by providing your own specification 
 * array, like described in the constructor documentation.
 * 
 * @package php-commit-hooks
 * @version $Revision$
 * @license http://www.opensource.org/licenses/bsd-license.html New BSD license
 */
class pchCommitMessageCheck extends pchCheck
{
    /**
     * A bug number is required
     */
    const REQUIRED = 1;

    /**
     * A bug number is optional
     */
    const OPTIONAL = 2;

    /**
     * A bug number is not allowed
     */
    const PROHIBITED = 4;

    /**
     * Commit message validation rules
     * 
     * @var array
     */
    protected $rules = array(
        'Refs'        => self::REQUIRED,
        'Fixed'       => self::REQUIRED,
        'Closed'      => self::REQUIRED,
        'Implemented' => self::OPTIONAL,
        'Documented'  => self::OPTIONAL,
        'Tested'      => self::PROHIBITED,
        'Translated'  => self::PROHIBITED,
        'Added'       => self::PROHIBITED,
    );

    /**
     * Parsed result in processable form
     * 
     * @var array
     */
    protected $result;

    /**
     * Construct parser
     *
     * You may optionally specify a custom specification array to introduce new 
     * keywords, or configure the available keywords differently. The 
     * specification array should look like:
     *
     * <code>
     *  array(
     *      'Fixed'       => pchCommitMessageCheck::REQUIRED,
     *      'Implemented' => pchCommitMessageCheck::OPTIONAL,
     *      'Tested'      => pchCommitMessageCheck::PROHIBITED,
     *      ...
     *  );
     * </code>
     *
     * @return void
     */
    public function __construct( array $specification = null )
    {
        if ( $specification !== null )
        {
            $this->rules = $specification;
        }
    }

    /**
     * Parse a commit message
     *
     * Parses a commit messages defined by the grammar documented in the class
     * header. If a parse error occures an array with meesages will be 
     * returned. If the commit message matches the defined grammar an empty 
     * array will be returned.
     *
     * @param string $string
     * @return array
     */
    public function parse( $string )
    {
        $string = $this->normalizeWhitespaces( $string );
        if ( $string === '' )
        {
            return array(
                new pchIssue( E_ERROR, null, null, 'Empty commit message.' ),
            );
        }

        $string = $this->removeComments( $string );
        if ( $string === '' )
        {
            // Do not enter parsing process if there were only comments in the
            // commit message.
            return array();
        }

        return $this->parseStatements( $string );
    }

    /**
     * Removes comments from a commit message
     *
     * Removes all valid comments from a commit messages, as they are not of
     * interest for the content extraction.
     *
     * @param string $string
     * @return string
     */
    protected function removeComments( $string )
    {
        return preg_replace(
            '(^#(?: [\x20-\x7E]{1,77})?$)m',
            '',
            $string
        );
    }

    /**
     * Normalizes whitespaces in commit message
     *
     * Even not defined in the grammar we do not care about additional newlines
     * or empty lines anywhere.
     *
     * @param string $string
     * @return string
     */
    protected function normalizeWhitespaces( $string )
    {
        return preg_replace(
            '((?:\r\n|\r|\n)(?:[ \t](?:\r\n|\r|\n))*)',
            "\n",
            trim( $string )
        );
    }

    /**
     * Parse all statements
     *
     * If a parse error occures an array with meesages will be returned. If the 
     * commit message matches the defined grammar an empty array will be 
     * returned.
     *
     * A result array with all parsed messages will be stored in the following 
     * form and may be requested using the getResult() method:
     *
     * <code>
     *  array(
     *      array(
     *          'type' => <type>,
     *          'bug'  => <number> | null,
     *          'text' => <text>,
     *      ),
     *      ...
     *  )
     * </code>
     *
     * @param string $string
     * @return array
     */
    protected function parseStatements( $string )
    {
        $lines = explode( "\n", $string );
        $statements = array();
        $statement = null;

        foreach ( $lines as $line )
        {
            // Skip empty lines
            if ( trim( $line ) === '' )
            {
                continue;
            }

            // Check for line length
            $line = rtrim( $line );
            $echodLine = '"' . substr( $line, 0, 30 ) . '..."';
            if ( strlen( $line ) > 79 )
            {
                return array(
                    new pchIssue( E_ERROR, null, null, "Too long line: $echodLine" ),
                );
            }

            if ( preg_match( '(^-\\x20
              (?# Type of statement )
                (?P<type>' . implode( '|', array_keys( $this->rules ) ) . ')
              (?# Match optional bug number )
                (?:\\x20\\#(?P<bug>[1-9]+[0-9]*))?
              (?# Match required text line )
                (?::\\x20(?P<text>[\x20-\x7E]+))?
            $)x', $line, $match ) )
            {
                // Check if required text has been included in message
                if ( !isset( $match['text'] ) || empty( $match['text'] ) )
                {
                    return array(
                        new pchIssue( E_ERROR, null, null, "Textual description missing in line $echodLine" ),
                    );
                }

                // Check if bug number has been set for statements requireing a
                // bug number
                if ( ( $this->rules[$match['type']] === self::REQUIRED ) &&
                     ( !isset( $match['bug'] ) ||
                       empty( $match['bug'] ) ) )
                {
                    return array(
                        new pchIssue( E_ERROR, null, null, "Missing bug number in line: $echodLine" ),
                    );
                }

                // Ensure no bug number has been provided for statements which
                // may not be used together with a bug number
                if ( $this->rules[$match['type']] === self::PROHIBITED )
                {
                    if ( isset( $match['bug'] ) && !empty( $match['bug'] ) )
                    {
                        return array(
                            new pchIssue( E_ERROR, null, null, "Superflous bug number in line: $echodLine" ),
                        );
                    }

                    // Force bug to null, so we can use this variable later
                    $match['bug'] = null;
                }

                // Append prior statement to statement array
                if ( $statement !== null )
                {
                    $statements[] = $statement;
                }

                // Create new statement from data
                $statement = array(
                    'type' => str_replace( 'Closed', 'Fixed', $match['type'] ),
                    'bug'  => $match['bug'],
                    'text' => $match['text'],
                );
            }
            elseif ( preg_match( '(^  (?P<text>[\x20-\x7E]+)$)', $line, $match ) )
            {
                if ( $statement == null )
                {
                    // Each additional comment line has to be preceeded by a
                    // statement
                    return array(
                        new pchIssue( E_ERROR, null, null, "No statement precedes text line: $echodLine" ),
                    );
                }

                $statement['text'] .= ' ' . $match['text'];
            }
            else
            {
                return array(
                    new pchIssue( E_ERROR, null, null, "Invalid commit message: $echodLine" ),
                );
            }
        }

        // Append last statement
        if ( $statement !== null )
        {
            $statements[] = $statement;
        }
        $this->result = $statements;

        return array();
    }

    /**
     * Get parse result
     * 
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Return a string representation of the implemented EBNF
     *
     * Returns a string representation of the EBNF, based on the configured 
     * rules and the default settings, like the commit message length.
     * 
     * @return string
     */
    public function getEBNF()
    {
        $ebnf  = "";
        $ebnf .= "Message       ::= Statement+ | Statement* Comment+\n";
        $ebnf .= "Statement     ::= " . implode( " | ", array_keys( $this->rules ) )  . "\n";
        $ebnf .= "Comment       ::= '# ' TextLine | '#\\n'\n";
        $ebnf .= "\n";

        foreach ( $this->rules as $name => $type )
        {
            $ebnf .= sprintf( "%s%s ::= '- %s'%s%s ': ' TextLine Text?\n",
                $name,
                $ws = str_repeat( ' ', max( 0, 13 - strlen( $name ) ) ),
                $name,
                $ws,
                ( $type === self::REQUIRED ? 'BugNr ' :
                    ( $type === self::OPTIONAL ? 'BugNr?' : '      ' )
                )
            );
        }

        $ebnf .= "\n";
        $ebnf .= "Text          ::= '  ' TextLine Text?\n";
        $ebnf .= "BugNr         ::= ' #' [1-9]+[0-9]*\n";
        $ebnf .= "TextLine      ::= [\\x20-\\x7E]+ \"\\n\"\n";

        return $ebnf;
    }

    /**
     * Validate the current check
     *
     * Validate the check on the specified repository. Returns an array of 
     * found issues.
     * 
     * @param pchRepository $repository 
     * @return void
     */
    public function validate( pchRepository $repository )
    {
        $process = $repository->buildSvnLookCommand( 'log' );
        $process->execute();
        return $this->parse( $process->stdoutOutput );
    }
}

