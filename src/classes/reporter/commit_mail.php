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
 * Mail reporter 
 * 
 * @package php-commit-hooks
 * @version $Revision$
 * @license http://www.opensource.org/licenses/bsd-license.html New BSD license
 */
class pchCommitMailReporter extends pchMailReporter
{
    /**
     * Report occured issues
     *
     * Report occured issues, passed as an array.
     * 
     * @param pchRepository $repository 
     * @param array $issues
     * @return void
     */
    public function report( pchRepository $repository, array $issues ) 
    {
        mail(
            $this->replacePlaceholders( $this->receiver, $repository ),
            $this->subject . " r{$repository->version} - " . $this->getBasePath( $repository ),
            "Author:   {$repository->author}\n" . 
            "Date:     {$repository->date}\n" . 
            "Revision: {$repository->version}\n\n" . 
            "Log:\n\n{$repository->log}\n\n" . 
            "Modified:\n{$repository->changed}\n\n" . 
            $repository->diff,
            "From: " . $this->replacePlaceholders( $this->sender, $repository ) . "\r\n"
        );
    }

    /**
     * Get base path from changes
     * 
     * @param pchRepository $repository 
     * @return void
     */
    protected function getBasePath( pchRepository $repository )
    {
        $changed = $repository->{'dirs-changed'};
        $dirs    = array_map(
            function ( $dir )
            {
                $dir = $dir[0] === '/' ? $dir : '/' . $dir;
                return trim( $dir );
            },
            preg_split( '(\r\n|\r|\n)', $changed )
        );

        $common = array_shift( $dirs );
        foreach ( $dirs as $dir )
        {
            $c = 0;
            $maxLength = min( strlen( $common ), strlen( $dir ) );
            while ( ( $c < $maxLength ) &&
                    ( $common[$c] === $dir[$c] ) )
            {
                ++$c;
            }

            $common = substr( $common, 0, $c );
        }

        return $common;
    }
}

