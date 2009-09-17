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
 * Check for required keywords
 *
 * Implements a check for required keywords on the modified or added files.
 * 
 * @package php-commit-hooks
 * @version $Revision$
 * @license http://www.opensource.org/licenses/bsd-license.html New BSD license
 */
class pchSvnKeywordsCheck extends pchCheck
{
    /**
     * Required keyword per file type.
     *
     * For each file type an array of keywords can be configured, each 
     * associated with a list of contained texts in the property with the name 
     * of the keyword.
     * 
     * @var array
     */
    protected $keywords = array(
        'php' => array(
            'svn:keywords' => array(
                'Revision',
            ),
        ),
    );

    /**
     * Construct from optional keywords configuration
     * 
     * @param array $keywords 
     * @return void
     */
    public function __construct( array $keywords = null )
    {
        if ( $keywords !== null )
        {
            $this->keywords = $keywords;
        }
    }

    /**
     * Check keywords for a single file
     *
     * Check the keywords, as specified in the configuration, for one single 
     * file.
     * 
     * @param pchRepository $repository 
     * @param string $file 
     * @return array
     */
    public function checkKeywords( pchRepository $repository, $file )
    {
        $issues = array();
        $type   = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
        if ( isset( $this->keywords[$type] ) )
        {
            foreach ( $this->keywords[$type] as $property => $values )
            {
                $command = $repository->buildSvnLookCommand( 'propget' );
                $command->argument( $property )->argument( $file );
                $command->execute();

                foreach ( $values as $value )
                {
                    if ( strpos( $command->stdoutOutput, $value ) === false )
                    {
                        $issues[] = new pchIssue( E_WARNING, $file, null, "Missing value '$value' for property '$property'." );
                    }
                }
            }
        }

        return $issues;
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
        $process = $repository->buildSvnLookCommand( 'changed' );
        $process->execute();
        $files   = preg_split( '(\r\n|\r|\n)', trim( $process->stdoutOutput ) );

        $issues = array();
        foreach ( $files as $file )
        {
            if ( !preg_match( '(^[AM]\s+(?P<filename>.*)$)', $file, $match ) )
            {
                continue;
            }

            $issues = array_merge(
                $issues,
                $this->checkKeywords( $repository, $match['filename'] )
            );
        }

        return $issues;
    }
}

