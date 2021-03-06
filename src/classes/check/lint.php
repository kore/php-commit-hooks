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
class pchLintCheck extends pchCheck
{
    /**
     * Configured linters to check source files for validity
     * 
     * @var array
     */
    protected $linters = array();

    /**
     * Construct from linter configuration
     * 
     * @param array $linters 
     * @return void
     */
    public function __construct( array $linters = null )
    {
        if ( $linters === null )
        {
            $this->linters = array(
                'php' => new pchPhpLintCheck(),
            );
        }
        else
        {
            $this->linters = $linters;
        }
    }

    /**
     * Lint single file
     *
     * If issues with the passed file are found the function will return an 
     * array with the found issues, and an empty array otherwise.
     * 
     * @param pchRepository $repository 
     * @param string $file 
     * @return array
     */
    public function lint( pchRepository $repository, $file )
    {
        $type = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
        if ( isset( $this->linters[$type] ) )
        {
            return $this->linters[$type]->lint( $file, $this->getFileContents( $repository, $file ) );
        }

        return array();
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
        $issues = array();
        foreach ( $this->getChangedFiles( $repository ) as $file )
        {
            $issues = array_merge(
                $issues,
                $this->lint( $repository, $file )
            );
        }

        return $issues;
    }
}

