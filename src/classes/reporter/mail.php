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
class pchMailReporter extends pchTextReporter
{
    /**
     * Sender of the mail
     * 
     * @var string
     */
    protected $sender;

    /**
     * Receiver of the mail
     * 
     * @var mixed
     */
    protected $receiver;

    /**
     * Subject of the mail
     * 
     * @var string
     */
    protected $subject;

    /**
     * Construct mail reporter
     * 
     * Construct the mail reporter from the sender of the mail, the receiver
     * and subject.
     *
     * In all values simple values are replaced, like {user} is replaced by the
     * SVN user name.
     * 
     * @param string $sender 
     * @param string $receiver 
     * @param string $subject 
     * @return void
     */
    public function __construct( $sender, $receiver, $subject )
    {
        $this->sender   = $sender;
        $this->receiver = $receiver;
        $this->subject  = $subject;
    }

    /**
     * Report occured issues
     *
     * Report occured issues, passed as an array to the command line. Will exit 
     * with a non-zero exit code if any "errors" occured, and with a zero exit 
     * code, of no issues occured.
     *
     * Will always abort script execution.
     * 
     * @param pchRepository $repository 
     * @param array $issues
     * @return void
     */
    public function report( pchRepository $repository, array $issues ) 
    {
        if ( !count( $issues ) )
        {
            return;
        }

        mail(
            $this->receiver,
            $this->subject,
            $this->getTextReport( $issues ),
            "From: {$this->sender}\r\n"
        );
    }
}

