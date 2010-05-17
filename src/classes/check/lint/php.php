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
 * Check for valid files
 *
 * Implements linter checks for all added or modified files.
 * 
 * @package php-commit-hooks
 * @version $Revision$
 * @license http://www.opensource.org/licenses/bsd-license.html New BSD license
 */
class pchPhpLintCheck extends pchLintCheckImplementation
{
    /**
     * Lint file contents
     *
     * If issues with the passed file are found the function will return an 
     * array with the found issues, and an empty array otherwise.
     * 
     * @param string $file 
     * @param string $contents 
     * @return array
     */
    public function lint( $file, $contents )
    {
        $check = new pbsSystemProcess( '/usr/bin/env' );
        $check->argument( 'php' )->argument( '-l' );
        
        // Run process asynchronously to pipe file contents into it
        $pipes = $check->execute( true );
        fwrite( $pipes[0], stream_get_contents( $contents ) );
        fclose( $pipes[0] );

        $output = stream_get_contents( $pipes[1] );
        $errors = stream_get_contents( $pipes[2] );
        
        if ( $check->close() )
        {
            // An error occured, transform return contents except for the last 
            // line into an issue object
            $message = implode( "\n", array_slice( preg_split( '(\r\n|\r|\n)', trim( $output ) ), 0, -1 ) );
            return array(
                new pchIssue( E_ERROR, $file, null, $message ),
            );
        }

        return array();
    }
}

