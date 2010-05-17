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
 * Abstract base reporter for text based reporting output
 * 
 * @package php-commit-hooks
 * @version $Revision$
 * @license http://www.opensource.org/licenses/bsd-license.html New BSD license
 */
abstract class pchTextReporter extends pchReporter
{
    /**
     * Mapping of error codes to names
     * 
     * @var array
     */
    protected $mapping = array(
        E_ERROR   => 'Error',
        E_WARNING => 'Warning',
        E_NOTICE  => 'Notice',
        E_STRICT  => 'Strict error',
    );

    /**
     * Get a text representation of the issues.
     *
     * Returns a text reporting all occured issues ordered by the files they 
     * occured in.
     * 
     * @param array $issues
     * @return string
     */
    protected function getTextReport( array $issues ) 
    {
        $return = '';

        // Group issues by affected files
        $files = array();
        foreach ( $issues as $issue )
        {
            if ( isset( $files[$issue->file] ) )
            {
                $files[$issue->file][] = $issue;
            }
            else
            {
                $files[$issue->file] = array( $issue );
            }
        }

        // Output results to STDOUT
        foreach ( $files as $file => $issues )
        {
            if ( $file )
            {
                $return .= sprintf( "%s\n%s\n\n",
                    $file,
                    str_repeat( '=', strlen( $file ) )
                );
            }

            foreach ( $issues as $issue )
            {
                $return .= sprintf( "- %s%s: %s\n",
                    ( $issue->line === null ? '' : "Line {$issue->line}: " ),
                    $this->mapping[$issue->type],
                    $issue->message
                );
            }

            $return .= "\n";
        }

        return $return;
    }
}

