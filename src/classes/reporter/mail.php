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
 * Reporter 
 * 
 * @package php-commit-hooks
 * @version $Revision$
 * @license http://www.opensource.org/licenses/bsd-license.html New BSD license
 */
class pchMailReporter extends pchReporter
{
    /**
     * Configured mail sender
     * 
     * @var string
     */
    protected $sender;

    /**
     * Configured mail receiver
     * 
     * @var string
     */
    protected $receiver;

    /**
     * Configured mail subject
     * 
     * @var string
     */
    protected $subject;

    /**
     * Construct mail reporter from confuguration.
     *
     * Construct mail reporter from mail sending configuration. It receives 
     * three parameters, while in each simple patterns will be replaced during 
     * sending. The parameters are the mail sender, the mail receiver and the 
     * mail subject.
     *
     * The following strings inside the parameters will be replaced:
     * - {user} with the name of the comitter.
     * 
     * @param string $sender 
     * @param string $receiver 
     * @param string $subject 
     * @return void
     */
    public function __construct( $sender, $receiver, $subject )
    {
        $this->sender   = (string) $sender;
        $this->receiver = (string) $receiver;
        $this->subject  = (string) $subject;
    }

    /**
     * Report occured issues
     *
     * Will send a mail with the issues to report to the user specified in the 
     * constructor. Relies on a working PHP mail() function.
     * 
     * @param array $issues
     * @return void
     */
    public function report( array $issues ) 
    {
        if ( !count( $issues ) )
        {
            return;
        }

        // @TODO: Mail issues
    }
}

